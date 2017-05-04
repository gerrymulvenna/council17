<?php

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

    function __construct($id, $no, $count, $party, $candID, $fname, $sname, $firstpref, $transfers, $total, $status = NULL, $occurred = NULL)
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

class Constituencies
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

?>