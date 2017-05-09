<?php
require "functions.php";

echo "<pre>\n";

if (isset($_POST['council']) && isset($_POST['ward']) && isset($_POST['year']))
{
    $ward_info = new countInfo($_POST['ward_name'], $_POST['ward'], $_POST['seats'], $_POST['electorate'], $_POST['electorate'], $_POST['total_poll'], $_POST['valid_poll']);
    $datadir = '../' . $_POST['year'] . '/SCO/' . $_POST['council'];
    $topfile = "$datadir/all-constituency-info.json";
    if (file_exists($topfile))
    {
        echo "Reading $topfile\n";
        $json = readJSON($topfile);
        $alldata = new Council($_POST['ward_name'], $_POST['ward'], $_POST['ward'], $ward_info);
        $alldata->set($json);
    }
    else
    {
        $alldata = new Council($_POST['ward_name'], $_POST['ward'], $_POST['ward'], $ward_info);
    }

    $fname = "$datadir/" . $_POST['ward']. "/ResultsJson.json";
    if (file_exists($fname))
    {
        echo "Reading $fname<br>\n";
        $json = readJSON($fname);
        $rdata = new Results($ward_info);
        $rdata->set($json);   // this isn't really working - it overwrites new countInfo data
        $rdata->Constituency->countInfo = $ward_info; // so this is a workaround to bring in the posted values for electorate etc.
    }
    else
    {
        $rdata = new Results($ward_info);
    }

    $matrix = str_replace(',', '', $_POST['pastebin']);  // take out any commas
    if (strlen($matrix) > 0)
    {
        $pastebin = preg_split("/\\r\\n|\\r|\\n/", $matrix);
        $candidates = $_POST['Candidate_Id'];
        $data_missing = True;
        while (count($pastebin) >= count($candidates))
        {
            $data_missing = False;
            for ($i=0; $i<count($candidates); $i++)
            {
                $numbers[$i] = preg_split("/\\s/", array_shift ($pastebin));
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
                            $arrStatus = $rdata->currentStatus($rdata->Constituency->countGroup[$row]->Candidate_Id);
                            if ($arrStatus)
                            {
                                echo "Stage " . $stage . " data added for " . $_POST['Firstname'][$row] . " " . $_POST['Surname'][$row] . ", " . $arrStatus['Status'] . " " . $arrStatus['Occurred_On_Count'] . "\n";
                                $rdata->Constituency->countGroup[] = new countItem($id++, $_POST['ward'], $stage, $_POST['Party_Name'][$row], $_POST['Candidate_Id'][$row], $_POST['Firstname'][$row], $_POST['Surname'][$row], $rdata->Constituency->countGroup[$row]->Candidate_First_Pref_Votes, $numbers[$row][$col], $numbers[$row][$col+1], $arrStatus['Status'], $arrStatus['Occurred_On_Count']);
                            }
                            else
                            {
                                echo "Stage " . $stage . " data added for " . $_POST['Firstname'][$row] . " " . $_POST['Surname'][$row] . "\n";
                                $rdata->Constituency->countGroup[] = new countItem($id++, $_POST['ward'], $stage, $_POST['Party_Name'][$row], $_POST['Candidate_Id'][$row], $_POST['Firstname'][$row], $_POST['Surname'][$row], $rdata->Constituency->countGroup[$row]->Candidate_First_Pref_Votes, $numbers[$row][$col], $numbers[$row][$col+1]);
                            }
                        }
                    }
                }
                else
                {
                    echo "Data mismatch. " . count($rdata->Constituency->countGroup) . " count records in JSON data, " . count($candidates) . " candidates.\n";
                }
            }
            else
            {
                $stage = 1;
                $id = 0;
                $offset = (($numbers[0][0] == 0) && (count($numbers[0]) % 2 == 0)) ? 0 : -1;          // allow a skip on first column of transfers 
                for ($col = 0; $col < count($numbers[0]); $col+=2, $stage++)
                {
                    for ($row = 0; $row < count($candidates); $row++)
                    {
                        if ($stage == 1)
                        {
                            echo "Stage " . $stage . " data added for " . $_POST['Firstname'][$row] . " " . $_POST['Surname'][$row] . "\n";
                            $rdata->Constituency->countGroup[] = new countItem($id++, $_POST['ward'], $stage, $_POST['Party_Name'][$row], $_POST['Candidate_Id'][$row], $_POST['Firstname'][$row], $_POST['Surname'][$row], $numbers[$row][$offset + 1], 0, $numbers[$row][$col + 1 + $offset]);
                        }
                        else
                        {
                            echo "Stage " . $stage . " data added for " . $_POST['Firstname'][$row] . " " . $_POST['Surname'][$row] . "\n";
                            $rdata->Constituency->countGroup[] = new countItem($id++, $_POST['ward'], $stage, $_POST['Party_Name'][$row], $_POST['Candidate_Id'][$row], $_POST['Firstname'][$row], $_POST['Surname'][$row], $numbers[$row][$offset + 1], $numbers[$row][$col + $offset], $numbers[$row][$col + 1 + $offset]);
                        }
                    }
                }
            }
        }
        if ($data_missing)
        {
            echo "Data mismatch. " . count($pastebin) . " rows detected. " . count($candidates) . " candidates.\n";
        }
    }
    else
    {
        echo "No new data detected. Length of 'pastebin' = " . strlen($matrix) . "\n";
    }
    $rdata->updateStatus();
    echo "Updating results data " . $fname . "\n";
    writeJSON($rdata, $fname);
    
    $wdata = new Constituency_Summary($_POST['ward_name'], $_POST['ward'], $_POST['ward'], $ward_info);
    $new_ward = True;
    for ($i = 0; $i<count($alldata->Constituencies); $i++)
    {
        if ($alldata->Constituencies[$i]->Directory == $_POST['ward'])
        {
            $alldata->Constituencies[$i] = $wdata;
            echo "Updating " . $wdata->Constituency_Name . "\n";
            $new_ward = False;
        }
    }
    if ($new_ward)
    {
        $alldata->Constituencies[] = $wdata;
        echo "Adding " . $wdata->Constituency_Name . "\n";
    }
    writeJSON($alldata,"$datadir/all-constituency-info.json");
    echo "Writing $datadir/all-constituency-info.json\n";
}
else
{
    echo "Meta-data not detected, council: " . $_POST['council'] . ", ward: " . $_POST['ward'] . ", year: " . $_POST['year'] . "\n";
}

echo "</pre>\n";
?>