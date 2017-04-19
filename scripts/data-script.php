<?php

// script to pull data from Democracy Club csv and create JSONs

$elections = array(
"local.aberdeen-city.2017-05-04" => "Aberdeen City",
"local.aberdeenshire.2017-05-04"=> "Aberdeenshire",
"local.angus.2017-05-04" => "Angus",
"local.argyll-and-bute.2017-05-04" => "Argyll and Bute",
"local.city-of-edinburgh.2017-05-04" => "City of Edinburgh",
"local.clackmannanshire.2017-05-04" => "Clackmannanshire",
"local.dumfries-and-galloway.2017-05-04" => "Dumfries and Galloway",
"local.dundee-city.2017-05-04" => "Dundee City",
"local.east-ayrshire.2017-05-04" => "East Ayrshire",
"local.east-dunbartonshire.2017-05-04" => "East Dunbartonshire",
"local.east-lothian.2017-05-04" => "East Lothian",
"local.east-renfrewshire.2017-05-04" => "East Renfrewshire",
"local.falkirk.2017-05-04" => "Falkirk",
"local.fife.2017-05-04" => "Fife",
"local.glasgow-city.2017-05-04" => "Glasgow City",
"local.highland.2017-05-04" => "Highland",
"local.inverclyde.2017-05-04" => "Inverclyde",
"local.midlothian.2017-05-04" => "Midlothian",
"local.moray.2017-05-04" => "Moray",
"local.eilean-siar.2017-05-04" => "Na h-Eileanan an Iar",
"local.north-ayrshire.2017-05-04" => "North Ayrshire",
"local.north-lanarkshire.2017-05-04" => "North Lanarkshire",
"local.orkney-islands.2017-05-04" => "Orkney Islands",
"local.perth-and-kinross.2017-05-04" => "Perth and Kinross",
"local.renfrewshire.2017-05-04" => "Renfrewshire",
"local.the-scottish-borders.2017-05-04" => "The Scottish Borders",
"local.shetland-islands.2017-05-04" => "Shetland Islands",
"local.south-ayrshire.2017-05-04" => "South Ayrshire",
"local.south-lanarkshire.2017-05-04" => "South Lanarkshire",
"local.stirling.2017-05-04" => "Stirling",
"local.west-dunbartonshire.2017-05-04" => "West Dunbartonshire",
"local.west-lothian.2017-05-04" => "West Lothian");

// this array of party abbreviations mirrors the classes in parties.css
// used in the jstree data to prefix each candidate and set the icon class
$party_prefix = array(
"Independent" =>"(Ind)",
"Independent-Network" =>"(Ind)",
"Independent-Alliance-North-Lanarkshire" =>"(Ind)",
"Scottish-Green-Party" =>"(Green)",
"Orkney-Manifesto-Group" =>"(OMG)",
"Trade-Unionist-and-Socialist-Coalition" =>"(TUSC)",
"Liberal-Democrats" =>"(LD)",
"Labour-Party" =>"(Lab)",
"Labour-and-Co-operative-Party" =>"(Lab)",
"Conservative-and-Unionist-Party" =>"(Con)",
"Scottish-National-Party-SNP" =>"(SNP)",
"Scottish-Socialist-Party" =>"(SSP)",
"UK-Independence-Party-UKIP" =>"(UKIP)",
"Scottish-Libertarian-Party" =>"(SLP)",
"Solidarity---Scotlands-Socialist-Movement" =>"(Solidarity)",
"National-Front" =>"(NF)",
"West-Dunbartonshire-Community-Party" =>"(WDCP)",
"RISE---Respect-Independence-Socialism-and-Environmentalism" =>"(RISE)");


$dataRoot = "https://candidates.democracyclub.org.uk/media/candidates-";
$outDir = "../2017/SCO/";

buildData(array_keys($elections), $dataRoot, $outDir);
buildPtree($elections, $outDir, $party_prefix);
buildCtree(array_keys($elections), $outDir, $party_prefix);
//boundaryWards(array_keys($elections), $outDir, "boundary-wardinfo.csv");

