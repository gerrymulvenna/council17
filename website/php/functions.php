<?php

require "mappings.php";

function add_count_head($title, $name, $twimg)
{
    echo '
<!DOCTYPE html>
<html>
<head>';
    echo"    <title>$title</title>\n";
    echo'
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.js"></script>

    <link rel="stylesheet" href="/website/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="/website/css/style.css" media="screen, handheld" />
    <link rel="stylesheet" type="text/css" href="/website/css/overview.css" media="screen, handheld" />
    <link rel="stylesheet" type="text/css" href="/website/css/enhanced.css" media="screen  and (min-width: 60.5em)" />
    <link rel="stylesheet" type="text/css" href="/website/css/parties.css" media="screen, handheld" />

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
echo "    <meta property=\"og:image\" content=\"https://" . $_SERVER['SERVER_NAME'] . "$twimg\" />\n";
echo '

</head>
<body>

    <div id="wrap">
';
}

function results_head($title, $name, $twimg)
{
    global $councils, $wards;

    $desc = "Presenting results and data visualisations for the Scottish Council Elections 2017";
    $url = "http://council17.mulvenna.org/results/";
    if (isset($_GET['year']) && isset($_GET['council']) && isset($_GET['ward']))
    {
        $url = "https://" . $_SERVER['SERVER_NAME'] . "/results/?year=" . $_GET['year'] . "&council=" . $_GET['council'] . "&ward=" . $_GET['ward'];
        if (isset($councils[$_GET['council']]) && isset($wards[$_GET['ward']]))
        {
            $desc = $wards[$_GET['ward']] . ", " . $councils[$_GET['council']] . " ward-level results visualisation for the council election " . $_GET['year'];
            $title = $desc;
        }
    }
    echo '<!DOCTYPE html>
<html>
<head>';
    echo"    <title>$title</title>\n";
    echo'
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.js"></script>

    <link rel="stylesheet" href="/website/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="/website/css/style.css" media="screen, handheld" />
    <link rel="stylesheet" type="text/css" href="/website/css/overview.css" media="screen, handheld" />
    <link rel="stylesheet" type="text/css" href="/website/css/enhanced.css" media="screen  and (min-width: 60.5em)" />
    <link rel="stylesheet" type="text/css" href="/website/css/stages.css" media="screen, handheld" />
    <link rel="stylesheet" type="text/css" href="/website/css/transfers.css" media="screen, handheld" />
    <link rel="stylesheet" type="text/css" href="/website/css/parties.css" media="screen, handheld" />

    <link rel="icon" type="image/png" href="/website/image/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/website/image/favicon-16x16.png" sizes="16x16" />
    <!--[if (lt IE 9)&(!IEMobile)]>
		<link rel="stylesheet" type="text/css" href="enhanced.css" />
		<![endif]-->
    <meta name="description" content="' . $desc . '" />
    <meta name="keywords" content="Scotland, local elections, open data, 2017, crowdsource, single transferable vote, stv, ward, candidate, voting, #council17, electoral"
    />
    <meta name="author" content="Gerry Mulvenna">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="1 month">
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@gerrymulvenna" />
    <meta name="twitter:creator" content="@gerrymulvenna" />
    <meta property="og:url" content="' . $url . '" />
    <meta property="og:title" content="' . $title . '" />
    <meta property="og:description" content="' . $desc . '" />
';
echo "    <meta property=\"og:image\" content=\"https://" . $_SERVER['SERVER_NAME'] . "$twimg\" />\n";
echo '

</head>
<body>

    <div id="wrap">
';
}

