<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";
 
// --------- council specific variables in this section --------
// this should be the council identifier consistent with Democracy Club data, this website and to a certain extent the map data
// used in the mapName js variable, the twitter image, the breadcrumb link
$slug = 'home-page';
$council_name = 'Landing page';  // used in the title and breadcrumb
 // ------ below here should be the same for each council (but this is the top-level, so it has different content --------
 
 
// function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, $twimg)
head("#council17 Browse data by map / council / party - interface to crowd-sourced data for the Scottish Council elections 2017", $slug, 0, 0, 0, NULL, "/website/image/treeview1.png");
navigation("Scottish Council elections 2017");
 
echo'<div class="content">
<h1>Three ways to find your #council17 candidates</h1>
<div class="main-option">
<h3>Interactive map</h3>
<img src="/website/image/scotland.png">
</div>
<div class="main-option">
<h3>Drill down by council</h3>
<img src="/website/image/treeview1.png">
</div>
<div class="main-option">
<h3>Drill down by party</h3>
<img src="/website/image/treeview2.png">
</div>
 
</div>';
foot(True, True);
?>