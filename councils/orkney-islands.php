<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";

// function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, $twimg)
head("#council17 Orkney - Map-based interface to crowd-sourced data for the Scottish Council elections 2017", 'orkney-islands', 58.9809, -2.9605, 8, 'Ward_Name', 'Ward', 'CODE', '/website/image/orkney-islands.png');

echo'
<body>
	<div id="wrap">
';
navigation("Scottish Council elections 2017");
echo '
		<div class="content">
			<div id="map"></div>
			<h2 id="breadcrumb"><a href="/councils/">Scotland</a> // <a href="orkney-islands.php">Orkney Council</a></h2>
			<div id="wardinfo"></div>
			<div id="candidates"></div>
		</div>
	</div>
	<script src="/website/js/script.js"></script>
	<script src="/website/js/map.js"></script>

</body>
</html>
';
?>
