<?php
// script to do a sanity check on the (transfer) count data, essentially adding transfers to previous total_votes to see if they tally
require "functions.php";

$dataDir = "../2017/SCO/";
$precision = 0.001;  // 5 significant figures is a s precise as the results can be, but there seems to be some rounding at play so a precision of 0.01 might be more lenient for data extracted from some councils

verify_all($dataDir, $precision);

?>