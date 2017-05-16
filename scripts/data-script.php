<?php
// script to pull data from Democracy Club csv and create JSONs
require "functions.php";

$dataRoot = "https://candidates.democracyclub.org.uk/media/candidates-";
$outDir = "../2017/SCO/";

$elected = getElectedCandidates($outDir, $elected_without_contest);
buildRtree($elections, $outDir, $party_prefix);
//buildData(array_keys($elections), $dataRoot, $outDir);
//buildPtree($elections, $outDir, $party_prefix);
//buildCtree($elections, $outDir, $party_prefix);

//boundaryWards(array_keys($elections), $outDir, "boundary-wardinfo.csv");

//build a results tree to present party / councillor data at national, council and ward level
function buildRTree($elections, $dataDir, $party_prefix)
{
    global $elected;

    $national_parties = array();
    $councils = array();
    $council_parties = array();
    $wards = array();
    $ward_parties = array();
    $wardcode = array();
    $cwards = array();
    $id = 0;
    $ctotal = 0;
    $root = new jstree_node(++$id,"root","Scotland");
    $council_container = new jstree_node(++$id, "container", "Explore by council"); 
    $root->children[] = $council_container;

    // get an index of ward and council info so we can access the results data
    $wardinfo = readJSON($dataDir . "wardinfo.json");
    foreach ($wardinfo->Wards as $ward)
    {
        if (!empty($ward->election))
        {
            $wardcode[$ward->cand_ward_code] = $ward->map_ward_code;
        }
    }

    // convert the candidate data to tree nodes indexed by cand_ward_code (post_id)
    foreach ($elections as $election => $council)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            echo "RESULTS data " . $election . "<br>\n";
            $cdata = readJSON($dataDir. $election . ".json");
            foreach ($cdata->wards as $ward)
            {
                if (!empty($ward->post_id))
                {
                    $node = new jstree_node(++$id, "ward", $ward->post_label);
                    $cArray = array();  //clear array of candidates for each ward. We'll use this when pulling in results
                    foreach ($ward->candidates as $candidate)
                    {
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
                                $ward_node = new jstree_node(++$id, "ward", $ward->post_label);
                                $wards[$election . $ward->post_label] = $ward_node;
                                $council_node->children[0]->children[] = $ward_node;  // first child of each council node should be "Explore by ward"
                            }
                        }
                        else
                        {
                            // need a new council node, new ward node 
                            $council_node = new jstree_node(++$id, "council", $council);
                            $council_node->children[] = new jstree_node(++$id, "container", "Explore by wards");
                            $councils[$election] = $council_node;
                            $council_container->children[] = $council_node;
                            $ward_node = new jstree_node(++$id, "ward", $ward->post_label);
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
                        // now that we have all the nodes in place for this candidate we can add it to the appropriate places and add quantitate data from the results
                        $root->no_candidates += 1;
                        $ward_party_node->no_candidates += 1;
                        $council_party_node->no_candidates += 1;
                        $national_party_node->no_candidates += 1;
                        $cand_node = new jstree_node(++$id,"candidate",$candidate->name);
                        $ward_party_node->children[] = $cand_node;
                        if ($candidate->elected == "True")
                        {
                            $ward_party_node->no_seats += 1;
                            $council_party_node->no_seats += 1;
                            $national_party_node->no_seats += 1;
                            $council_party_node->children[] = $cand_node;
                        }
                    }
                    // now let's get the results data
                    $fname = $dataDir . $matches[1] . "/" . $wardcode[$ward->post_id] . "/ResultsJson.json";
                    echo "Getting results from $fname<br>\n";
                    if (file_exists($fname))
                    {
                        $json = readJSON($fname);
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
                                    }
                                    else
                                    {
                                        print_r($cArray[$item->Candidate_Id]);
                                    }
                                }
                            }
                        }
                        $info = $json->Constituency->countInfo;
                        foreach ($parties as $party)
                        {
                            if (array_key_exists($party, $national_parties))
                            {
                                $national_parties[$party]->incrementProperty("valid_poll",$info->Valid_Poll);
                                $national_parties[$party]->incrementProperty("ward_quotas",$info->Quota);
                                $national_parties[$party]->incrementProperty("no_wards");
                            }
                            if (array_key_exists($party, $council_parties))
                            {
                                $council_parties[$party . $election]->incrementProperty("no_wards");
                                $council_parties[$party . $election]->incrementProperty("ward_quotas",$info->Quota);
                                $council_parties[$party . $election]->incrementProperty("valid_poll",$info->Valid_Poll);
                            }
                            $ward_parties[$party . $election . $ward->post_label]->incrementProperty("valid_poll",$info->Valid_Poll);
                            $ward_parties[$party . $election . $ward->post_label]->incrementProperty("ward_quotas",$info->Quota);
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

                        $root->no_seats += $info->Number_Of_Seats;
                        $councils[$election]->no_seats += $info->Number_Of_Seats;
                        $wards[$election . $ward->post_label]->no_seats += $info->Number_Of_Seats;
                    }
                    else
                    {
                        // probably an uncontested ward   
                    }
                }
            }
        }
    }
    classifyParties($root, $party_prefix);
    writeJSON($root, $dataDir . "results-tree.json");
}

