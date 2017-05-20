<?php

// avoid any scripting on public-facing website to avoid trashing any data files
if ($_SERVER['SERVER_ADDR'] == "216.92.68.138")
{
    header("Location: /"); /* Redirect browser */
    exit();
}

// array of candidate IDs in the 3 uncontested wards
$elected_without_contest = array("21540", "21541", "21542", "21375", "8755", "21376", "20966", "20967", "20968");

// blank template for the CSV data file
$blank_row = array(
"id" => "",
"name" => "",
"party_name" =>"",
"council_id" => "",
"council_name" => "",
"election" => "",
"cand_ward_id" => "",
"map_ward_id" => "",
"ward_name" => "",
"contested" => "",
"elected" => "",
"status" => "",
"occurred_on_count" => "",
"first_prefs" => "",
"transfers02"=>"",
"total_votes02"=>"",
"transfers03"=>"",
"total_votes03"=>"",
"transfers04"=>"",
"total_votes04"=>"",
"transfers05"=>"",
"total_votes05"=>"",
"transfers06"=>"",
"total_votes06"=>"",
"transfers07"=>"",
"total_votes07"=>"",
"transfers08"=>"",
"total_votes08"=>"",
"transfers09"=>"",
"total_votes09"=>"",
"transfers10"=>"",
"total_votes10"=>"",
"transfers11"=>"",
"total_votes11"=>"",
"transfers12"=>"",
"total_votes12"=>"",
"electorate"=>"",
"total_poll"=>"",
"valid_poll"=>"",
"rejected"=>"",
"quota"=>"",
"seats"=>"",
"candidates"=>""
);

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
"Christian-Party-Proclaiming-Christs-Lordship" => "CPPCL",
"Christian-Party-" => "CPPCL",
"Socialist-Labour-Party" => "SocLab",
"Social-Democratic-Party" => "SDP",
"A-Better-Britain-_-Unionist-Party" => "ABBUP",
"Scottish-Unionist-Party" => "SUP",
"The-Rubbish-Party" => "RP",
"Independent" =>"Ind",
"Independent-Network" =>"IndNet",
"Independent-Alliance-North-Lanarkshire" =>"IANL",
"Scottish-Green-Party" =>"Green",
"Orkney-Manifesto-Group" =>"OMG",
"Trade-Unionist-and-Socialist-Coalition" =>"TUSC",
"Liberal-Democrats" =>"LD",
"Labour-Party" =>"Lab",
"Labour-and-Co-operative-Party" =>"LabCo",
"Conservative-and-Unionist-Party" =>"Con",
"Scottish-National-Party-SNP" =>"SNP",
"Scottish-Socialist-Party" =>"SSP",
"UK-Independence-Party-UKIP" =>"UKIP",
"Scottish-Libertarian-Party" =>"SLP",
"Solidarity---Scotlands-Socialist-Movement" =>"Solidarity",
"National-Front" =>"NF",
"West-Dunbartonshire-Community-Party" =>"WDCP",
"RISE---Respect-Independence-Socialism-and-Environmentalism" =>"RISE",
"Not-transferred" => "N/T");

// this array of party abbreviations mirrors the classes in parties.css
// used in the jstree data to prefix each candidate and set the icon class
$party_colors = array(
"Christian-Party-Proclaiming-Christs-Lordship" => "#3B0C72",
"Christian-Party-" => "#3B0C72",
"Socialist-Labour-Party" => "#ff0000",
"Social-Democratic-Party" => "#ff0000",
"A-Better-Britain-_-Unionist-Party" => "#2E4F98",
"Scottish-Unionist-Party" => "#034AA6",
"The-Rubbish-Party" => "#7ec0ee",
"Independent" =>"#B0BEC5",
"Independent-Network" =>"#B0BEC5",
"Independent-Alliance-North-Lanarkshire" =>"#B0BEC5",
"Scottish-Green-Party" =>"#43B02A",
"Orkney-Manifesto-Group" =>"#008083",
"Trade-Unionist-and-Socialist-Coalition" =>"#E5327E",
"Liberal-Democrats" =>"#F2B027",
"Labour-Party" =>"#cb2710",
"Labour-and-Co-operative-Party" =>"#cb2710",
"Conservative-and-Unionist-Party" =>"#00386B",
"Scottish-National-Party-SNP" =>"#fef48b",
"Scottish-Socialist-Party" =>"#DE3136",
"UK-Independence-Party-UKIP" =>"112, 48, 160",
"Scottish-Libertarian-Party" =>"0, 97, 167",
"Solidarity---Scotlands-Socialist-Movement" =>"#054a24",
"National-Front" =>"#B4716B",
"West-Dunbartonshire-Community-Party" =>"#ED1A23",
"RISE---Respect-Independence-Socialism-and-Environmentalism" =>"#F25D25",
"Not-transferred" => "#000000");


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

