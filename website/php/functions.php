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
  <script src="/website/js/jstree/jstree.min.js"></script>
  <script src="/website/js/jstree/jstree.types.js"></script>
  <script src="/website/js/jstree/jstree.search.js"></script>
  <script src="/website/js/leaflet-pip.js"></script>
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
    <link rel="stylesheet" href="/website/css/font-awesome.min.css">
	<link rel="stylesheet" href="/website/js/jstree/themes/default/style.min.css" />
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
    <meta property="og:url" content="http://council17.mulvenna.org/councils/" />
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
                <li><a href="/councils"><span>By map</span></a></li>
                <li><a href="/treeview/"><span>By council</span></a></li>
                <li><a href="/treeview/by-party.php"><span>By party</span></a></li>
                <li><a href="/about"><span>About</span></a></li>
            </ul>
        </div>';
}

function content($council_name, $slug, $param3 = NULL, $param4 = NULL)
{
echo "<script type=\"text/javascript\">\n";
echo "    var mapTitle = '$council_name';\n";
echo "</script>\n";
echo '
		<div class="content">
    			<div id="map"></div>
';
echo "			<h2 id=\"breadcrumb\"><a href=\"/councils/\">Scotland</a> | <a href=\"$slug.php\">$council_name</a></h2>\n";
selectCouncil("Pick a different council from this list?");
echo '			<div id="wardinfo"><h5>Choose a ward in this council from the map</h5></div>
			<div id="candidates">
            </div>
		</div>
';
}

function foot($infopage = False, $treeview = False, $param3 = NULL, $param4 = NULL)
{
echo '
	</div>';
if (!$infopage)
{
    echo '
        <script src="/website/js/map.js"></script>
        <script src="/website/js/script.js"></script>
    ';
}
if ($treeview)
{
    echo '
        <script src="/website/js/treeview.js"></script>
    ';
}
// Google analytics
echo"<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-12076032-17', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>";
}

function selectCouncil ($prompt)
{
echo'<div id="select-council">
				<select id="council-list" class="select" onClick="selectCouncil()">';
echo "\n<option>$prompt</option>\n";
echo '					<option value="aberdeen-city.php">Aberdeen City</option>
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
					<option value="eilean-siar.php">Na h-Eileanan An Iar</option>
					<option value="north-ayrshire.php">North Ayrshire</option>
					<option value="north-lanarkshire.php">North Lanarkshire</option>
					<option value="orkney-islands.php">Orkney Islands</option>
					<option value="perth-and-kinross.php">Perth And Kinross</option>
					<option value="renfrewshire.php">Renfrewshire</option>
					<option value="the-scottish-borders.php">Scottish Borders</option>
					<option value="shetland-islands.php">Shetland Islands</option>
					<option value="south-ayrshire.php">South Ayrshire</option>
					<option value="south-lanarkshire.php">South Lanarkshire</option>
					<option value="stirling.php">Stirling</option>
					<option value="west-dunbartonshire.php">West Dunbartonshire</option>
					<option value="west-lothian.php">West Lothian</option>
				</select>
			</div>';

}

?>