//build JSON data for the jstree with Parties as the children of the root using wardinfo and the candidate JSON for each council
function buildPTree($elections, $dataDir, $party_prefix)
{
    $parties = array();
    $councils = array();
    $wards = array();
    $cwards = array();
    $id = 0;
    $ctotal = 0;
    $root = new jstree_node(++$id,"root","All Parties");

    // convert the candidate data to tree nodes indexed by cand_ward_code (post_id)
    foreach ($elections as $election => $council)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            $cdata = readJSON($dataDir. $election . ".json");
            foreach ($cdata->wards as $ward)
            {
                if (!empty($ward->post_id))
                {
                    echo "PARTIES candidate data " . $election . " " . $ward->post_id . "<br>\n";
                    $node = new jstree_node(++$id, "ward", $ward->post_label);
                    foreach ($ward->candidates as $candidate)
                    {
                        // create or update the party node
                        if (array_key_exists($candidate->party_name, $parties))
                        {
                            if (array_key_exists($candidate->party_name . $election, $councils))
                            {
                                if (array_key_exists($candidate->party_name . $election . $ward->post_label, $wards))
                                {
                                    $ward_node = $wards[$candidate->party_name . $election . $ward->post_label];
                                    $party_node = $parties[$candidate->party_name];
                                    $party_node->no_candidates += 1;
                                }
                                else
                                {
                                    $council_node = $councils[$candidate->party_name . $election];
                                    $party_node = $parties[$candidate->party_name];
                                    $party_node->no_candidates += 1;
                                    $ward_node = new jstree_node(++$id,"ward",$ward->post_label);
                                    $wards[$candidate->party_name . $election . $ward->post_label] = $ward_node;
                                    $council_node->children[] = $ward_node;
                                }
                            }
                            else
                            {
                                $party_node = $parties[$candidate->party_name];
                                $party_node->no_candidates += 1;
                                $council_node = new jstree_node(++$id,"council",$council);
                                $councils[$candidate->party_name . $election] = $council_node;
                                $party_node->children[] = $council_node;
                                $ward_node = new jstree_node(++$id,"ward",$ward->post_label);
                                $wards[$candidate->party_name . $election . $ward->post_label] = $ward_node;
                                $council_node->children[] = $ward_node;
                            }
                        }
                        else
                        {
                            $party_node = new jstree_node(++$id,"party", $candidate->party_name);
                            $party_node->no_candidates = 1;

                            $parties[$candidate->party_name] = $party_node;
                            $root->children[] = $party_node;
                            $council_node = new jstree_node(++$id,"council",$council);
                            $councils[$candidate->party_name . $election] = $council_node;
                            $party_node->children[] = $council_node;
                            $ward_node = new jstree_node(++$id,"ward",$ward->post_label);
                            $wards[$candidate->party_name . $election . $ward->post_label] = $ward_node;
                            $council_node->children[] = $ward_node;
                        }
                        $root->no_candidates += 1;
                        $cand_node = new jstree_node(++$id,"candidate",$candidate->name);
                        $cand_node->properties = $candidate;
                        $ward_node->children[] = $cand_node;
                    }
                }
            }
        }
    }
    $root->sortbycandidate();
    $root->listChildren();
    foreach ($root->children as $party_node)
    {
        $party = stripParty($party_node->text);
        $party_node->icon = $party;        // icon property in jstree types plugin is interpreted as a class if it does not contain /
        $prefix = (array_key_exists($party, $party_prefix)) ? " " . $party_prefix[$party] . " " : " ";
        $party_node->text = $prefix . $party_node->text;
    }
    extendParties($root);
    writeJSON($root, $dataDir . "party-tree.json");
}


//build JSON data for the jstree library using wardinfo and the candidate JSON for each council
function buildCTree($elections, $dataDir, $party_prefix)
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
                    $node->children = convertCandidates ($ward->candidates, $id, $party_prefix);
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

// quick fix to show counts in party tree (recursive)
function extendParties($node)
{
    if (count($node->children))
    {
        switch ($node->type)
        {
            case "root":
                $node->text .= " (" . $node->no_candidates . " candidate(s) from " . count($node->children). " political parties)";
                break;
            case "party":
                $node->text .= " (" . $node->no_candidates . " candidate(s) contesting " . $node->countType("ward") . " ward(s) across " . count($node->children) . " council(s))";
                break;
        }
        foreach ($node->children as $child)
        {
            extendParties($child);
        }
    }
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
function convertCandidates($candidates, $last_id, $party_prefix)
{
    $nodes = array();
    foreach ($candidates as $c)
    {
        $party = stripParty($c->party_name);
        $prefix = (array_key_exists($party, $party_prefix)) ? " " . $party_prefix[$party] . " " : " ";
        echo "$prefix ";
        $node = new jstree_node(++$last_id, "candidate", $prefix . $c->name);
        $node->icon = $party;        // icon property in jstree types plugin is interpreted as a class if it does not contain /
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

// replace and remove certain characters from a party name to be consistent with the CSS form used in script.js
// party_name.replace(/\s+/g, "-").replace(/[\',()]/g,"")
function stripParty($name)
{
    $pattern = array('/\s+/', "/[',()]/");
    $replacement = array('-', '');
    return( preg_replace($pattern, $replacement, $name));
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

    private $count;

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

    // recursively count nodes of a given type
    function countType($type)
    {
        $this->count = 0;
        if ($this->type == $type)
        {
            $this->count = 1;
        }
        foreach ($this->children as $child)
        {
            $this->count += $child->countType($type);
        }
        return ($this->count);
    }

    function sortbytext()
    {
        $tmp = $this->children;
        usort($tmp, array($this, "cmp"));
        $this->children = $tmp;
    }

    function cmptext($a, $b)
    {
        if ($a->text == $b->text) {
            return 0;
        }
        return ($a->text < $b->text) ? -1 : 1;
    }

    // descending by no_candidates
    function sortbycandidate()
    {
        $tmp = $this->children;
        usort($tmp, array($this, "cmpcandidate"));
        $this->children = $tmp;
    }

    function cmpcandidate($a, $b)
    {
        if ($a->no_candidates == $b->no_candidates)
        {
            if ($a->text == $b->text) {
                return 0;
            }
            return ($a->text < $b->text) ? -1 : 1;
        }
        return ($a->no_candidates < $b->no_candidates) ? 1 : -1;
    }

    function listChildren()
    {
        foreach ($this->children as $child)
        {
            echo $child->text . "<br>\n";
        }
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