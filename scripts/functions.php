<?php

// avoid any scripting on public-facing website to avoid trashing any data files
if ($_SERVER['SERVER_ADDR'] == "216.92.68.138")
{
    header("Location: /"); /* Redirect browser */
    exit();
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
        $elected[$id] = "True";
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
                                    foreach ($json->Constituency->countGroup as $item)
                                    {
                                        if (!isset($elected[$item->Candidate_Id]))
                                        {
                                            switch ($item->Status)
                                            {
                                                case "Elected":
                                                    $elected[$item->Candidate_Id] = True;
                                                    break;
                                        
                                                case "Excluded":
                                                    $elected[$item->Candidate_Id] = False;
                                                    break;
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