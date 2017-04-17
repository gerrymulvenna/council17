<?php

// script to pull data from Democracy Club csv and create JSONs

$elections = array(
"local.aberdeen-city.2017-05-04",
"local.aberdeenshire.2017-05-04",
"local.angus.2017-05-04",
"local.argyll-and-bute.2017-05-04",
"local.city-of-edinburgh.2017-05-04",
"local.clackmannanshire.2017-05-04",
"local.dumfries-and-galloway.2017-05-04",
"local.dundee-city.2017-05-04",
"local.east-ayrshire.2017-05-04",
"local.east-dunbartonshire.2017-05-04",
"local.east-lothian.2017-05-04",
"local.east-renfrewshire.2017-05-04",
"local.falkirk.2017-05-04",
"local.fife.2017-05-04",
"local.glasgow-city.2017-05-04",
"local.highland.2017-05-04",
"local.inverclyde.2017-05-04",
"local.midlothian.2017-05-04",
"local.moray.2017-05-04",
"local.eilean-siar.2017-05-04",
"local.north-ayrshire.2017-05-04",
"local.north-lanarkshire.2017-05-04",
"local.orkney-islands.2017-05-04",
"local.perth-and-kinross.2017-05-04",
"local.renfrewshire.2017-05-04",
"local.the-scottish-borders.2017-05-04",
"local.shetland-islands.2017-05-04",
"local.south-ayrshire.2017-05-04",
"local.south-lanarkshire.2017-05-04",
"local.stirling.2017-05-04",
"local.west-dunbartonshire.2017-05-04",
"local.west-lothian.2017-05-04");

$dataRoot = "https://candidates.democracyclub.org.uk/media/candidates-";
$outDir = "../2017/SCO/";

buildTrees($elections, $outDir);

//buildData($elections, $dataRoot, $outDir);
//boundaryWards($elections, $outDir, "boundary-wardinfo.csv");

//build JSON data for the jstree library using wardinfo and the candidate JSON for each council
function buildTrees($elections, $dataDir)
{
    $councils = array();
    $wards = array();
    $cwards = array();
    $id = 0;
    $ctotal = 0;

    // convert the candidate data to tree nodes indexed by cand_ward_code (post_id)
    foreach ($elections as $election)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            $cdata = readJSON($dataDir. $election . ".json");
            foreach ($cdata->wards as $ward)
            {
                if (!empty($ward->post_id))
                {
                    echo "Candidate data " . $election . " " . $ward->post_id . "<br>\n";
                    $node = new jstree_node(++$id, "ward", $ward->post_label);
                    $node->no_candidates = count($ward->candidates);
                    $node->children = convertCandidates ($ward->candidates, $id);
                    $id += count($node->children);
                    $ctotal += count($node->children);    // keep track of the total candidates
                    $cwards[$ward->post_id] = $node;
                }
            }
        }
    }

    // convert the wardinfo data to tree nodes and build the tree structure incorporating the candidate nodes
    $root = new jstree_node(++$id,"root","Scottish Councils");
    $root->no_candidates = $ctotal;
    $wardinfo = readJSON($dataDir . "wardinfo.json");
    foreach ($wardinfo->Wards as $ward)
    {
        if (!empty($ward->election))
        {
            echo "Ward data " . $ward->election . " " . $ward->ward_name . "<br>\n";
            // create or update the council node
            if (array_key_exists($ward->election, $councils))
            {
                $root->no_seats += $ward->seats + 0;
            }
            else
            {
                $root->no_seats += $ward->seats + 0;
                $node = new jstree_node(++$id,"council",$ward->council);
                $councils[$ward->election] = $node;
                $root->children[] = $node;
            }
            // add the ward node
            $ward_node = new jstree_node(++$id, "ward", $ward->ward_name);
            $ward_node->no_seats = $ward->seats;
            $ward_node->properties["map_ward_code"] = $ward->map_ward_code;
            $ward_node->properties["cand_ward_code"] = $ward->cand_ward_code;
            $ward_node->properties["ward_no"] = $ward->ward_no;
            $ward_node->children = $cwards[$ward->cand_ward_code]->children;
            $ward_node->no_candidates = $cwards[$ward->cand_ward_code]->no_candidates;
            $node->children[] = $ward_node;
            $node->no_candidates += $ward_node->no_candidates;
            $node->no_seats += $ward->seats + 0;
        }
    }
    extendNames($root);
    writeJSON($root, $dataDir . "council-tree.json");
}

// quick fix to show seats and candidates for parent nodes (recursive)
function extendNames($node)
{
    if (count($node->children))
    {
        $node->text .= " (" . $node->no_seats . " seats, " . $node->no_candidates . " candidates)";
        foreach ($node->children as $child)
        {
            extendNames($child);
        }
    }
}

// convert an array of candidates to an array of jstree nodes
function convertCandidates($candidates, $last_id)
{
    $nodes = array();
    foreach ($candidates as $c)
    {
        $node = new jstree_node(++$last_id, "candidate", $c->name);
        $node->no_candidates = 1;
        $node->properties = $c;
        $nodes[] = $node;
    }
    return($nodes);
}



