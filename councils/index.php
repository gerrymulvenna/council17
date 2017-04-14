<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";

// function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, )
head("#council17 Map-based interface to crowd-sourced data for the Scottish Council elections 2017", 'scottish-councils', 57.6, -4.2247, 6, 'NAME', 'Council', NULL,'/website/image/scotland.png');
echo '
<body>
	<div id="wrap">';
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
		</div>
	</div>
	<script src="/website/js/script.js"></script>
	<script src="/website/js/map.js"></script>

</body>
</html>';
?>