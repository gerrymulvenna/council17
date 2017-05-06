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
        $rdata = readJSON($fname);
        $rdata->Constituency->countInfo = $ward_info;
    }
    else
    {
        $rdata = new Results($ward_info);
    }
    writeJSON($rdata, $fname);
    

//    $stage++;
//    $rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish Green Party', 1, 'Nigel', 'BAGSHAW', 1642, 0, 1642);
//    $rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish National Party (SNP)', 2, 'Gavin', 'BARRIE', 1434, 0, 1434);
//    $rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Conservative and Unionist Party', 3, 'Scott', 'DOUGLAS', 1291, 0, 1291);
//    $rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Labour  Party', 4, 'Lesley', 'HINDS', 2751, 0, 2751, "Elected", 1);
//    $rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Liberal Democrats', 5, 'Tim', 'McKAY', 1143, 0, 1143);
//    $rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Conservative and Unionist Party', 6, 'Iain', 'WHYTE', 1956, 0, 1956);
//    $rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish National Party (SNP)', 7, 'John', 'YOUNG', 792, 0, 792);

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