function results_content()
{
echo '        <div class="cta">
            <strong>Please note: official results are published on each council website upon full completion of the counts in all wards for that council.</strong>
            <div id="seats_summary" style="background-color: #ffffff; margin: auto; color: #212121; border-bottom: solid; border-bottom-width: 1px;"></div>
        </div>

        <div class="content">
            <div class="row">
                <h2>Single Transferable Vote (STV) animation</h2>
                <p>Choose election year, council, ward and then click play to run the animation.</p>
                <div id="menuBar">
                    <select id="yearSelect">
                        <option value="2017">2017</option>
                        <option value="2012">2012</option>
                    </select>
';
echo councilList("council-list-2017", "select", "2017", "../2017/SCO");
echo councilList("council-list-2012", "select", "2012", "../2012/SCO");

echo '
                    <select id="constituencySelect"></select>
                    <div id="mapLink"></div>
                </div>
            </div>

            <div id="stv" class="row">
                <div id="stageNumbers"></div>
                <div id="controls">
                    <a href="#Again" id="again" class="fa fa-step-backward"></a>
                    <a href="#Pause" id="pause-replay" class="fa fa-pause"></a>
                    <a href="#Next" id="step" class="fa fa-step-forward"></a>
                </div>
                <div id="quota"></div>
                <div id="animation"></div>
            </div>

            <div class="row">
                <div id="raw-data"></div>
            </div>
            <div id="results-ack">
                <p><em>The Single Transferable Vote (STV) animation was developed by James Bligh (<a href="http://twitter.com/anamates" target="_blank" title="External Link">@anamates</a>) and adapted by Bob Harper on <a href="http://electionsni.org" target="_blank" title="External Link">Elections NI</a>.</em><br>
                <em>Results data were collated by <a href="https://twitter.com/gerrymulvenna">@gerrymulvenna</a> (31 councils) and <a href="https://twitter.com/andrewteale">@andrewteale</a> (Scottish Borders).</em><br>
                <em>The full set of candidate data for #council17 was collated by</em></div><div id="dc-logo"><a href="http://democracyclub.org.uk/"><img src="/website/image/democracyclub-logo-with-text.png" width="250"></a>
            </div>
        </div>
    </div>
';
}

function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc = NULL, $twimg = '/website/image/scotland.png')
{
    global $councils, $wards;

    $desc = "Ward level candidates list for the Scottish Council Elections 2017";
    $url = $_SERVER['REQUEST_URI'];
    if (preg_match("/^\/councils\/(.+)\.php$/", $_SERVER['SCRIPT_NAME'], $matches))
    {
        if (isset($councils[$matches[1]]))
        {
            $desc = $councils[$matches[1]] . " council election candidates 2017";
            $title = $desc;
            if (isset($_GET['ward']))
            {
                if (isset($wards[$_GET['ward']]))
                {
                    $desc = $wards[$_GET['ward']] . ", " . $councils[$matches[1]] . " council election candidates 2017";
                    $title = $desc;
                }
            }
        }
    }
    echo '<!DOCTYPE html>
<html>
<head>';
    echo"    <title>$title</title>\n";
    echo'
    <meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://unpkg.com/leaflet@0.7.7/dist/leaflet.js"></script>
	<script src="https://cdn.rawgit.com/calvinmetcalf/leaflet-ajax/gh-pages/dist/leaflet.ajax.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.js"></script>
	<script src="/website/js/leaflet.pattern.js"></script>
    <script src="/website/js/jstree/jstree.min.js"></script>
  <script src="/website/js/jstree/jstree.types.js"></script>
  <script src="/website/js/jstree/jstree.search.js"></script>
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

  <link rel="stylesheet" href="https://unpkg.com/leaflet@0.7.7/dist//leaflet.css"/>
	<link rel="stylesheet" type="text/css" href="/website/css/style.css" media="screen, handheld" />
	<link rel="stylesheet" type="text/css" href="/website/css/parties.css" media="screen, handheld" />
	<link rel="stylesheet" type="text/css" href="/website/css/enhanced.css" media="screen  and (min-width: 50.5em)" />
    <link rel="stylesheet" href="/website/css/font-awesome.min.css">
	<link rel="stylesheet" href="/website/js/jstree/themes/default/style.min.css" />
	<link rel="icon" type="image/png" href="/website/image/favicon-32x32.png" sizes="32x32" />
	<link rel="icon" type="image/png" href="/website/image/favicon-16x16.png" sizes="16x16" />
		<!--[if (lt IE 9)&(!IEMobile)]>
		<link rel="stylesheet" type="text/css" href="enhanced.css" />
		<![endif]-->
    <meta name="description" content="' . $desc . '" />
    <meta name="keywords" content="Scotland, local elections, open data, 2017, crowdsource, single transferable vote, stv, ward, candidate, voting, #council17, electoral"
    />
    <meta name="author" content="Gerry Mulvenna">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="1 month">
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@gerrymulvenna" />
    <meta name="twitter:creator" content="@gerrymulvenna" />
    <meta property="og:url" content="' . $url . '" />
    <meta property="og:title" content="' . $title . '" />
    <meta property="og:description" content="' . $desc . '" />
';
echo "    <meta property=\"og:image\" content=\"https://" . $_SERVER['SERVER_NAME'] . "$twimg\" />\n";
echo '

</head>
<body>
	<div id="wrap">
';

}