class jstree_state
{
    public $opened;
    public $disabled;
    public $selected;

    function __construct($opened, $disabled, $selected)
    {
        $this->opened = $opened;
        $this->disabled = $disabled;
        $this->selected = $selected;
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
    public $state;

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
        $this->state = new jstree_state(false, false, false);
    }

    function open()
    {
        $this->state->opened = true;
    }

    function close()
    {
        $this->state->opened = false;
    }

    function disable()
    {
        $this->state->disabled = true;
    }

    function enable()
    {
        $this->state->disabled = false;
    }

    function select()
    {
        $this->state->selected = true;
    }

    function deselect()
    {
        $this->state->selected = false;
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

    // descending by no_seats
    function sortbyseats()
    {
        $tmp = $this->children;
        usort($tmp, array($this, "cmpseats"));
        $this->children = $tmp;
    }

    function cmpseats($a, $b)
    {
        if ($a->type == "container") return -1;
        if ($b->type == "container") return 1;
        if ($a->no_seats == $b->no_seats)
        {
            if ($a->text == $b->text) {
                return 0;
            }
            return ($a->text < $b->text) ? -1 : 1;
        }
        return ($a->no_seats < $b->no_seats) ? 1 : -1;
    }

    function listChildren()
    {
        foreach ($this->children as $child)
        {
            echo $child->text . "<br>\n";
        }
    }

    //add a property to a node and its children
    function applyProperty($key, $value)
    {
        if (is_array($this->properties))
        {
            $this->properties[$key] = $value;
        }
        elseif (is_object($this->properties))
        {
            $this->properties->$key = $value;
        }
        foreach ($this->children as $child)
        {
            $child->applyProperty($key, $value);
        }
    }

    //assign a numeric value to a node's property
    function assignProperty($key, $value)
    {
        if (is_array($this->properties))
        {
            $this->properties[$key] = $value + 0;
        }
        elseif (is_object($this->properties))
        {
            $this->properties->$key = $value + 0;
        }
    }

    //increment a node's property
    function incrementProperty($key, $value = 1)
    {
        if (is_array($this->properties))
        {
            if (array_key_exists($key, $this->properties))
            {
                $this->properties[$key] += $value;
            }
            else
            {
                $this->properties[$key] = $value;
            }
        }
        elseif (is_object($this->properties))
        {
            if (property_exists($this->properties, $key))
            {
                $this->properties->$key += $value;
            }
            else
            {
                $this->properties->$key = $value;
            }
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

class Results
{
    public $Constituency;

    function __construct($info)
    {
        $this->Constituency = new Constituency($info->Constituency_Name, $info->Constituency_Number, $info->Number_Of_Seats, $info->Voting_Age_Pop, $info->Total_Electorate, $info->Total_Poll, $info->Valid_Poll);
    }

    // go through the results and mark Elected/Excluded status where appropriate
    public function updateStatus($retain = True)
    {
        $cand_status = array();
        $cand_ids = array();
        $last_stage = 0;
        $no_elected = 0;

        if ($retain == False)  // clear existing status fields
        {
            for ($i = 0; $i < count($this->Constituency->countGroup); $i++)
            {
                $this->Constituency->countGroup[$i]->Status = "";
                $this->Constituency->countGroup[$i]->Occurred_On_Count = "";
            }
        }
        for ($i = 0; $i < count($this->Constituency->countGroup); $i++)
        {
            // get a handle on what the max count_number is
            if ($this->Constituency->countGroup[$i]->Count_Number > $last_stage)
            {
                $last_stage = $this->Constituency->countGroup[$i]->Count_Number;
            }
            // build array of candidate IDs
            if (!in_array($this->Constituency->countGroup[$i]->Candidate_Id, $cand_ids))
            {
                $cand_ids[] = $this->Constituency->countGroup[$i]->Candidate_Id;
            }

            if (empty($this->Constituency->countGroup[$i]->Status))
            {
                if ($this->Constituency->countGroup[$i]->Total_Votes >= $this->Constituency->countInfo->Quota)
                {
                    $this->markStatus('Elected', $this->Constituency->countGroup[$i]->Candidate_Id, $this->Constituency->countGroup[$i]->Count_Number);
                    $cand_status[$this->Constituency->countGroup[$i]->Candidate_Id] = 'Elected';
                }
                elseif ($this->Constituency->countGroup[$i]->Total_Votes == 0 && ($this->Constituency->countGroup[$i]->Transfers < 0))
                {
                    $this->markStatus('Excluded', $this->Constituency->countGroup[$i]->Candidate_Id, $this->Constituency->countGroup[$i]->Count_Number - 1);
                    $cand_status[$this->Constituency->countGroup[$i]->Candidate_Id] = 'Excluded';
                }
            }
            else
            {
                $cand_status[$this->Constituency->countGroup[$i]->Candidate_Id] = $this->Constituency->countGroup[$i]->Status;
            }
        }
        // get no. of elected
        foreach ($cand_status as $key => $value)
        {
            if ($value == 'Elected')
            {
                $no_elected++;
            }
        }
        if ($no_elected < $this->Constituency->countInfo->Number_Of_Seats)
        {
            // if the number of candidates without status is the same and the unmber of unfilled seats, then they must be Elected
            if (count($cand_ids) - count($cand_status) == $this->Constituency->countInfo->Number_Of_Seats - $no_elected)
            {
                foreach ($cand_ids as $id)
                {
                    if (!in_array($id, array_keys($cand_status)))
                    {
                        $this->markStatus("Elected", $id, $last_stage);
                        echo "Last candidate standing marked ELECTED ($id, stage $last_stage) in " . $this->Constituency->countInfo->Constituency_Name . "\n";
                        $no_elected++;
                    }
                }
            }
        }
    }

    // set the Status and Occurred_On_Count properties for a particular $cid in the countGroup data
    function markStatus($status, $cid, $count)
    {
        for ($i = 0; $i < count($this->Constituency->countGroup); $i++)
        {
            if (($this->Constituency->countGroup[$i]->Candidate_Id == $cid) && ($this->Constituency->countGroup[$i]->Count_Number >= $count))
            {
                $this->Constituency->countGroup[$i]->Status = $status;
                $this->Constituency->countGroup[$i]->Occurred_On_Count = $count;
            }
        }
    }

    // use this to convert a stdClass object imported from JSON
    public function set($data)
    {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }

    // returns array (with elements Status, Occurred_On_Count) if any records for a given candidate $cid have Status set
    public function currentStatus($cid)
    {
        for ($i = 0; $i < count($this->Constituency->countGroup); $i++)
        {
            if ($this->Constituency->countGroup[$i]->Candidate_Id == $cid && !empty($this->Constituency->countGroup[$i]->Status))
            {
                return (array("Status" => $this->Constituency->countGroup[$i]->Status, "Occurred_On_Count" => $this->Constituency->countGroup[$i]->Occurred_On_Count));
            }
        }   
        return (false);            
    }
        
}

class Constituency
{
    public $countInfo;
    public $countGroup;

    function __construct($name, $no, $seats, $pop, $electorate, $total, $valid)
    {
        $this->countInfo = new countInfo($name, $no, $seats, $pop, $electorate, $total, $valid);
        $this->countGroup = array();
    }
}

class countInfo
{
    public $Valid_Poll;
    public $Number_Of_Seats;
    public $Total_Poll;
    public $Voting_Age_Pop;
    public $Quota;
    public $Constituency_Name;
    public $Constituency_Number;
    public $Total_Electorate;
    public $Spoiled;

    function __construct($name, $no, $seats, $pop, $electorate, $total, $valid)
    {
        $this->Valid_Poll = $valid;
        $this->Number_Of_Seats = $seats;
        $this->Total_Poll = $total;
        $this->Voting_Age_Pop = $pop;
        $this->Quota = floor($valid/($seats+1))+1;
        $this->Constituency_Name = $name;
        $this->Constituency_Number = $no;
        $this->Total_Electorate = $electorate;
        $this->Spoiled = $total - $valid;
    }
}

class countItem
{
    public $Candidate_First_Pref_Votes;
    public $Status;
    public $Occurred_On_Count;
    public $Surname;
    public $Firstname;
    public $Constituency_Number;
    public $Party_Name;
    public $Candidate_Id;
    public $Count_Number;
    public $Transfers;
    public $id;
    public $Total_Votes;

    function __construct($id, $no, $count, $party, $candID, $fname, $sname, $firstpref, $transfers, $total, $status = "", $occurred = "")
    {
        $this->Candidate_First_Pref_Votes = $firstpref;
        $this->Status = $status;
        $this->Occurred_On_Count = $occurred;
        $this->Surname = $sname;
        $this->Firstname = $fname;
        $this->Constituency_Number = $no;
        $this->Party_Name = $party;
        $this->Candidate_Id = $candID;
        $this->Count_Number = $count;
        $this->Transfers = $transfers;
        $this->id = $id;
        $this->Total_Votes = $total;
    }
}

class Council
{
    public $Constituencies;

    //adds the first summary
    function __construct($name, $no, $code, $info)
    {
        $this->Constituencies[] = new Constituency_Summary ($name, $no, $code, $info);
    }

    // use this to convert a stdClass object imported from JSON
    public function set($data)
    {
        foreach ($data AS $key => $value) $this->{$key} = $value;
    }
}

class Constituency_Summary
{
    public $Constituency_Name;
    public $Constituency_Number;
    public $Directory;
    public $countInfo;

    function __construct($name, $no, $code, $info)
    {
        $this->Constituency_Name = $name;
        $this->Constituency_Number = $no;
        $this->Directory = $code;
        $this->countInfo = $info;
    }

}

function verify_all ($dir, $precision)
{
    echo "<pre>\n";
    $clist = scandir($dir);    // list of council folders
    foreach ($clist as $council)
    {
        if (!in_array($council,array(".","..")))
        {
            if (is_dir($dir  . "/" . $council))
            {
                verify_council($dir, $council, $precision);
            }
        }
    }
    echo "</pre>\n";
}


function verify_council($dir, $council, $precision)
{
    $fname = $dir . "/" . $council . "/all-constituency-info.json";
    if (file_exists($fname))
    {	
        $wlist = scandir($dir . "/" . $council);  // list of ward folders
        foreach ($wlist as $ward)
        {
            if (!in_array($ward,array(".","..")))
            {
                if (is_dir($dir  . "/" . $council . "/" . $ward))
                {
                    verify_ward($dir, $council, $ward, $precision);
                }
            }
        }
    }
}


function verify_ward($dir, $council, $ward, $precision)
{
    $fname = $dir . "/" . $council . "/" . $ward . "/ResultsJson.json";
    if (file_exists($fname))
    {	
        echo "\nVerifying $fname\n";
        $json = readJSON($fname);
        $transfers = array();
        $total_votes = array();
        foreach($json->Constituency->countGroup as $item)
        {
            $transfers[$item->Candidate_Id][$item->Count_Number] = $item->Transfers + 0;
            $total_votes[$item->Candidate_Id][$item->Count_Number] = $item->Total_Votes + 0;
        }
        foreach($total_votes as $id => $votes)
        {
            for ($stage = 2; $stage <= count($votes); $stage++)
            {
                $target = $votes[$stage-1] + $transfers[$id][$stage];
                $diff = abs($votes[$stage] - $target);
                if ($diff > $precision)
                {
                    echo "$council $ward $id $stage " . $votes[$stage-1] . " + " . $transfers[$id][$stage] . " <> " . $votes[$stage] . " target: " . $target . " diff: " . $diff . "\n";
                }
            }
        }
    }
}

function markStatus_all ($dir)
{
    echo "<pre>\n";
    $clist = scandir($dir);    // list of council folders
    foreach ($clist as $council)
    {
        if (!in_array($council,array(".","..")))
        {
            if (is_dir($dir  . "/" . $council))
            {
                markStatus_council($dir, $council);
            }
        }
    }
    echo "</pre>\n";
}


function markStatus_council($dir, $council)
{
    $fname = $dir . "/" . $council . "/all-constituency-info.json";
    if (file_exists($fname))
    {	
        $wlist = scandir($dir . "/" . $council);  // list of ward folders
        foreach ($wlist as $ward)
        {
            if (!in_array($ward,array(".","..")))
            {
                if (is_dir($dir  . "/" . $council . "/" . $ward))
                {
                    markStatus_ward($dir, $council, $ward);
                }
            }
        }
    }
}


function markStatus_ward($dir, $council, $ward)
{
    $fname = $dir . "/" . $council . "/" . $ward . "/ResultsJson.json";
    if (file_exists($fname))
    {	
        echo "\nRefreshing status on $fname\n";
        $json = readJSON($fname);
        $info = new countInfo($json->Constituency->countInfo->Constituency_Name, $json->Constituency->countInfo->Constituency_Number, $json->Constituency->countInfo->Number_Of_Seats, 0, $json->Constituency->countInfo->Total_Electorate, $json->Constituency->countInfo->Total_Poll, $json->Constituency->countInfo->Valid_Poll);
        $rdata = new Results($info);
        $rdata->set($json);
        $rdata->updateStatus(False);
        echo "Writing $fname<br>\n";
        writeJSON($rdata, $fname);
   }
}

// returns an array of booleans where the key is the candidate_id
function getElectedCandidates($dir, $uncontested)
{
    $elected = array();
    foreach($uncontested as $id)
    {
        $elected[$id] = 1;
    }
    $clist = scandir($dir);    // list of council folders
    foreach ($clist as $council)
    {
        if (!in_array($council,array(".","..")))
        {
            if (is_dir($dir  . "/" . $council))
            {
                $fname = $dir . "/" . $council . "/all-constituency-info.json";
                if (file_exists($fname))
                {	
                    $wlist = scandir($dir . "/" . $council);  // list of ward folders
                    foreach ($wlist as $ward)
                    {
                        if (!in_array($ward,array(".","..")))
                        {
                            if (is_dir($dir  . "/" . $council . "/" . $ward))
                            {
                                $fname = $dir . "/" . $council . "/" . $ward . "/ResultsJson.json";
                                if (file_exists($fname))
                                {	
                                    $json = readJSON($fname);
                                    $no_elected = 0;
                                    $no_seats = $json->Constituency->countInfo->Number_Of_Seats;
                                    foreach ($json->Constituency->countGroup as $item)
                                    {
                                        if (!isset($elected[$item->Candidate_Id]))
                                        {
                                            switch ($item->Status)
                                            {
                                                case "Elected":
                                                    $elected[$item->Candidate_Id] = True;
                                                    $no_elected++;
                                                    break;
                                        
                                                case "Excluded":
                                                    $elected[$item->Candidate_Id] = False;
                                                    break;
                                            }
                                        }
                                    }
                                    // in this second pass we catch all the candidates not elected, who didn't reach Excluded status (only applicable if contest is complete)
                                    if ($no_elected == $no_seats)
                                    {
                                        foreach ($json->Constituency->countGroup as $item)
                                        {
                                            if (!isset($elected[$item->Candidate_Id]))
                                            {
                                                if (empty($item->Status))
                                                {
                                                    $elected[$item->Candidate_Id] = False;
                                                }
                                            }
                                        }
                                    }
                               }

                            }
                        }
                    }
                }
            }
        }
    }
    return ($elected);
}
?>