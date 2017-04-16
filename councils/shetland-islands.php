<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";

// --------- council specific variables in this section --------
// this should be the council identifier consistent with Democracy Club data, this website and to a certain extent the map data
// used in the mapName js variable, the twitter image, the breadcrumb link
$slug = 'shetland-islands';
$council_name = 'Shetland';  // used in the title and breadcrumb
$mapLat = 60.3;              // good centre position for the map
$mapLong = -1.2659;          // good centre position for the map 
$mapZoom = 8;                // zoom level starting position
$mapProperty = 'NAME';  // the property in the geojson file with the name of the ward
$mapUnit = 'Ward';           // either Council or Ward
$mapWardDesc= 'CODE';        // the property in the geojson file with the unique ward identifier

// function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, $twimg)
head("#council17 $council_name - Map-based interface to crowd-sourced data for the Scottish Council elections 2017", $slug, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, "/website/image/$slug.png");

// ------ below here should be the same for each council --------

// function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, $twimg)
head("#council17 $council_name - Map-based interface to crowd-sourced data for the Scottish Council elections 2017", $slug, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, "/website/image/$slug.png");
navigation("Scottish Council elections 2017");
content($council_name, $slug);
foot();
?>
