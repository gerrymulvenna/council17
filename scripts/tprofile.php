<?php
// script to analyse a transfer profile report

analyse_transfer_profile("sample-profile.csv");

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

    echo "<pre>\n";
    print_r($votes);
    print_r($npref);
    print_r($score);
    echo "</pre>\n";
}

?>