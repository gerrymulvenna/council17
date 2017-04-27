<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";
?> 
<!DOCTYPE html>
<html>
<head>    <title>#council17 Scotland - Map-based interface to crowd-sourced data for the Scottish Council elections 2017</title>

    <meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.js"></script>
    <link href="https://api.tiles.mapbox.com/mapbox.js/v1.6.4/mapbox.css" rel="stylesheet" />

	<link rel="stylesheet" type="text/css" href="/website/css/style.css" media="screen, handheld" />
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
    <meta property="og:url" content="http://council17.mulvenna.org/councils/" />
    <meta property="og:title" content="Scottish Council elections 2017 #council17" />
    <meta property="og:description" content="Presenting crowdsourced open data, live results and data visualisations for the Scottish Council Elections 2017" />
    <meta property="og:image" content="http://elections.gjm/website/image/scotland.png" />
</head>
<body>
	<div id="wrap">
<?php
navigation("Scottish Council elections 2017");
?>
<div class="content" align="center">
    <h1>Four ways to find your #council17 candidates</h1>
    <div class="main-option">
        <h3>Interactive map</h3>
        <a href="/councils/"><img src="/website/image/scotland.png"></a>
    </div>
    <div class="main-option">
        <h3>Find ward by postcode</h3>
		<div id="map" class="hidden"></div>
		<input id="postcode" type="text" placeholder="Postcode" maxlength="8"><br>
		<button id="me">Find ward</button>
		<script src="/website/js/postcode.js"></script>
	    <script src="/website/js/leaflet-pip.js"></script>
    </div>
    <div class="main-option">
        <h3>Drill down by council</h3>
        <a href="/treeview/"><img src="/website/image/treeview1.png"></a>
    </div>
    <div class="main-option">
        <h3>Drill down by party</h3>
        <a href="/treeview/by-party.php"><img src="/website/image/treeview2.png"></a>
    </div>
</div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-12076032-17', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>