//recursive routine to apply prefix and class to party nodes
function classifyParties($root, $party_prefix)
{
    if ($root->type == "root" || $root->type == "council" || $root->type == "ward")
    {
        echo "Sorting " . $root->text . "\n";
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
                $suffix = ": " . $node->no_seats . (($node->no_seats == 1) ? " seat" : " seats") . sprintf(" (%.1f%%), ", 100 * $node->no_seats / $root->no_seats) . sprintf("%d first preferences (%.1f%%)", $node->properties['first_prefs'], 100 * $node->properties['first_prefs'] / $root->properties['valid_poll']);
            }
            else
            {
                $suffix = "";
            }
            $node->text = $prefix . $node->text . $suffix;
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
    global $elected;

    $parties = array();
    $councils = array();
    $wards = array();
    $wardcode = array();
    $cwards = array();
    $id = 0;
    $ctotal = 0;
    $root = new jstree_node(++$id,"root","All Parties");

    // get an index of ward and council info so we can build href preoperties for ward and candidate nodes
    $wardinfo = readJSON($dataDir . "wardinfo.json");
    foreach ($wardinfo->Wards as $ward)
    {
        if (!empty($ward->election))
        {
            $wardcode[$ward->cand_ward_code] = $ward->map_ward_code;
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
                    echo "PARTIES candidate data " . $election . " " . $ward->post_id . "<br>\n";
                    $node = new jstree_node(++$id, "ward", $ward->post_label);
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
                                    $ward_node = new jstree_node(++$id,"ward",$ward->post_label);
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
                                $ward_node = new jstree_node(++$id,"ward",$ward->post_label);
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
                            $ward_node = new jstree_node(++$id,"ward",$ward->post_label);
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
    $root->listChildren();
    foreach ($root->children as $party_node)
    {
        $party = stripParty($party_node->text);
        $party_node->icon = $party;        // icon property in jstree types plugin is interpreted as a class if it does not contain /
        $prefix = (array_key_exists($party, $party_prefix)) ? " " . $party_prefix[$party] . " " : " ";
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
    foreach ($elections as $election => $council_slug)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            $cdata = readJSON($dataDir. $election . ".json");
            foreach ($cdata->wards as $ward)
            {
                if (!empty($ward->post_id))
                {
                    echo "Candidate data " . $election . " " . $ward->post_id . "<br>\n";
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
    $root->no_candidates = $ctotal;
    $wardinfo = readJSON($dataDir . "wardinfo.json");
    foreach ($wardinfo->Wards as $ward)
    {
        if (!empty($ward->election))
        {
            echo "Ward data " . $ward->election . " " . $ward->ward_name . "<br>\n";
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
        $prefix = (array_key_exists($party, $party_prefix)) ? " " . $party_prefix[$party] . " " : " ";
        echo "$prefix ";
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
    foreach ($elections as $election)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            $wards = array();
            $wardIDs = array();   //used to keep track of which wards have been added
            $candURL = $dataRoot.$election.".csv";
            $arrCand = getData($candURL, $election);
            echo "$election<br>";
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
    foreach ($elections as $election)
    {
        if (preg_match('/^local\.(.+)\.2017-05-04$/', $election, $matches))
  	    {
            echo "$election<br>";
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