<?php
require "functions.php";

echo "<pre>\n";
print_r($_POST);
echo "</pre>\n";

if (isset($_POST['council']) && isset($_POST['ward']) && isset($_POST['year']))
{
    $datadir = '../' . $_POST['year'] . '/SCO/' . $_POST['council'];
    $alldata = readJSON("$datadir/all-constituency-info.json");

    $fname = "$datadir/" . $_POST['ward']. "/ResultsJson.json";
    $ward_info = new countInfo($_POST['ward_name'], $_POST['ward'], $_POST['seats'], $_POST['electorate'], $_POST['electorate'], $_POST['total_poll'], $_POST['valid_poll']);
    if (file_exists($fname))
    {
        echo "Reading $fname<br>\n";
        $json = readJSON($fname);
        $rdata = new Results($ward_info);
        $rdata->set($json);
    }
    else
    {
        $rdata = new Results($ward_info);
    }

    if (strlen($_POST['pastebin'] > 0))
    {
        $pastebin = preg_split("/\\r\\n|\\r|\\n/", $_POST['pastebin']);
        $candidates = $_POST['Candidate_Id'];
        if (count($pastebin) >= count($candidates))
        {
            for ($i=0; $i<count($candidates); $i++)
            {
                $numbers[$i] = preg_split("/\\s/", $pastebin[$i]);
            }
            if (count($rdata->Constituency->countGroup) > 0)
            {
                # the number of candidates should be a factor of the number of countInfo records
                if (count($rdata->Constituency->countGroup) % count($candidates) == 0)
                {
                    $stage = (count($rdata->Constituency->countGroup) / count($candidates)) + 1;
                    $id = count($rdata->Constituency->countGroup);
                    for ($col = 0; $col < count($numbers[0]); $col+=2, $stage++)
                    {
                        for ($row = 0; $row < count($candidates); $row++)
                        {
                            $rdata->Constituency->countGroup[] = new countItem($id++, $_POST['ward'], $stage, $_POST['Party_Name'][$row], $_POST['Candidate_Id'][$row], $_POST['Firstname'][$row], $_POST['Surname'][$row], $rdata->Constituency->countGroup[$row]->Candidate_First_Pref_Votes, $numbers[$row][$col], $numbers[$row][$col+1]);
                        }
                    }
                }
            }
            else
            {
                $stage = 1;
                $id = 0;
                for ($col = 0; $col < count($numbers[0]); $col+=2, $stage++)
                {
                    for ($row = 0; $row < count($candidates); $row++)
                    {
                        if ($stage == 1)
                        {
                            $rdata->Constituency->countGroup[] = new countItem($id++, $_POST['ward'], $stage, $_POST['Party_Name'][$row], $_POST['Candidate_Id'][$row], $_POST['Firstname'][$row], $_POST['Surname'][$row], $numbers[$row][0], 0, $numbers[$row][$col]);
                        }
                        else
                        {
                            $rdata->Constituency->countGroup[] = new countItem($id++, $_POST['ward'], $stage, $_POST['Party_Name'][$row], $_POST['Candidate_Id'][$row], $_POST['Firstname'][$row], $_POST['Surname'][$row], $numbers[$row][0], $numbers[$row][$col - 1], $numbers[$row][$col]);
                        }
                    }
                }
            }
        }
    }
    $rdata->updateStatus();
    writeJSON($rdata, $fname);
    
    $wdata = new Constituencies($_POST['ward_name'], $_POST['ward'], $_POST['ward'], $ward_info);
    $new_ward = True;
    for ($i = 0; $i<count($alldata->Constituencies); $i++)
    {
        if ($alldata->Constituencies[$i]->Directory == $_POST['ward'])
        {
            $alldata->Constituencies[$i] = $wdata;
            echo "Updating " . $wdata->Constituency_Name . "<br>\n";
            $new_ward = False;
        }
    }
    if ($new_ward)
    {
        $alldata->Constituencies[] = $wdata;
        echo "Adding " . $wdata->Constituency_Name . "<br>\n";
    }
    writeJSON($alldata,"$datadir/all-constituency-info.json");
    echo "Writing $datadir/all-constituency-info.json<br>\n";
        
}

?>