<?php
// script to pull data from Democracy Club csv and create JSONs
require "functions.php";

$dataRoot = "https://candidates.democracyclub.org.uk/media/candidates-";
$outDir = "../2017/SCO/";

$elected = getElectedCandidates($outDir, $elected_without_contest);
buildRtree($elections, $outDir, $party_prefix, $party_colors);
buildData(array_keys($elections), $dataRoot, $outDir);
buildPtree($elections, $outDir, $party_prefix);
buildCtree($elections, $outDir, $party_prefix);

//boundaryWards(array_keys($elections), $outDir, "boundary-wardinfo.csv");

// process the transfer data in the STV results, includes a "Labour / Co-op" fudge
function recordtransfers(&$tx, $data, $council, $ward)
{
    // first pass determine the donor at each stage
    $donors = array();
    $totals = array();
    $max = 0;
    foreach ($data as $item)
    {
        if ($item->Count_Number > $max)
        {
            $max = $item->Count_Number;
        }
        if ($item->Count_Number > 1)
        {
            if ($item->Transfers < 0)
            {
                $party = ($item->Party_Name == "Labour and Co-operative Party") ? "Labour Party" : $item->Party_Name;
                $donors[$item->Count_Number] = $party;
                addTransfers($tx, '.', $party, "total", 0 - $item->Transfers);
                addTransfers($tx, $council, $party, "total", 0 - $item->Transfers);
                addTransfers($tx, "$council/$ward", $party, "total",  0 - $item->Transfers);
                $totals[$item->Count_Number] = 0 - $item->Transfers;
            }
        }
    }

    // second pass record transfers to recipient parties, accumulating subtotals of transfers so we can derive the non-transferred amounts in the next pass
    $subtotals = array();
    foreach ($data as $item)
    {
        if ($item->Count_Number > 1)
        {
            if ($item->Transfers > 0)
            {
                $party = ($item->Party_Name == "Labour and Co-operative Party") ? "Labour Party" : $item->Party_Name;
                addTransfers($tx, '.', $donors[$item->Count_Number], $party, $item->Transfers);
                addTransfers($tx, $council, $donors[$item->Count_Number], $party, $item->Transfers);
                addTransfers($tx, "$council/$ward", $donors[$item->Count_Number], $party, $item->Transfers);
                if (isset($subtotals[$item->Count_Number]))
                {
                    $subtotals[$item->Count_Number] += $item->Transfers;
                }
                else
                {
                    $subtotals[$item->Count_Number] = $item->Transfers;
                }
            }
        }
    }

    // third pass to derive the non-transferred amounts 
    for ($stage = 2; $stage <= $max; $stage++)
    {
        addTransfers($tx, '.', $donors[$stage], "Not transferred", $totals[$stage] - $subtotals[$stage]);
        addTransfers($tx, $council, $donors[$stage], "Not transferred", $totals[$stage] - $subtotals[$stage]);
        addTransfers($tx, "$council/$ward", $donors[$stage], "Not transferred", $totals[$stage] - $subtotals[$stage]);
    }
}

function addTransfers(&$tx, $context, $donor, $recipient, $value)
{
    if (!array_key_exists($context, $tx))
    {
        $tx[$context] = array();
    }
    if (!array_key_exists($donor, $tx[$context]))
    {
        $tx[$context][$donor] = array();
    }
    if (array_key_exists($recipient, $tx[$context][$donor]))
    {
        $tx[$context][$donor][$recipient] += $value;
    }
    else
    {
        $tx[$context][$donor][$recipient] = $value;
    }
}

class transfer
{
    public $donor;
    public $donor_short;
    public $recipient;
    public $color;
    public $amount;
    public $total;

    function __construct($donor, $short, $recipient, $color, $amount, $total)
    {
        $this->donor = $donor;
        $this->donor_short = $short;
        $this->recipient = $recipient;
        $this->color = $color;
        $this->amount = $amount;
        $this->total = $total;
    }
}

function saveTransfers($tx, $dir, $pp, $pc)
{
    foreach ($tx as $context => $data)
    {
        $json = array();
        uasort($data, "cmpDonors");
        foreach ($data as $donor => $transfers)
        {
            uksort($transfers, "cmpRecipients");
            foreach ($transfers as $recipient => $value)
            {
                if ($recipient != "total")
                {
                    $json[] = new transfer($donor, $pp[stripParty($donor)], $pp[stripParty($recipient)], $pc[stripParty($donor)], 100 * $value / $data[$donor]["total"], $data[$donor]["total"]);
                }
            }
        }
        writeJSON($json, $dir . $context . "/transfers.json");
    }
}

// sort by totals descending
function cmpDonors($a, $b)
{
    if ($a["total"] == $b["total"])
    {
        return 0;
    }
    else
    {
        return ($a["total"] > $b["total"]) ? -1 : 1;
    }
}

