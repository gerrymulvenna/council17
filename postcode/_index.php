<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";

// --------- council specific variables in this section --------
// this should be the council identifier consistent with Democracy Club data, this website and to a certain extent the map data
// used in the mapName js variable, the twitter image, the breadcrumb link
$slug = 'postcode';
$council_name = 'Find ward by postcode';  // used in the title and breadcrumb

// ------ below here should be the same for each council (but this is the top-level, so it has different content --------


// function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, $twimg)
head("#council17 Find ward by postcode - interface to crowd-sourced data for the Scottish Council elections 2017", $slug, 0, 0, 0, NULL, "/website/image/treeview1.png");
navigation("Scottish Council elections 2017");

echo'<div class="content">

<h3>Find ward by Postcode</h3>
	<input id="postcodd" placeholder="Enter a postcode" maxchars="8"><br>
    <button id="find">Find ward by postcode</button>
			<div id="map"></div>

</div>';
foot(True, True);
?>