// wards -> candidates 
function buildData($elections, $dataRoot, $dir)
{
    $wardinfo = array();
    foreach ($elections as $election)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            $wards = array();
            $wardIDs = array();   //used to keep track of which wards have been added
            $candURL = $dataRoot.$election.".csv";
            $arrCand = getData($candURL, $election);
            echo "$election<br>";
            for ($i = 1; $i < count($arrCand); $i++)
            {
                if (count($arrCand[$i]) <= 1)
                {
                    unset ($arrCand[$i]);
                }
            }

            $header = array_shift($arrCand);
            array_walk($arrCand, '_combine_array', $header);
  
            $node = new jstree_node(++$id, $matches[1]);
            foreach ($arrCand as $candidate)
            {
                // fudge to get surname using part after last space
                $names = splitName($candidate['name']);
                if (!empty($names))
                {
                    $candidate = array_merge($candidate, $names);
                }
                $post_id = $candidate['post_id'];
                if (!empty($post_id))
                {
                    $post_label = $candidate['post_label'];
                    unset ($candidate['election']);
                    unset ($candidate['post_id']);
                    unset ($candidate['post_label']);
					$key = array_search($post_id, $wardIDs);
                    if ($key === False)
                    {
                        $wardIDs[] = $post_id;
                        $wards[] = array('post_id' => $post_id, 'post_label' => $post_label, 'election' => $election, 'candidates' => array($candidate));
                        $wardinfo[] = array('post_id' => $post_id, 'post_label' => $post_label, 'election' => $matches[1]);
                    }
                    else
                    {
                        array_push($wards[$key]['candidates'], $candidate);			   
                    }
                }
            }
            $dc = new DemoClub_Wards();
            $dc->wards = $wards;
            writeJSON($dc, $dir . $election . ".json");
        }
    }
    saveCSV($wardinfo, "candidate-wardinfo.csv");
}

// once-off routine to get ward codes from boundary data files
function boundaryWards($elections, $dir, $my_file)
{
    $wardinfo = array();
    foreach ($elections as $election)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            echo "$election<br>";
            $b_file = $dir . 'boundaries/'. $matches[1] . ".geojson";
            $json = file_get_contents($b_file);
            $data = json_decode($json);
            foreach($data->features as $feature)
            {
                $prop = $feature->properties;
                // this is the newer boundary data
                if (isset($prop->Ward_Code))
                {
                    $wardinfo[] = array('council' => $prop->Council, 'ward_no' => $prop->Ward_No, 'post_label' => $prop->Ward_Name, 'post_id' => $prop->Ward_Code);
                }
                // this is the older type
                elseif (isset($prop->CODE))
                {
                    $wardinfo[] = array('council' => $prop->FILE_NAME, 'ward_no' => $prop->Ward_no, 'post_label' => $prop->NAME, 'post_id' => $prop->CODE);
                }
            }
        }
    }
    saveCSV($wardinfo, $my_file);
}
            

function splitName($name)
{
  $ret = array();
  $pos = strrpos($name, " ");
	if ($pos)
	{
  	$surname = substr($name, $pos + 1);
  	$firstname = substr($name, 0, $pos);
		$ret['Surname'] = $surname;
		$ret['firstname'] = $firstname;
	}
	return ($ret);
}

function _combine_array(&$row, $key, $header) {
    if (count($row) > 1 )
    {
      $row = array_combine($header, $row);
    }
}

function getData($csvURL, $election)
{
    $ch = curl_init($csvURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $data = curl_exec($ch);
    curl_close($ch);

    $my_file = $election .".csv";
    $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
    fwrite($handle, $data);
    fclose($handle);

    $arr = array();
    $handle = fopen($my_file, "r");
    while ($row = fgetcsv($handle))
    {
        $arr[] = $row;
    }
    fclose($handle);
    return ($arr);
}

class DemoClub_Councils
{
 public $councils;

 public function __construct()
 {
 	$this->councils = array();
 } 
}

class DemoClub_Wards
{
 public $wards;

 public function __construct()
 {
 	$this->wards = array();
 } 
}

class jstree_node
{
    public $id;
    public $type;
    public $text;
    public $no_seats;
    public $no_candidates;
    public $properties;
    public $children;

    function __construct($id, $type, $text, $properties = array())
    {
        $this->id = $id;
        $this->type = $type;
        $this->text = $text;
        $this->no_seats = 0;
        $this->no_candidates = 0;
        $this->properties = $properties;
        $this->children = array();
    }
}


// fgetcsv() is more resilient than str_getcsv when fields contain EOL characters
function getCSV($my_file)
{
    $arr = array();
    $handle = fopen($my_file, "r");
    while ($row = fgetcsv($handle))
    {
        $arr[] = $row;
    }
    fclose($handle);
    return ($arr);
}

//write an array to CSV (assumes two-dimensional array with headers in first row)
function saveCSV($arr, $my_file)
{
    $header = array_keys($arr[0]);
    $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
    fputcsv($handle, $header);
    foreach ($arr as $row)
    {
        fputcsv($handle, $row);
    }
    fclose($handle);
}

// output the data as a JSON file
function writeJSON($data, $my_file)
{
  $json = json_encode($data);
  $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
  fwrite($handle, $json);
  fclose($handle);
}

// input data from a JSON file
function readJSON($my_file)
{
    $handle = fopen($my_file, 'r') or die('Cannot open file:  '.$my_file);
    $json = fread($handle, filesize($my_file));
    fclose($handle);
    $data = json_decode($json);
    return ($data);
}

?>