// sort in alphabetical order, but leave N/T last, total is ignored ultimately (note: needs uksort to sort by keys)
function cmpRecipients($a, $b)
{
    if ($a == "total")
    {
        return 1;
    }
    if ($b == "total")
    {
        return -1;
    }
    if ($a == "Not transferred")
    {
        return 1;
    }
    if ($b == "Mot transferred")
    {
        return -1;
    }
    if ($a == $b)
    {
        return 0;
    }
    else
    {
        return ($a < $b) ? -1 : 1;
    }
}

        

//build a results tree to present party / councillor data at national, council and ward level
function buildRTree($elections, $dataDir, $party_prefix, $party_colors)
{
    global $elected_without_contest;  // array of candidate IDs standing in uncontested wards
    global $blank_row;                // field specification for CSV structure

    $national_parties = array();
    $councils = array();
    $council_parties = array();
    $wards = array();
    $ward_parties = array();
    $wardcode = array();
    $wardname = array();
    $cwards = array();
    $txdata = array();
    $id = 0;
    $ctotal = 0;
    $root = new jstree_node(++$id,"root","Scotland");
    $root->open();      // expand at startup
    $council_container = new jstree_node(++$id, "container", "Explore by council"); 
    $root->children[] = $council_container;

    // get an index of ward and council info so we can access the results data
    $wardinfo = readJSON($dataDir . "wardinfo.json");
    foreach ($wardinfo->Wards as $ward)
    {
        if (!empty($ward->election))
        {
            $wardcode[$ward->cand_ward_code] = $ward->map_ward_code;
            $wardname[$ward->cand_ward_code] = $ward->ward_name;
        }
    }

    // convert the candidate data to tree nodes indexed by cand_ward_code (post_id)
    echo "Building RESULTS data tree...<br>\n";
    foreach ($elections as $election => $council)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            $cdata = readJSON($dataDir. $election . ".json");
            foreach ($cdata->wards as $ward)
            {
                if (!empty($ward->post_id))
                {
                    $node = new jstree_node(++$id, "ward", $ward->post_label);
                    $cArray = array();  //clear array of candidates for each ward. We'll use this when pulling in results
                    foreach ($ward->candidates as $candidate)
                    {
                        $row = $blank_row;
                        // for the purposes of these summary data, let's treat "Labour-and-Co-operative-Party" as the same as "Labour Party"
                        if ($candidate->party_name == "Labour and Co-operative Party")
                        {
                            $candidate->party_name = "Labour Party";
                        }
                        $cArray[$candidate->id] = $candidate;
                        // create or update the party node
                        if (array_key_exists($election, $councils))
                        {
                            if (array_key_exists($election . $ward->post_label, $wards))
                            {
                                // all parent nodes exist, just need to find parent nodes from the arrays
                                $ward_node = $wards[$election . $ward->post_label];
                                $council_node = $councils[$election];
                            }
                            else
                            {
                                // need a new ward node
                                $council_node = $councils[$election];
                                $ward_node = new jstree_node(++$id, "ward", $wardname[$ward->post_id]);
                                $wards[$election . $ward->post_label] = $ward_node;
                                $council_node->children[0]->children[] = $ward_node;  // first child of each council node should be "Explore by ward"
                            }
                        }
                        else
                        {
                            // need a new council node, new ward node 
                            $council_node = new jstree_node(++$id, "council", $council);
                            $council_node->children[] = new jstree_node(++$id, "container", "Explore by wards");
                            $council_node->properties['slug'] = $matches[1];
                            $councils[$election] = $council_node;
                            $council_container->children[] = $council_node;
                            $ward_node = new jstree_node(++$id, "ward", $wardname[$ward->post_id]);
                            $wards[$election . $ward->post_label] = $ward_node;
                            $council_node->children[0]->children[] = $ward_node;  // first child of each council node should be "Explore by ward"
                        }
                        if (array_key_exists($candidate->party_name, $national_parties))
                        {
                            if (array_key_exists($candidate->party_name . $election, $council_parties))
                            {
                                if (array_key_exists($candidate->party_name . $election . $ward->post_label, $ward_parties))
                                {
                                    // all 3 types of node already exist for this party
                                    $ward_party_node = $ward_parties[$candidate->party_name . $election . $ward->post_label];
                                    $council_party_node = $council_parties[$candidate->party_name . $election];
                                    $national_party_node = $national_parties[$candidate->party_name];
                                }
                                else
                                {
                                    // need a new ward_party node 
                                    $ward_party_node = new jstree_node(++$id,"party",$candidate->party_name);
                                    $ward_parties[$candidate->party_name . $election . $ward->post_label] = $ward_party_node;
                                    $ward_node->children[] = $ward_party_node;
                                    $council_party_node = $council_parties[$candidate->party_name . $election];
                                    $national_party_node = $national_parties[$candidate->party_name];
                                }
                            }
                            else
                            {
                                // need a new council_party node and ward_party node
                                $council_party_node = new jstree_node(++$id, "party", $candidate->party_name);
                                $council_node->children[] = $council_party_node;
                                $council_parties[$candidate->party_name . $election] = $council_party_node;
                                $ward_party_node = new jstree_node(++$id,"party",$candidate->party_name);
                                $ward_parties[$candidate->party_name . $election . $ward->post_label] = $ward_party_node;
                                $ward_node->children[] = $ward_party_node;
                                $national_party_node = $national_parties[$candidate->party_name];
                            }
                        }
                        else
                        {
                            // need all the party nodes
                            $national_party_node = new jstree_node(++$id, "party", $candidate->party_name);
                            $national_parties[$candidate->party_name] = $national_party_node;
                            $root->children[] = $national_party_node;
                            $council_party_node = new jstree_node(++$id, "party", $candidate->party_name);
                            $council_node->children[] = $council_party_node;
                            $council_parties[$candidate->party_name . $election] = $council_party_node;
                            $ward_party_node = new jstree_node(++$id,"party",$candidate->party_name);
                            $ward_parties[$candidate->party_name . $election . $ward->post_label] = $ward_party_node;
                            $ward_node->children[] = $ward_party_node;
                        }
                        // now that we have all the nodes in place for this candidate we can add it to the appropriate places and add quantitative data from the results
                        $root->no_candidates += 1;
                        $ward_party_node->no_candidates += 1;
                        $council_party_node->no_candidates += 1;
                        $ward_node->no_candidates += 1;
                        $council_node->no_candidates += 1;
                        $national_party_node->no_candidates += 1;
                        $cand_node = new jstree_node(++$id,"candidate",$candidate->name);
                        $ward_party_node->children[] = $cand_node;

                        // populate the CSV data row for candidate variables
                        $row['id'] = $candidate->id;
                        $row['name'] = $candidate->name;
                        $row['party_name'] = $candidate->party_name;
                        $row['council_id'] = $matches[1];
                        $row['council_name'] = $council;
                        $row['election'] = $election;
                        $row['cand_ward_id'] = $ward->post_id;
                        $row['map_ward_id'] = $wardcode[$ward->post_id];
                        $row['ward_name'] = $wardname[$ward->post_id];
                        $row['contested'] = (in_array($candidate->id, $elected_without_contest)) ? 0 : 1;
                        $row['elected'] = ($candidate->elected == "True") ? 1 : 0;
                        $row['candidates'] = count($ward->candidates);

                        if ($candidate->elected == "True")
                        {
                            $ward_party_node->no_seats += 1;
                            $council_party_node->no_seats += 1;
                            $national_party_node->no_seats += 1;
                            $council_party_node->children[] = $cand_node;
                        }
                        // catch the uncontested wards here and update variables which won't get touched when we process the results
                        if (in_array($candidate->id, $elected_without_contest))
                        {
                            $root->no_seats++;;
                            $council_node->no_seats++;
                            $ward_node->no_seats++;
                            $row['status'] = 'Elected';
                        }
                        $data[$candidate->id] = $row;
                    }
                    // now let's get the results data
                    $fname = $dataDir . $matches[1] . "/" . $wardcode[$ward->post_id] . "/ResultsJson.json";
                    if (file_exists($fname))
                    {
                        $json = readJSON($fname);
                        recordtransfers($txdata, $json->Constituency->countGroup, $matches[1], $wardcode[$ward->post_id]);
                        $info = $json->Constituency->countInfo;
                        $parties = array();    // use this to update party properties once per ward
                        foreach ($json->Constituency->countGroup as $item)
                        {
                            if ($item->Count_Number == 1)
                            {
                                if (array_key_exists($item->Candidate_Id, $cArray))
                                {
                                    if (property_exists($cArray[$item->Candidate_Id], "party_name"))
                                    {
                                        $party = $cArray[$item->Candidate_Id]->party_name;
                                        if (!in_array($party, $parties))
                                        {
                                            $parties[] = $party;
                                        }
                                        $national_parties[$party]->incrementProperty("first_prefs",$item->Candidate_First_Pref_Votes);
                                        $council_parties[$party . $election]->incrementProperty("first_prefs",$item->Candidate_First_Pref_Votes);
                                        $ward_parties[$party . $election . $ward->post_label]->incrementProperty("first_prefs",$item->Candidate_First_Pref_Votes);
                                        // update CSV data
                                        $data[$item->Candidate_Id]['first_prefs'] = $item->Candidate_First_Pref_Votes;
                                        $data[$item->Candidate_Id]['status'] = $item->Status;
                                        $data[$item->Candidate_Id]['occurred_on_count'] = $item->Occurred_On_Count;
                                        $data[$item->Candidate_Id]['electorate'] = $info->Total_Electorate;
                                        $data[$item->Candidate_Id]['total_poll'] = $info->Total_Poll;
                                        $data[$item->Candidate_Id]['valid_poll'] = $info->Valid_Poll;
                                        $data[$item->Candidate_Id]['rejected'] = $info->Spoiled;
                                        $data[$item->Candidate_Id]['quota'] = $info->Quota;
                                        $data[$item->Candidate_Id]['seats'] = $info->Number_Of_Seats;
                                    }
                                    else
                                    {
                                        print_r($cArray[$item->Candidate_Id]);
                                    }
                                }
                                else
                                {
                                    echo "Candidate_Id NOT FOUND: " . $item->Candidate_Id . ", " . $item->Firstname . " " . $item->Surname . ", " . $item->Party_Name . "<br>\n";
                                }
                            }
                            else
                            {
                                // stages beyond the first add data to CSV
                                $tkey = "transfers" . sprintf("%02d", $item->Count_Number);
                                $vkey = "total_votes" . sprintf("%02d", $item->Count_Number);
                                $data[$item->Candidate_Id][$tkey] = $item->Transfers;
                                $data[$item->Candidate_Id][$vkey] = $item->Total_Votes;
                                if (empty($data[$item->Candidate_Id]['status']))
                                {
                                    $data[$item->Candidate_Id]['status'] = $item->Status;
                                    $data[$item->Candidate_Id]['occurred_on_count'] = $item->Occurred_On_Count;
                                }
                            }
                        }
                        foreach ($parties as $party)
                        {
                            if (array_key_exists($party, $national_parties))
                            {
                                $national_parties[$party]->incrementProperty("valid_poll",$info->Valid_Poll);
                                $national_parties[$party]->incrementProperty("quotas",$info->Quota);
                                $national_parties[$party]->incrementProperty("no_wards");
                            }
                            if (array_key_exists($party . $election, $council_parties))
                            {
                                $council_parties[$party . $election]->incrementProperty("no_wards");
                                $council_parties[$party . $election]->incrementProperty("quotas",$info->Quota);
                                $council_parties[$party . $election]->incrementProperty("valid_poll",$info->Valid_Poll);
                            }
                            $ward_parties[$party . $election . $ward->post_label]->incrementProperty("valid_poll",$info->Valid_Poll);
                            $ward_parties[$party . $election . $ward->post_label]->incrementProperty("quotas",$info->Quota);
                            $ward_parties[$party . $election . $ward->post_label]->incrementProperty("no_wards");
                        }
                        $root->incrementProperty("electorate", $info->Total_Electorate);
                        $councils[$election]->incrementProperty("electorate", $info->Total_Electorate);
                        $wards[$election . $ward->post_label]->incrementProperty("electorate", $info->Total_Electorate);

                        $root->incrementProperty("total_poll", $info->Total_Poll);
                        $councils[$election]->incrementProperty("total_poll", $info->Total_Poll);
                        $wards[$election . $ward->post_label]->incrementProperty("total_poll", $info->Total_Poll);

                        $root->incrementProperty("valid_poll", $info->Valid_Poll);
                        $councils[$election]->incrementProperty("valid_poll", $info->Valid_Poll);
                        $wards[$election . $ward->post_label]->incrementProperty("valid_poll", $info->Valid_Poll);

                        $root->incrementProperty("no_wards");
                        $councils[$election]->incrementProperty("no_wards");
                        $wards[$election . $ward->post_label]->incrementProperty("no_wards");

                        $root->no_seats += $info->Number_Of_Seats;
                        $councils[$election]->no_seats += $info->Number_Of_Seats;
                        $wards[$election . $ward->post_label]->no_seats += $info->Number_Of_Seats;
                    }
                    else
                    {
                        // probably an uncontested ward, which are dealt with in the main candidate data loop abovve
                        $root->incrementProperty("no_wards");
                        $council_node->incrementProperty("no_wards");
                        $ward_node->incrementProperty("no_wards");
                    }
                }
            }
        }
    }
    classifyParties($root, $party_prefix);
    writeJSON($root, $dataDir . "results-tree.json");

    // just Scotland-level data (used for colouring the map by biggest party)
    $summary = getSummary($root->children[0], $party_prefix, $party_colors);
    writeJSON($summary, $dataDir . "summary.json");

    // generate the scotland-level overview JSON, a summary of the party standings across all councils
    $overview = new Overview("All 32 Councils", "scotland","root");
    $overview->electorate = $root->properties['electorate'];
    $overview->total_poll = $root->properties['total_poll'];
    $overview->valid_poll = $root->properties['valid_poll'];
    $overview->no_seats = $root->no_seats;
    $overview->no_candidates = $root->no_candidates;
    $overview->no_wards = $root->properties['no_wards'];
    for ($i = 1; $i < count($root->children); $i++)   //skip element 0, which is just the container
    {
        $name = $root->children[$i]->icon;
        $short = $party_prefix[$name];
        $color = $party_colors[$name];
        $party = new Party($name, $short, $color);
        $party->no_seats = $root->children[$i]->no_seats;
        $party->no_candidates = $root->children[$i]->no_candidates;
        $party->no_wards = $root->children[$i]->properties['no_wards'];
        $party->first_prefs = $root->children[$i]->properties['first_prefs'];
        $party->quotas = round($root->children[$i]->properties['first_prefs'] / $root->children[$i]->properties['quotas'], 2);
        $overview->parties[] = $party;
    }
    writeJSON($overview, $dataDir . "overview.json");

    foreach ($root->children[0]->children as $cnode)
    {
        // generate the council-level overview JSON, a summary of the party standings for a given council
        if (preg_match('/<strong>(.+)<\/strong>/', $cnode->text, $matches))
        {
            $overview = new Overview($matches[1], $cnode->properties['slug'] ,"council");
            $overview->electorate = $cnode->properties['electorate'];
            $overview->total_poll = $cnode->properties['total_poll'];
            $overview->valid_poll = $cnode->properties['valid_poll'];
            $overview->no_seats = $cnode->no_seats;
            $overview->no_candidates = $cnode->no_candidates;
            $overview->no_wards = $cnode->properties['no_wards'];
            for ($i = 1; $i < count($cnode->children); $i++)   //skip element 0, which is just the container
            {
                $name = $cnode->children[$i]->icon;
                $short = $party_prefix[$name];
                $color = $party_colors[$name];
                $party = new Party($name, $short, $color);
                $party->no_seats = $cnode->children[$i]->no_seats;
                $party->no_candidates = $cnode->children[$i]->no_candidates;
                // where a party's only candidate is in an uncontested ward, no_wards will be set to zero, so it actually means CONTESTED wards in that context
                if (array_key_exists('no_wards', $cnode->children[$i]->properties))
                {
                    $party->no_wards = $cnode->children[$i]->properties['no_wards'];
                }
                else
                {
                    $party->no_wards = 0;
                }
                if (array_key_exists('first_prefs', $cnode->children[$i]->properties))
                {
                    $party->first_prefs = $cnode->children[$i]->properties['first_prefs'];
                    $party->quotas = round($cnode->children[$i]->properties['first_prefs'] / $cnode->children[$i]->properties['quotas'], 2);
                }
                else
                {
                    $party->first_prefs = 0;
                    $party->quotas = 0;
                }
                $overview->parties[] = $party;
            }
            writeJSON($overview, $dataDir . $cnode->properties['slug'] . "/overview.json");
        }
    }


