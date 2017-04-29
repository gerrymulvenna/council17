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
?>