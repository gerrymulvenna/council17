<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/europarl.php";
$slug = 'ireland';
europarl_head("Results visualisation for the European Parliament election", $slug, "/website/image/results.png");
navigation("European Parliament elections (Ireland)");
europarl_content();
europarl_foot();
?>