//    saveTransfers($txdata, $dataDir, $party_prefix, $party_colors);

    $csv = array_values($data);  // needs to be a standard array, not associative
    for ($i=0;$i<count($csv); $i++)
    {
        // fill in a status for candidates not elected, but not excluded
        if (empty($csv[$i]['status']))
        {
            $csv[$i]['status'] = "Not elected";
        }
    }
    saveCSV($csv, $dataDir . "council17-mulvenna-org.csv");
}

// return an array of councils containing information about the parties with most seats in each council
// takes the "Explore by council" jsnode as its starting point
// assumes that there is a "slug" property in each council node
function getSummary($cnode, $party_prefix, $party_colors)
{
    $data = array();
    foreach($cnode->children as $node)
    {
        $council = $node->properties['slug'];
        $data[] = array('council'=>$council, 'no_seats' =>$node->no_seats, 'biggest_parties' => getPartiesWithMostSeats($node, $party_prefix, $party_colors));
    }
    return($data);
}

// return an array of one or more parties who have the most seats in a given council, specified by the council node ($parent)
// assumes there is an "icon" property in each party note specifying the party name (with hyphens)
function getPartiesWithMostSeats($parent, $party_prefix, $party_colors)
{
    $parties = array();
    $max = 0;
    foreach($parent->children as $party)
    {
        if ($party->no_seats > $max)
        {
            $max = $party->no_seats;
        }
    }
    foreach($parent->children as $party)
    {
        if ($party->no_seats == $max)
        {
            $name = $party->icon;
            $parties[] = array('party' => $party_prefix[$name], 'color' => $party_colors[$name], 'no_seats' => $party->no_seats);
        }
    }
    return($parties);
}

