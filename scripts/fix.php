<?php

require "functions.php";

$dataDir = "../2017/SCO/";

correct($dataDir, "clackmannanshire");

function correct ($dir, $council)
{
    $root = $dir . $council;
    $dirlist = scandir($root);
    print_r($dirlist);
    foreach ($dirlist as $ward)
    {
        if (!in_array($ward,array(".","..")))
        {
            if (is_dir($root  . "/" . $ward))
            {
                $fname = $root . "/" . $ward . "/ResultsJson.json";
                if (file_exists($fname))
                {	
			echo "Reading $fname<br>\n";
			$json = readJSON($fname);
			foreach($json->Constituency->countGroup as $item)
			{
			    if ($item->Candidate_First_Pref_Votes == 0 && $item->Total_Votes > 0)
			    {
    				$item->Candidate_First_Pref_Votes = $item->Total_Votes;
			    }
			}
			echo "Writing $fname<br>\n";
			writeJSON($json, $fname);
                }
            }
        }
    }
}



?>