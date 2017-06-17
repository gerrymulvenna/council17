<?php
// script to analyse a transfer profile report

analyse_transfer_profile("Ward_3_-_Preference_Profile_Report.csv");

function analyse_transfer_profile($infile)
{
    $profile = file($infile);   // read profile into array
    echo "<pre>\n";

    // first line has no. of candidates and no of seats
    $firstline = trim(array_shift($profile));
    list($candidates, $seats) = explode(" ", $firstline, 2);

    // initialise grids with zero
    $score[0][0] = 0;
    for ($i=0; $i<=$candidates; $i++)
    {
        for ($j=1; $j<=$candidates; $j++)
        {
            if ($i > 0 && $j <> $i)
            {
                $second[$i][$j] = 0;
                $third[$i][$j] = 0;
            }
            $votes[$i][$j] = 0;  // how many $j preferences did candidate $i get, use $i=0 for total
            $npref[$i][$j] = 0;  // for ballots with a first preference for candidate $i, count the frequency of $j candidates ranked in total, use $i=0 for total
            $score[$i][$j] = 0;  // for ballots with a first preference for candidate $i, award a score of (n - p) / (n - 1) to candidate $j, where n is no. of candidates and p is candidate $j preference, use $i=0 for total
        }
    }
    do
    {
        // each line with numbers has format: N p1 p2 .. pn 0, where N is the number of votes for this permutation of preferences p1 p2 .. pn with 0 marking the end of the sequence
        $data = trim(array_shift($profile));
        $values = explode(' ', $data, $candidates + 2);
        if ($values[0] > 0)
        {
            $npref[$values[1]][count($values) - 2] += $values[0];
            $npref[0][count($values) - 2] += $values[0];
            if ($values[2] > 0)
            {
                $second[$values[1]][$values[2]] += $values[0];
            }
            if (count($values) > 4)
            {
                $third[$values[1]][$values[3]] += $values[0];
            }
            for ($i=1; $i<count($values) - 1; $i++)
            {
                $votes[$values[$i]][$i] += $values[0];
                $votes[0][$i] += $values[0];
                if ($i == 1)
                {
                    $score[$values[1]][$values[1]] += $values[0]; // keep track of total first pref votes
                    $score[0][0] += $values[0]; // keep track of total first pref votes
                    $score[0][$values[$i]] += $values[0]; // add first preference votes with a score of 1.0 each
                }
                else
                {
                    $score[$values[1]][$values[$i]] += ($candidates - $i) / ($candidates - 1); // add the score formula
                    $score[0][$values[$i]] += ($candidates - $i) / ($candidates - 1); // add the score formula
                }
            }
        }
    } while ($values[0] > 0);

    $names[0] = "";   //placeholder
    for ($i=1; $i<=$candidates; $i++)
    {
        $names[$i] = preg_replace('/"/', '', trim(array_shift($profile)));  //candidate
    }
    $names[0] = preg_replace('/"/', '', trim(array_shift($profile)));       //ward

    $_score[$names[0]][$names[0]] = sprintf("%6.4f)", 1);
    for ($i=0; $i<=$candidates; $i++)
    {
        if ($i > 0)
        {
            $_second[$names[$i]]["Nobody"] = sprintf("%6d (%4.1f%%)", $npref[$i][1], 100 * $npref[$i][1] / $votes[$i][1]);    // no second transfers
            $_third[$names[$i]]["Nobody"] = sprintf("%6d (%4.1f%%)", $npref[$i][1] + $npref[$i][2], 100 * ($npref[$i][1] + $npref[$i][2]) / $votes[$i][1]);    // no second transfers
        }
        for ($j=1; $j<=$candidates; $j++)
        {
            if ($i > 0 && $j <> $i)
            {
                $_second[$names[$i]][$names[$j]] = sprintf("%6d (%4.1f%%)", $second[$i][$j], 100 * $second[$i][$j] / $votes[$i][1]);
                $_third[$names[$i]][$names[$j]] = sprintf("%6d (%4.1f%%)", $third[$i][$j], 100 * $third[$i][$j] / $votes[$i][1]);
            }
            $_votes[$names[$i]][$j] = sprintf("%6d (%5.1f%%)", $votes[$i][$j], 100 * $votes[$i][$j] / $votes[0][$j]);
            $_npref[$names[$i]][$j] = sprintf("%6d (%5.1f%%)", $npref[$i][$j], 100 * $npref[$i][$j] / $votes[$i][1]);
            $_score[$names[$i]][$names[$j]] = sprintf("%6.2f, %6.4f)", $score[$i][$j], ($candidates -1) * $score[$i][$j] / $score[$i][$i]);
        }
    }
            
    

    echo "<pre>\n";
    echo "Who gets the second preference?\n";
    print_r($_second);
    echo "Who gets the third preference?\n";
    print_r($_third);
    echo "Rank frequency for each candidate\n";
    print_r($_votes);
    echo "How many candidates were ranked?\n";
    print_r($_npref);
    echo "Notional scores\n";
    print_r($_score);
    echo "</pre>\n";
}

?>