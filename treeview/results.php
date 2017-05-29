<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";

// --------- council specific variables in this section --------
// this should be the council identifier consistent with Democracy Club data, this website and to a certain extent the map data
// used in the mapName js variable, the twitter image, the breadcrumb link
$slug = 'results-tree';
$council_name = 'Explore #council17 results';  // used in the title and breadcrumb

// ------ below here should be the same for each council (but this is the top-level, so it has different content --------


// function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, $twimg)
head("Explore Scottish local election results", $slug, 0, 0, 0, NULL, "/website/image/treeview3.png");
navigation("Scottish Council elections 2017");

echo'<div class="content">

<h3>Explore and search the 2017 local election results for Scotland</h3>
<div id="tree-ack"><div id="dc-caption">Results data collated by <a href="https://twitter.com/gerrymulvenna">@gerrymulvenna</a> (31 councils) and <a href="https://twitter.com/andrewteale">@andrewteale</a> (Scottish Borders). The full set of candidate data was collated by</div><div id="dc-logo"><a href="http://democracyclub.org.uk"><img src="https://democracyclub.org.uk/static/dc_theme/images/logo-with-text-2017.png" width="250"></a></div></div>
	<input type="text" id="results-tree-search" value="" class="input" placeholder="Find party, candidate, council or ward" />
	<div id="results-tree" class="demo"></div>

</div>';
foot(True, True);
?>