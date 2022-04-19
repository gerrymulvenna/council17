<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";

// --------- council specific variables in this section --------
// this should be the council identifier consistent with Democracy Club data, this website and to a certain extent the map data
// used in the mapName js variable, the twitter image, the breadcrumb link
$slug = 'council-tree';
$council_name = 'Browse data by council';  // used in the title and breadcrumb

// ------ below here should be the same for each council (but this is the top-level, so it has different content --------


// function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, $twimg)
head("#council17 Browse data by council / ward / candidate - interface to crowd-sourced data for the Scottish Council elections 2017", $slug, 0, 0, 0, NULL, "/website/image/treeview1.png");
navigation("Scottish Council elections 2017");

echo'<div class="content">

<h3>Explore and search by COUNCIL</h3>
<div id="tree-ack"><div id="dc-caption">This full set of candidate data was collated by</div><div id="dc-logo"><a href="http://democracyclub.org.uk"><img src="/website/image/democracyclub-logo-with-text.png" width="250"></a></div></div>
	<input type="text" id="council-tree-search" value="" class="input" placeholder="Find council, ward or candidate" />
	<div id="council-tree" class="demo"></div>

</div>';
foot(True, True);
?>