//recursive routine to apply prefix and class to party nodes
function classifyParties($root, $party_prefix)
{
    if ($root->type == "root" || $root->type == "council" || $root->type == "ward")
    {
        if (array_key_exists("total_poll", $root->properties))
        {
            $suffix =  number_format($root->no_seats) . (($root->no_seats == 1) ? " councillor" : " councillors");
            $suffix .= ", electorate: " . number_format($root->properties['electorate']) . ", turnout: " . number_format($root->properties['total_poll']) . sprintf(" (%.2f%%)", 100 * $root->properties['total_poll'] / $root->properties['electorate']);
            $suffix .= ", rejected: " . number_format($root->properties['total_poll'] - $root->properties['valid_poll']) .  sprintf(" (%.1f%%)", 100 * ($root->properties['total_poll'] - $root->properties['valid_poll']) / $root->properties['total_poll']);
        }
        else
        {
            $suffix =  number_format($root->no_seats) . (($root->no_seats == 1) ? " councillor" : " councillors");
            $suffix .= ", uncontested ward";
        }
        $root->text =  "<strong>" . $root->text . "</strong>";
        $root->text .= " <em>" . $suffix . "</em>";
    }
    if ($root->type == "root" || $root->type == "council" || $root->type == "ward")
    {
        $root->sortbyseats();
    }
    foreach ($root->children as $node)
    {
        if ($node->type == "party")
        {
            $party = stripParty($node->text);
            $node->icon = $party;        // icon property in jstree types plugin is interpreted as a class if it does not contain /
            $prefix = (array_key_exists($party, $party_prefix)) ? " " . $party_prefix[$party] . " " : " ";
            
            if (array_key_exists("first_prefs", $node->properties))
            {
                $suffix = $node->no_seats . (($node->no_seats == 1) ? " councillor" : " councillors") . sprintf(" (%.1f%%) ", 100 * $node->no_seats / $root->no_seats);
                $suffix .= " from ". number_format($node->no_candidates) . (($node->no_candidates == 1) ? " candidate" : " candidates") . sprintf(" (%.1f%% success rate), ", 100 * $node->no_seats / $node->no_candidates); 
                $suffix .= number_format($node->properties['first_prefs']) . " first prefs " . sprintf("(%.1f%%)", 100 * $node->properties['first_prefs'] / $root->properties['valid_poll']);
                if (array_key_exists("quotas", $node->properties))
                {
                    $suffix .= sprintf(", %.2f quotas per ward", $node->properties['first_prefs'] / $node->properties['quotas']);
                }
            }
            elseif ($node->no_candidates > 0) // uncontested wards will fall in here
            {
                $suffix = $node->no_seats . (($node->no_seats == 1) ? " councillor" : " councillors") . sprintf(" (%.1f%%) ", 100 * $node->no_seats / $root->no_seats);
                $suffix .= " from ". number_format($node->no_candidates) . sprintf(" candidates (%.1f%% success rate), ", 100 * $node->no_seats / $node->no_candidates); 
                $suffix .= "0 first prefs (0.0%)";
                $suffix .= ", 0.00 quotas per ward";
            }
            else
            {
                $suffix = "";
            }
            $node->text = $prefix . " " . $suffix;
        }

        if (count($node->children) > 0)
        {
            classifyParties($node, $party_prefix);
        }
    }
}
    


