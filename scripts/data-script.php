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

twoTier($elections, $dataRoot, $outDir);

// wards -> candidates 
function twoTier($elections, $dataRoot, $dir)
{
    foreach ($elections as $election)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            $wards = array();
            $wardIDs = array();   //used to keep track of which wards have been added
            $candURL = $dataRoot.$election.".csv";
            $arrCand = array_map('str_getcsv', explode(PHP_EOL, getData($candURL)));
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
                    }
                    else
                    {
                        array_push($wards[$key]['candidates'], $candidate);			   
                    }
                }
            }
            writeJSON($wards, $dir . $election . ".json");
        }
    }
}

// output the data as a JSON file
function writeJSON($wards, $my_file)
{
  $dc = new DemoClub_Wards();
  $dc->wards = $wards;
  $json = json_encode($dc);
  $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
  fwrite($handle, $json);
  fclose($handle);
}


// councils - > wards -> candidates (stalled development)
function threeTier($elections, $dataRoot, $my_file)
{
  $councils = array();
  foreach ($elections as $election)
  {
     if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	 {
       $council = array('slug' => $matches[1], 'election' => $election, 'wards' => array());
  
       $candURL = $dataRoot.$election.".csv";
       $arrCand = array_map('str_getcsv', explode(PHP_EOL, getData($candURL)));
       $header = array_shift($arrCand);
       array_walk($arrCand, '_combine_array', $header);
  
  		 foreach ($arrCand as $candidate)
  		 {
  		   $post_id = $candidate['post_id'];
  			 if (!empty($post_id))
  			 {
    			 $post_label = $candidate['post_label'];
    		   unset ($candidate['election']);
  	  		 unset ($candidate['post_id']);
  		     unset ($candidate['post_label']);
  		     if (array_key_exists($post_id, $council['wards']))
  			   {
             array_push($council['wards'][$post_id]['candidates'], $candidate);			   
  			   }
  			   else
  			   {
  			 	   $council['wards'][$post_id] = array('post_label' => $post_label, 'candidates' => array($candidate));
  			   }
  			 }
  		 }
  		}
      $councils[] = $council;
  }
	$dc = new DemoClub_Councils();
	$dc->Councils = $councils;
  $json = json_encode($dc);
  $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
  fwrite($handle, $json);
  fclose($handle);
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
  $row = array_combine($header, $row);
}

function getData($csvURL)
{
        $ch = curl_init($csvURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
        curl_close($ch);

        return($data);
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

?>