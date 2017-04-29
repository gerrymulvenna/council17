<?php

require "functions.php";

createFolders("../2017/SCO/wardinfo.json");

// script to populate directory structure based on wardinfo
function createFolders($warddata)
{
    $wardinfo = readJSON($warddata);
    foreach ($wardinfo->Wards as $ward)
    {
        if (!empty($ward->election))
        {
            $dir = "../2017/SCO/" . $ward->election . "/" . $ward->map_ward_code;
            if (mkdir($dir, "0755", TRUE))
            {
                echo  $dir . " success<br>\n";
            }
            else
            {
                echo  $dir . " failure<br>\n";
            }
        }
    }
    
}
?>