function navigation($title, $param2 = NULL, $param3 = NULL, $param4 = NULL)
{
    echo"<header><h1><a href = \"/\">$title</a></h1><p>Explore results and candidates for #council17 in Scotland</p></header>\n";
    echo'
        <label for="show-menu" class="show-menu">Menu</label>
        <input type="checkbox" id="show-menu" role="button">
        <div id="cssmenu">
            <ul>
                <li><a href="/europarl/"><span>EP2019 (Ireland)</span></a></li>
                <li><a href="/results/"><span>STV animation</span></a></li>
                <li><a href="/treeview/results.php"><span>Data explorer</span></a></li>
                <li><a href="/councils/"><span>By map</span></a></li>
                <li><a href="/postcode/"><span>By postcode</span></a></li>
                <li><a href="/treeview/"><span>By council</span></a></li>
                <li><a href="/treeview/by-party.php"><span>By party</span></a></li>
                <li><a href="/download/"><span>Download</span></a></li>
                <li><a href="/about/"><span>About</span></a></li>
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
    			<div id="details">
';
echo "			<h2 id=\"breadcrumb\"><a href=\"/councils/\">Scotland</a> | <a href=\"$slug.php\">$council_name</a></h2>\n";
selectCouncil("Pick a different council from this list?");
echo '			<div id="wardinfo"><h5>Choose a ward the map to see the candidates</h5></div>
            <div id="electorate"></div>
            <div id="tabs-container">
                <ul class="tabs-menu">
                    <li class="current"><a id="ctab" href="#no_seats">Councillors</a></li>
                    <li><a href="#first_prefs">First Preferences</a></li>
                    <li><a href="#quotas">Quotas</a></li>
                </ul>
                <div class="tab">
                    <div id="no_seats" class="tab-content"></div>
                    <div id="first_prefs" class="tab-content"></div>
                    <div id="quotas" class="tab-content"></div>
                </div>
            </div>
			<div id="candidates"></div>
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
        <script src="/website/js/overview.js"></script>
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

function results_foot($param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
{
echo '
    <!--Load local scripts-->
    <script type="text/javascript" src="/website/js/results.js"></script>
    <script type="text/javascript" src="/website/js/stages2.js"></script>
';
  // Google analytics
echo "<script>
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

function selectCouncil ($prompt, $class = "select", $suffix = ".php")
{
echo'<div id="select-council">
				<select id="council-list" class="' . $class . '" onClick="selectCouncil()" name="council">';
echo "\n<option>$prompt</option>\n";
echo '					<option value="aberdeen-city' . $suffix . '">Aberdeen City</option>
					<option value="aberdeenshire' . $suffix . '">Aberdeenshire</option>
					<option value="angus' . $suffix . '">Angus</option>
					<option value="argyll-and-bute' . $suffix . '">Argyll And Bute</option>
					<option value="city-of-edinburgh' . $suffix . '">City Of Edinburgh</option>
					<option value="clackmannanshire' . $suffix . '">Clackmannanshire</option>
					<option value="dumfries-and-galloway' . $suffix . '">Dumfries And Galloway</option>
					<option value="dundee-city' . $suffix . '">Dundee City</option>
					<option value="east-ayrshire' . $suffix . '">East Ayrshire</option>
					<option value="east-dunbartonshire' . $suffix . '">East Dunbartonshire</option>
					<option value="east-lothian' . $suffix . '">East Lothian</option>
					<option value="east-renfrewshire' . $suffix . '">East Renfrewshire</option>
					<option value="falkirk' . $suffix . '">Falkirk</option>
					<option value="fife' . $suffix . '">Fife</option>
					<option value="glasgow-city' . $suffix . '">Glasgow City</option>
					<option value="highland' . $suffix . '">Highland</option>
					<option value="inverclyde' . $suffix . '">Inverclyde</option>
					<option value="midlothian' . $suffix . '">Midlothian</option>
					<option value="moray' . $suffix . '">Moray</option>
					<option value="eilean-siar' . $suffix . '">Na h-Eileanan An Iar</option>
					<option value="north-ayrshire' . $suffix . '">North Ayrshire</option>
					<option value="north-lanarkshire' . $suffix . '">North Lanarkshire</option>
					<option value="orkney-islands' . $suffix . '">Orkney Islands</option>
					<option value="perth-and-kinross' . $suffix . '">Perth And Kinross</option>
					<option value="renfrewshire' . $suffix . '">Renfrewshire</option>
					<option value="the-scottish-borders' . $suffix . '">Scottish Borders</option>
					<option value="shetland-islands' . $suffix . '">Shetland Islands</option>
					<option value="south-ayrshire' . $suffix . '">South Ayrshire</option>
					<option value="south-lanarkshire' . $suffix . '">South Lanarkshire</option>
					<option value="stirling' . $suffix . '">Stirling</option>
					<option value="west-dunbartonshire' . $suffix . '">West Dunbartonshire</option>
					<option value="west-lothian' . $suffix . '">West Lothian</option>
				</select>
			</div>';

}

// used on results page
function councilList ($id, $class, $name, $root)
{
    $councils = array(
    "aberdeen-city" => "Aberdeen City",
    "aberdeenshire"=> "Aberdeenshire",
    "angus" => "Angus",
    "argyll-and-bute" => "Argyll and Bute",
    "city-of-edinburgh" => "City of Edinburgh",
    "clackmannanshire" => "Clackmannanshire",
    "dumfries-and-galloway" => "Dumfries and Galloway",
    "dundee-city" => "Dundee City",
    "east-ayrshire" => "East Ayrshire",
    "east-dunbartonshire" => "East Dunbartonshire",
    "east-lothian" => "East Lothian",
    "east-renfrewshire" => "East Renfrewshire",
    "falkirk" => "Falkirk",
    "fife" => "Fife",
    "glasgow-city" => "Glasgow City",
    "highland" => "Highland",
    "inverclyde" => "Inverclyde",
    "midlothian" => "Midlothian",
    "moray" => "Moray",
    "eilean-siar" => "Eilean Siar",
    "north-ayrshire" => "North Ayrshire",
    "north-lanarkshire" => "North Lanarkshire",
    "orkney-islands" => "Orkney Islands",
    "perth-and-kinross" => "Perth and Kinross",
    "renfrewshire" => "Renfrewshire",
    "the-scottish-borders" => "The Scottish Borders",
    "shetland-islands" => "Shetland Islands",
    "south-ayrshire" => "South Ayrshire",
    "south-lanarkshire" => "South Lanarkshire",
    "stirling" => "Stirling",
    "west-dunbartonshire" => "West Dunbartonshire",
    "west-lothian" => "West Lothian");

    $html = "<select id=\"$id\" class=\"$class\" name=\"$name\">\n";
    $html .= "<option value=\"0\">Select a council</option>\n";
    $dirlist = scandir($root);
    foreach ($dirlist as $path)
    {
        if (!in_array($path,array(".","..")))
        {
            if (is_dir($root . DIRECTORY_SEPARATOR . $path) && in_array($path, array_keys($councils)))
            {
                $fname = $root . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . "all-constituency-info.json";
                if (file_exists($fname))
                {
                    $html .= '<option value="' . $path . '">' . $councils[$path] . '</option>' . "\n";
                }
            }
        }
    }
    $html .= "</select>\n";
    return ($html);
}   


?>