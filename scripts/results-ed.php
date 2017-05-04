<?php
require "functions.php";

$datadir = '../2012/SCO/city-of-edinburgh';
$ward_code = 'S13002923';
$id = 0;

$ward_info = new countInfo('Inverleith', 5, 4, 23253, 23253, 11127, 11127);
$rdata = new Results($ward_info);
$rdata->Constituency->countGroup[] = new countItem($id++, 5, 1, 'Scottish Green Party', 1, 'Nigel', 'BAGSHAW', 1642, 0, 1642);
$rdata->Constituency->countGroup[] = new countItem($id++, 5, 1, 'Scottish National Party (SNP)', 2, 'Gavin', 'BARRIE', 1434, 0, 1434);
$rdata->Constituency->countGroup[] = new countItem($id++, 5, 1, 'Conservative and Unionist Party', 3, 'Scott', 'DOUGLAS', 1291, 0, 1291);
$rdata->Constituency->countGroup[] = new countItem($id++, 5, 1, 'Labour  Party', 4, 'Lesley', 'HINDS', 2751, 0, 2751, "Elected", 1);
$rdata->Constituency->countGroup[] = new countItem($id++, 5, 1, 'Liberal Democrats', 5, 'Tim', 'McKAY', 1143, 0, 1143);
$rdata->Constituency->countGroup[] = new countItem($id++, 5, 1, 'Conservative and Unionist Party', 6, 'Iain', 'WHYTE', 1956, 0, 1956);
$rdata->Constituency->countGroup[] = new countItem($id++, 5, 1, 'Scottish National Party (SNP)', 7, 'John', 'YOUNG', 792, 0, 792);

$fname = "$datadir/$ward_code/ResultsJson.json";
echo "Writing $fname<br>\n";
writeJSON($rdata, $fname);
$alldata = readJSON("$datadir/all-constituency-info.json");
print_r($alldata);

$wdata = new Constituencies('Inverleith', 5, $ward_code, $ward_info);
$codes = array_column($alldata->Constituencies, "Directory");
$new_ward = True;
for ($i = 0; $i<count($codes); $i++)
{
    if (codes[$i] == $ward_code)
    {
        $alldata->Constituencies[$i] = $wdata;
        $updated = False;
    }
}
if ($new_ward)
$alldata->Constituencies[] = $wdata;
writeJSON($alldata,"$datadir/all-constituency-info.json");
echo "Writing $datadir/all-constituency-info.json<br>\n";



//Nigel BAGSHAW Scottish Green Party 1642 172.81896 1814.81896 50.58768 1865.40664 459.32432 2324.73096 Elected
//Gavin BARRIE Scottish National Party (SNP) 1434 54.67944 1488.67944 659.37492 2148.05436 116.18196 2264.23632 Elected
//Scott DOUGLAS Scottish Conservative and Unionist 1291 20.55468 1311.55468 14.19956 1325.75424 161.58108 1487.33532
//Lesley HINDS Scottish Labour Party 2751 -549.00000 2202.00000 0.00000 2202.00000 0.00000 2202.00000 Elected
//Tim MCKAY Scottish Liberal Democrats 1143 83.01696 1226.01696 25.59428 1251.61124 -1251.61124 0.00000
//Iain WHYTE Scottish Conservative and Unionist 1956 32.92740 1988.92740 29.59868 2018.52608 260.97140 2279.49748 Elected
//John YOUNG Scottish National Party (SNP) 792 27.53928 819.53928 -819.53928 0.00000
//Non-transferable votes 0 157.46328 157.46328 40.18416 197.64744 253.55248 451.19992
?>