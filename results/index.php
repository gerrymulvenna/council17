<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";
$slug = 'scotland';
results_head("#council17 results visualisation for the Scottish Council elections 2017", $slug, "/website/image/scotland.png");
navigation("Scottish Council elections 2017");
results_content();
results_foot();
?>