//build JSON data for the jstree with Parties as the children of the root using wardinfo and the candidate JSON for each council
function buildPTree($elections, $dataDir, $party_prefix)
{
    $parties = array();
    $councils = array();
    $wards = array();
    $wardcode = array();
    $wardname = array();
    $cwards = array();
    $id = 0;
    $ctotal = 0;
    $root = new jstree_node(++$id,"root","All Parties");
    $root->open();      // expand at startup

    // get an index of ward and council info so we can build href preoperties for ward and candidate nodes
    echo "Building PARTIES data tree...<br>\n";
    $wardinfo = readJSON($dataDir . "wardinfo.json");
    foreach ($wardinfo->Wards as $ward)
    {
        if (!empty($ward->election))
        {
            $wardcode[$ward->cand_ward_code] = $ward->map_ward_code;
            $wardname[$ward->cand_ward_code] = $ward->ward_name;
        }
    }

    // convert the candidate data to tree nodes indexed by cand_ward_code (post_id)
    foreach ($elections as $election => $council)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            $cdata = readJSON($dataDir. $election . ".json");
            foreach ($cdata->wards as $ward)
            {
                if (!empty($ward->post_id))
                {
                    foreach ($ward->candidates as $candidate)
                    {
                        // create or update the party node
                        if (array_key_exists($candidate->party_name, $parties))
                        {
                            if (array_key_exists($candidate->party_name . $election, $councils))
                            {
                                if (array_key_exists($candidate->party_name . $election . $ward->post_label, $wards))
                                {
                                    $ward_node = $wards[$candidate->party_name . $election . $ward->post_label];
                                    $party_node = $parties[$candidate->party_name];
                                    $party_node->no_candidates += 1;
                                }
                                else
                                {
                                    $council_node = $councils[$candidate->party_name . $election];
                                    $party_node = $parties[$candidate->party_name];
                                    $party_node->no_candidates += 1;
                                    $ward_node = new jstree_node(++$id,"ward",$wardname[$ward->post_id]);
                                    $ward_node->properties['cand_map_code'] = $ward->post_id;
                                    $wards[$candidate->party_name . $election . $ward->post_label] = $ward_node;
                                    $council_node->children[] = $ward_node;
                                }
                            }
                            else
                            {
                                $party_node = $parties[$candidate->party_name];
                                $party_node->no_candidates += 1;
                                $council_node = new jstree_node(++$id,"council",$council);
                                $councils[$candidate->party_name . $election] = $council_node;
                                $party_node->children[] = $council_node;
                                $ward_node = new jstree_node(++$id,"ward",$wardname[$ward->post_id]);
                                $ward_node->properties['cand_map_code'] = $ward->post_id;
                                $wards[$candidate->party_name . $election . $ward->post_label] = $ward_node;
                                $council_node->children[] = $ward_node;
                            }
                        }
                        else
                        {
                            $party_node = new jstree_node(++$id,"party", $candidate->party_name);
                            $party_node->no_candidates = 1;

                            $parties[$candidate->party_name] = $party_node;
                            $root->children[] = $party_node;
                            $council_node = new jstree_node(++$id,"council",$council);
                            $councils[$candidate->party_name . $election] = $council_node;
                            $party_node->children[] = $council_node;
                            $ward_node = new jstree_node(++$id,"ward",$wardname[$ward->post_id]);
                            $ward_node->properties['cand_map_code'] = $ward->post_id;
                            $wards[$candidate->party_name . $election . $ward->post_label] = $ward_node;
                            $council_node->children[] = $ward_node;
                        }
                        $root->no_candidates += 1;
                        $name = ($candidate->elected == "True") ? '<span class="elected">' . $candidate->name . '</span>' : $candidate->name;
                        $cand_node = new jstree_node(++$id,"candidate",$name);
                        $ward_node->children[] = $cand_node;
                        $ward_node->applyProperty('href', '/councils/' . $matches[1] . ".php?ward=" . $wardcode[$ward_node->properties['cand_map_code']]);
                    }
                }
            }
        }
    }
    $root->sortbycandidate();
    foreach ($root->children as $party_node)
    {
        $party = stripParty($party_node->text);
        $party_node->icon = $party;        // icon property in jstree types plugin is interpreted as a class if it does not contain /
        $prefix = (array_key_exists($party, $party_prefix)) ? " (" . $party_prefix[$party] . ") " : " ";
        $party_node->text = $prefix . $party_node->text;
    }
    extendParties($root);
    writeJSON($root, $dataDir . "party-tree.json");
}


