<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";

// --------- council specific variables in this section --------
// this should be the council identifier consistent with Democracy Club data, this website and to a certain extent the map data
// used in the mapName js variable, the twitter image, the breadcrumb link
$slug = 'scotland';
$council_name = 'Scotland';  // used in the title and breadcrumb
$mapLat = 57.6;              // good centre position for the map
$mapLong = -4.2247;          // good centre position for the map 
$mapZoom = 6;                // zoom level starting position
$mapProperty = 'NAME';  // the property in the geojson file with the name of the ward
$mapUnit = 'Council';           // either Council or Ward
$mapWardDesc= NULL;        // the property in the geojson file with the unique ward identifier

// ------ below here should be the same for each council (but this is the top-level, so it has different content --------


// function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, $twimg)
head("#council17 $council_name - Map-based interface to crowd-sourced data for the Scottish Council elections 2017", $slug, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, "/website/image/$slug.png");
navigation("Scottish Council elections 2017");
echo "\n<script type=\"text/javascript\">\n";
echo "    var mapTitle = 'Scotland';\n";
echo '</script>
<div class="content">
			<div id="map"></div>
            <div id="details">
                <h2 id="breadcrumb">Scotland</h2>
';
	
selectCouncil("Select a council from this list or the map");
echo'
                <div id="quota"></div>
                <div id="overview"></div>
            </div>
            <div id="candidates"></div>
		</div>';
foot();
?>