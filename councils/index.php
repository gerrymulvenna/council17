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

echo'<div class="content">
			<div id="map"></div>
			<h2 id="breadcrumb">Scotland</h2>
			<div id="select-council">
				<select id="council-list" onClick="selectCouncil()"><option>Select a council from this list or the map</option>
					<option value="aberdeen-city.php">Aberdeen City</option>
					<option value="aberdeenshire.php">Aberdeenshire</option>
					<option value="angus.php">Angus</option>
					<option value="argyll-and-bute.php">Argyll And Bute</option>
					<option value="city-of-edinburgh.php">City Of Edinburgh</option>
					<option value="clackmannanshire.php">Clackmannanshire</option>
					<option value="dumfries-and-galloway.php">Dumfries And Galloway</option>
					<option value="dundee-city.php">Dundee City</option>
					<option value="east-ayrshire.php">East Ayrshire</option>
					<option value="east-dunbartonshire.php">East Dunbartonshire</option>
					<option value="east-lothian.php">East Lothian</option>
					<option value="east-renfrewshire.php">East Renfrewshire</option>
					<option value="falkirk.php">Falkirk</option>
					<option value="fife.php">Fife</option>
					<option value="glasgow-city.php">Glasgow City</option>
					<option value="highland.php">Highland</option>
					<option value="inverclyde.php">Inverclyde</option>
					<option value="midlothian.php">Midlothian</option>
					<option value="moray.php">Moray</option>
					<option value="na-h-eileanan-an-iar.php">Na H-Eileanan An Iar</option>
					<option value="north-ayrshire.php">North Ayrshire</option>
					<option value="north-lanarkshire.php">North Lanarkshire</option>
					<option value="orkney-islands.php">Orkney Islands</option>
					<option value="perth-and-kinross.php">Perth And Kinross</option>
					<option value="renfrewshire.php">Renfrewshire</option>
					<option value="scottish-borders.php">Scottish Borders</option>
					<option value="shetland-islands.php">Shetland Islands</option>
					<option value="south-ayrshire.php">South Ayrshire</option>
					<option value="south-lanarkshire.php">South Lanarkshire</option>
					<option value="stirling.php">Stirling</option>
					<option value="west-dunbartonshire.php">West Dunbartonshire</option>
					<option value="west-lothian.php">West Lothian</option>
				</select>
			</div>
			<div id="candidates"></div>
		</div>';
foot();
?>