//build JSON data for the jstree library using wardinfo and the candidate JSON for each council
function buildCTree($elections, $dataDir, $party_prefix)
{
    $councils = array();
    $wards = array();
    $cwards = array();
    $id = 0;
    $ctotal = 0;

    // convert the candidate data to tree nodes indexed by cand_ward_code (post_id)
    echo "Building CANDIDATE data tree...<br>\n";
    foreach ($elections as $election => $council_slug)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            $cdata = readJSON($dataDir. $election . ".json");
            foreach ($cdata->wards as $ward)
            {
                if (!empty($ward->post_id))
                {
                    $node = new jstree_node(++$id, "ward", $ward->post_label);
                    $node->no_candidates = count($ward->candidates);
                    
                    $node->children = convertCandidates ($ward->candidates, $id, $party_prefix);
                    $id += count($node->children);
                    $ctotal += count($node->children);    // keep track of the total candidates
                    $cwards[$ward->post_id] = $node;
                }
            }
        }
    }

    // convert the wardinfo data to tree nodes and build the tree structure incorporating the candidate nodes
    $root = new jstree_node(++$id,"root","Scottish Councils");
    $root->open();      // expand at startup
    $root->no_candidates = $ctotal;
    $wardinfo = readJSON($dataDir . "wardinfo.json");
    foreach ($wardinfo->Wards as $ward)
    {
        if (!empty($ward->election))
        {
            // create or update the council node
            if (array_key_exists($ward->election, $councils))
            {
                $root->no_seats += $ward->seats + 0;
            }
            else
            {
                $root->no_seats += $ward->seats + 0;
                $node = new jstree_node(++$id,"council",$ward->council);
                $councils[$ward->election] = $node;
                $root->children[] = $node;
            }
            // add the ward node
            $ward_node = new jstree_node(++$id, "ward", $ward->ward_name);
            $ward_node->no_seats = $ward->seats;
            $ward_node->properties["map_ward_code"] = $ward->map_ward_code;
            $ward_node->properties["cand_ward_code"] = $ward->cand_ward_code;
            $ward_node->properties["ward_no"] = $ward->ward_no;
            $ward_node->children = $cwards[$ward->cand_ward_code]->children;
            $ward_node->applyProperty("href", "/councils/" . $ward->election . ".php?ward=" . $ward->map_ward_code);
            $ward_node->no_candidates = $cwards[$ward->cand_ward_code]->no_candidates;
            $node->children[] = $ward_node;
            $node->no_candidates += $ward_node->no_candidates;
            $node->no_seats += $ward->seats + 0;
        }
    }
    extendNames($root);
    writeJSON($root, $dataDir . "council-tree.json");
}

