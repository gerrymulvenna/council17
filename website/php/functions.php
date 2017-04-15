<?php
function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc = NULL, $twimg = '/website/image/scotland.png')
{
    echo '
<!DOCTYPE html>
<html>
<head>';
    echo"    <title>$title</title>\n";
    echo'
    <meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="http://cdn.leafletjs.com/leaflet-0.7/leaflet.js"></script>
	<script src="http://cdn.rawgit.com/calvinmetcalf/leaflet-ajax/gh-pages/dist/leaflet.ajax.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/lodash/4.11.1/lodash.min.js"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.js"></script>
<script type="text/javascript">
// global vars for maps.js
';
    echo "    var mapName = '$mapName';\n";
    echo "    var mapLat = $mapLat;\n";
    echo "    var mapLong = $mapLong;\n";
    echo "    var mapZoom = $mapZoom;\n";
    echo "    var mapProperty = '$mapProperty';\n";
    echo "    var mapUnit = '$mapUnit';\n";
    if($mapWardDesc)
    {
        echo "    var mapWardDesc = '$mapWardDesc';\n";
    }
    echo '</script>

  <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7/leaflet.css"/>
	<link rel="stylesheet" type="text/css" href="/website/css/style.css" media="screen, handheld" />
	<link rel="stylesheet" type="text/css" href="/website/css/parties.css" media="screen, handheld" />
	<link rel="stylesheet" type="text/css" href="/website/css/enhanced.css" media="screen  and (min-width: 50.5em)" />
	<link rel="icon" type="image/png" href="/website/image/c17-icon-150x150.png" sizes="150x150" />
	<link rel="icon" type="image/png" href="/website/image/favicon-32x32.png" sizes="32x32" />
	<link rel="icon" type="image/png" href="/website/image/favicon-16x16.png" sizes="16x16" />
		<!--[if (lt IE 9)&(!IEMobile)]>
		<link rel="stylesheet" type="text/css" href="enhanced.css" />
		<![endif]-->
    <meta name="description" content="Map-based interface to browse the candidates for the Scottish Council Elections 2017" />
    <meta name="keywords" content="Scotland, local elections, open data, 2017, crowdsource, single transferable vote, stv, ward, candidate, voting, #council17, electoral"
    />
    <meta name="author" content="Gerry Mulvenna">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="1 month">
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@gerrymulvenna" />
    <meta name="twitter:creator" content="@gerrymulvenna" />
    <meta property="og:url" content="http://mulvenna.org" />
    <meta property="og:title" content="Scottish Council elections 2017 #council17" />
    <meta property="og:description" content="Presenting crowdsourced open data, live results and data visualisations for the Scottish Council Elections 2017" />
';
echo "    <meta property=\"og:image\" content=\"http://" . $_SERVER['SERVER_NAME'] . "$twimg\" />\n";
echo '	<!-- data, elections, ni -->

</head>
<body>
	<div id="wrap">
';

}

function navigation($title, $param2 = NULL, $param3 = NULL, $param4 = NULL)
{
    echo"<header><h1><a href = \"/\">$title</a></h1><p>Browse candidates for #council17 in Scotland</p></header>\n";
    echo'
        <label for="show-menu" class="show-menu">Menu</label>
        <input type="checkbox" id="show-menu" role="button">
        <div id="cssmenu">
            <ul>
<!--                <li><a href="/candidates"><span>Candidates</span></a></li>-->
                <li><a href="/councils"><span>By map</span></a></li>
<!--                <li><a href="/parties"><span>Parties</span></a></li>-->
                <li><a href="/about"><span>About</span></a></li>
            </ul>
        </div>';
}

function content($council_name, $slug, $param3 = NULL, $param4 = NULL)
{
echo '
		<div class="content">
    			<div id="map"></div>
';
echo "			<h2 id=\"breadcrumb\"><a href=\"/councils/\">Scotland</a> | <a href=\"$slug.php\">$council_name</a></h2>\n";
echo '			<div id="wardinfo"></div>
			<div id="candidates">
            </div>
		</div>
';
}

function foot($param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
{
echo '
	</div>
	<script src="/website/js/script.js"></script>
	<script src="/website/js/map.js"></script>

</body>
</html>';
}

?>