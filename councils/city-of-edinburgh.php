<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";

// function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, $twimg)
head("#council17 City of Edinburgh - Map-based interface to crowd-sourced data for the Scottish Council elections 2017", 'city-of-edinburgh', 55.95, -3.18, 10, 'Ward_Name', 'Ward', 'Ward_Code', '/website/image/city-of-edinburgh.png');

echo'
<body>
	<div id="wrap">
';
navigation("Scottish Council elections 2017");
echo '
		<div class="content">
			<div id="map"></div>
			<h3 id="breadcrumb"><a href="/councils/">Scotland</a> // <a href="city-of-edinburgh.php">City of Edinburgh Council</a></h3>
			<div id="wardinfo"></div>
			<div id="candidates"></div>
		</div>
	</div>
	<script src="/website/js/script.js"></script>
	<script src="/website/js/map.js"></script>

</body>
</html>';

?>