// quick fix to show counts in party tree (recursive)
function extendParties($node)
{
    if (count($node->children))
    {
        switch ($node->type)
        {
            case "root":
                $node->text .= " (" . $node->no_candidates . " candidate(s) from " . count($node->children). " political parties)";
                break;
            case "party":
                $node->text .= " (" . $node->no_candidates . " candidate(s) contesting " . $node->countType("ward") . " ward(s) across " . count($node->children) . " council(s))";
                break;
        }
        foreach ($node->children as $child)
        {
            extendParties($child);
        }
    }
}

 // quick fix to show seats and candidates for parent nodes (recursive)
 function extendNames($node)
 {
     if (count($node->children))
     {
        switch ($node->type)
        {
            case "root":
                $node->text .= " (" . count($node->children) . " councils, " . $node->countType("ward") . " wards, " . $node->no_seats . " seats, " . $node->no_candidates . " candidates)";
                break;
            case "council":
                $node->text .= " (" . count($node->children) . " wards, " . $node->no_seats . " seats, " . $node->no_candidates . " candidates)";
                break;
            case "ward":
                $node->text .= " (" . $node->no_seats . " seats, " . $node->no_candidates . " candidates)";
                break;
        }
        foreach ($node->children as $child)
        {
            extendNames($child);
        }
     }
 }


