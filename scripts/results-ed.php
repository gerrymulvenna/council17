<?php
require "functions.php";

$datadir = '../2012/SCO/city-of-edinburgh';
$ward_code = 'S13002923';
$id = 0;
$stage = 0;
$ward_name = 'Inverleith';
$ward_no = 5;

$ward_info = new countInfo($ward_name, $ward_no, 4, 23253, 23253, 11127, 11009);
$rdata = new Results($ward_info);
$stage++;
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish Green Party', 1, 'Nigel', 'BAGSHAW', 1642, 0, 1642);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish National Party (SNP)', 2, 'Gavin', 'BARRIE', 1434, 0, 1434);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Conservative and Unionist Party', 3, 'Scott', 'DOUGLAS', 1291, 0, 1291);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Labour  Party', 4, 'Lesley', 'HINDS', 2751, 0, 2751, "Elected", 1);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Liberal Democrats', 5, 'Tim', 'McKAY', 1143, 0, 1143);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Conservative and Unionist Party', 6, 'Iain', 'WHYTE', 1956, 0, 1956);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish National Party (SNP)', 7, 'John', 'YOUNG', 792, 0, 792);
$stage++;
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish Green Party', 1, 'Nigel', 'BAGSHAW', 1642, 172.81896, 1814.81896);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish National Party (SNP)', 2, 'Gavin', 'BARRIE', 1434, 54.67944, 1488.67944);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Conservative and Unionist Party', 3, 'Scott', 'DOUGLAS', 1291, 20.55468, 1311.55468);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Labour  Party', 4, 'Lesley', 'HINDS', 2751, -549.00000, 2202.00000, "Elected", 1);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Liberal Democrats', 5, 'Tim', 'McKAY', 1143, 83.01696, 1226.01696);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Conservative and Unionist Party', 6, 'Iain', 'WHYTE', 1956, 32.92740, 1988.92740);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish National Party (SNP)', 7, 'John', 'YOUNG', 792, 27.53928, 819.53928, "Excluded", 2);
$stage++;
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish Green Party', 1, 'Nigel', 'BAGSHAW', 1642, 50.58768, 1865.40664);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish National Party (SNP)', 2, 'Gavin', 'BARRIE', 1434, 659.37492, 2148.05436);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Conservative and Unionist Party', 3, 'Scott', 'DOUGLAS', 1291,  14.19956, 1325.75424);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Labour  Party', 4, 'Lesley', 'HINDS', 2751, 0, 2202.00000, "Elected", 1);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Liberal Democrats', 5, 'Tim', 'McKAY', 1143, 25.59428, 1251.61124, "Excluded", 3);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Conservative and Unionist Party', 6, 'Iain', 'WHYTE', 1956, 29.59868, 2018.52608);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish National Party (SNP)', 7, 'John', 'YOUNG', 792, -819.53928, 0.00000, "Excluded", 2);
$stage++;
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish Green Party', 1, 'Nigel', 'BAGSHAW', 1642,  459.32432, 2324.73096, "Elected", 4);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish National Party (SNP)', 2, 'Gavin', 'BARRIE', 1434, 116.18196, 2264.23632, "Elected", 4);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Conservative and Unionist Party', 3, 'Scott', 'DOUGLAS', 1291,  161.58108,1487.33532);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Labour  Party', 4, 'Lesley', 'HINDS', 2751, 0, 2202.00000, "Elected", 1);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Liberal Democrats', 5, 'Tim', 'McKAY', 1143, -1251.61124, 0.00000, "Excluded", 3);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Conservative and Unionist Party', 6, 'Iain', 'WHYTE', 1956, 260.97140,2279.49748, "Elected", 4);
$rdata->Constituency->countGroup[] = new countItem($id++, $ward_no, $stage, 'Scottish National Party (SNP)', 7, 'John', 'YOUNG', 792, 0, 0.00000, "Excluded", 2);

//Nigel BAGSHAW Scottish Green Party 1642 172.81896 1814.81896 50.58768 1865.40664 459.32432 2324.73096 Elected
//Gavin BARRIE Scottish National Party (SNP) 1434 54.67944 1488.67944 659.37492 2148.05436 116.18196 2264.23632 Elected
//Scott DOUGLAS Scottish Conservative and Unionist 1291 20.55468 1311.55468 14.19956 1325.75424 161.58108 1487.33532
//Lesley HINDS Scottish Labour Party 2751 -549.00000 2202.00000 0.00000 2202.00000 0.00000 2202.00000 Elected
//Tim MCKAY Scottish Liberal Democrats 1143 83.01696 1226.01696 25.59428 1251.61124 -1251.61124 0.00000
//Iain WHYTE Scottish Conservative and Unionist 1956 32.92740 1988.92740 29.59868 2018.52608 260.97140 2279.49748 Elected
//John YOUNG Scottish National Party (SNP) 792 27.53928 819.53928 -819.53928 0.00000
//Non-transferable votes 0 157.46328 157.46328 40.18416 197.64744 253.55248 451.19992

$fname = "$datadir/$ward_code/ResultsJson.json";
echo "<pre>\n";
echo "Writing $fname<br>\n";
writeJSON($rdata, $fname);
$alldata = readJSON("$datadir/all-constituency-info.json");

$wdata = new Constituencies($ward_name, $ward_no, $ward_code, $ward_info);
$new_ward = True;
for ($i = 0; $i<count($alldata->Constituencies); $i++)
{
    if ($alldata->Constituencies[$i]->Directory == $ward_code)
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
echo "</pre>\n";



?>