// convert an array of candidates to an array of jstree nodes
function convertCandidates($candidates, $last_id, $party_prefix)
{
    $nodes = array();
    foreach ($candidates as $c)
    {
        $party = stripParty($c->party_name);
        $prefix = (array_key_exists($party, $party_prefix)) ? " (" . $party_prefix[$party] . ") " : " ";
        $name = ($c->elected == "True") ? '<span class="elected">' . $c->name . '</span>' : $c->name;

        $node = new jstree_node(++$last_id, "candidate", $prefix . $name);
        $node->icon = $party;        // icon property in jstree types plugin is interpreted as a class if it does not contain /
        $node->no_candidates = 1;
        $nodes[] = $node;
    }
    return($nodes);
}



// wards -> candidates 
function buildData($elections, $dataRoot, $dir)
{
    global $elected;

    $wardinfo = array();
    echo "Building CANDIDATE data files...<br>\n";
    foreach ($elections as $election)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            $wards = array();
            $wardIDs = array();   //used to keep track of which wards have been added
            $candURL = $dataRoot.$election.".csv";
            $arrCand = getData($candURL, $election);
            for ($i = 1; $i < count($arrCand); $i++)
            {
                if (count($arrCand[$i]) <= 1)
                {
                    unset ($arrCand[$i]);
                }
            }

            $header = array_shift($arrCand);
            array_walk($arrCand, '_combine_array', $header);
  
            foreach ($arrCand as $candidate)
            {
                // fudge to get surname using part after last space
                $names = splitName($candidate['name']);
                if (!empty($names))
                {
                    $candidate = array_merge($candidate, $names);
                }
                if (isset($elected[$candidate['id']]))
                {
                    $candidate['elected'] = ($elected[$candidate['id']]) ? "True" : "False";
                }
                $post_id = $candidate['post_id'];
                if (!empty($post_id))
                {
                    $post_label = $candidate['post_label'];
                    unset ($candidate['election']);
                    unset ($candidate['post_id']);
                    unset ($candidate['post_label']);
					$key = array_search($post_id, $wardIDs);
                    if ($key === False)
                    {
                        $wardIDs[] = $post_id;
                        $wards[] = array('post_id' => $post_id, 'post_label' => $post_label, 'election' => $election, 'candidates' => array($candidate));
                        $wardinfo[] = array('post_id' => $post_id, 'post_label' => $post_label, 'election' => $matches[1]);
                    }
                    else
                    {
                        array_push($wards[$key]['candidates'], $candidate);			   
                    }
                }
            }
            $dc = new DemoClub_Wards();
            $dc->wards = $wards;
            writeJSON($dc, $dir . $election . ".json");
        }
    }
    saveCSV($wardinfo, "candidate-wardinfo.csv");
}

// once-off routine to get ward codes from boundary data files
function boundaryWards($elections, $dir, $my_file)
{
    $wardinfo = array();
    echo "Getting ward code mappings...<br>\n";
    foreach ($elections as $election)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            $b_file = $dir . 'boundaries/'. $matches[1] . ".geojson";
            $json = file_get_contents($b_file);
            $data = json_decode($json);
            foreach($data->features as $feature)
            {
                $prop = $feature->properties;
                // this is the newer boundary data
                if (isset($prop->Ward_Code))
                {
                    $wardinfo[] = array('council' => $prop->Council, 'ward_no' => $prop->Ward_No, 'post_label' => $prop->Ward_Name, 'post_id' => $prop->Ward_Code);
                }
                // this is the older type
                elseif (isset($prop->CODE))
                {
                    $wardinfo[] = array('council' => $prop->FILE_NAME, 'ward_no' => $prop->Ward_no, 'post_label' => $prop->NAME, 'post_id' => $prop->CODE);
                }
            }
        }
    }
    saveCSV($wardinfo, $my_file);
}
            

function splitName($name)
{
  $ret = array();
  $pos = strrpos($name, " ");
	if ($pos)
	{
  	$surname = substr($name, $pos + 1);
  	$firstname = substr($name, 0, $pos);
		$ret['Surname'] = $surname;
		$ret['firstname'] = $firstname;
	}
	return ($ret);
}

// replace and remove certain characters from a party name to be consistent with the CSS form used in script.js
// party_name.replace(/\s+/g, "-").replace(/[\',()]/g,"")
function stripParty($name)
{
    $endash = html_entity_decode('&#x2013;', ENT_COMPAT, 'UTF-8');
    $pattern = array('/\s+/', "/['\",()]/", "/$endash/u");
    $replacement = array('-', '', '_');
    return( preg_replace($pattern, $replacement, $name));
}


?>