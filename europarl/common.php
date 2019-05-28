<?php

// maps each region slug to a region name
$regions = array(
"dublin" => "Dublin",
"midlands-northwest"=> "Midlands/North-west",
"south" => "South",
"ni" => "Northern Ireland");


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
    <meta name="description" content="STV animation for European Parliament elections (Ireland)" />
    <meta name="keywords" content="Ireland, European Parliament elections, open data, 2019, 2014, single transferable vote, stv, , #ep19, electoral"
    />
    <meta name="author" content="Gerry Mulvenna">
    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="1 month">
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@gerrymulvenna" />
    <meta name="twitter:creator" content="@gerrymulvenna" />
    <meta property="og:url" content="http://council17.mulvenna.org/europarl/" />
    <meta property="og:title" content="European Parliament elections (Ireland)" />
    <meta property="og:description" content="Presenting crowdsourced open data, live results and data visualisations for the European Parliament elections (Ireland)" />
';
echo "    <meta property=\"og:image\" content=\"http://" . $_SERVER['SERVER_NAME'] . "$twimg\" />\n";
echo '

</head>
<body>

    <div id="wrap">
';
}

function europarl_head($title, $name, $twimg)
{
    global $councils, $wards;

    $desc = "Presenting results and data visualisations for the European Parliament Elections (Ireland)";
    $url = "https://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']);
    if (isset($_GET['year']) && isset($_GET['council']) && isset($_GET['ward']))
    {
        $url = "https://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']) . "/?year=" . $_GET['year'] . "&council=" . $_GET['council'] . "&ward=" . $_GET['ward'];
        if (isset($councils[$_GET['council']]) && isset($wards[$_GET['ward']]))
        {
            $desc = $wards[$_GET['ward']] . ", " . $councils[$_GET['council']] . " results visualisation for the European Parliament election " . $_GET['year'];
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

    <link rel="stylesheet" href="css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" media="screen, handheld" />
    <link rel="stylesheet" type="text/css" href="css/overview.css" media="screen, handheld" />
    <link rel="stylesheet" type="text/css" href="css/enhanced.css" media="screen  and (min-width: 60.5em)" />
    <link rel="stylesheet" type="text/css" href="css/stages.css" media="screen, handheld" />
    <link rel="stylesheet" type="text/css" href="css/transfers.css" media="screen, handheld" />
    <link rel="stylesheet" type="text/css" href="css/parties.css" media="screen, handheld" />

    <link rel="icon" type="image/png" href="/website/image/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/website/image/favicon-16x16.png" sizes="16x16" />
    <!--[if (lt IE 9)&(!IEMobile)]>
		<link rel="stylesheet" type="text/css" href="css/enhanced.css" />
		<![endif]-->
    <meta name="description" content="' . $desc . '" />
    <meta name="keywords" content="Ireland, European Parliament elections, open data, 2019, 2014, single transferable vote, stv, candidate, voting, #ep19, electoral"
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
echo "    <meta property=\"og:image\" content=\"https://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']) . "/$twimg\" />\n";
echo '

</head>
<body>

    <div id="wrap">
';
}

function europarl_content()
{
echo '        <div class="cta">
            <div id="seats_summary" style="background-color: #ffffff; margin: auto; color: #212121; border-bottom: solid; border-bottom-width: 1px;"></div>
        </div>

        <div class="content">
            <div class="row">
                <h2>Single Transferable Vote (STV) animation</h2>
                <p>Choose election year, region and then click play to run the animation.</p>
                <div id="menuBar">
                    <select id="yearSelect">
                        <option value="2019">2019</option>
                        <option value="2014">2014</option>
                    </select>
';
echo councilList("council-list-2019", "select", "2019", "../2019/EU");
echo councilList("council-list-2014", "select", "2014", "../2014/EU");

echo '
                    <select id="constituencySelect"></select>
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
                <em>Results data and Irish candidate data were collated by <a href="https://twitter.com/gerrymulvenna">@gerrymulvenna</a>.</em><br>
                <em>The UK candidate data was collated by</em></div><div id="dc-logo"><a href="http://democracyclub.org.uk"><img src="democracyclub-logo-with-text.png" width="250"></a>
            </div>
        </div>
    </div>
';
}


function navigation($title, $param2 = NULL, $param3 = NULL, $param4 = NULL)
{
    echo"<header><h1><a href = \"index.php\">$title</a></h1><p>Explore results of the European Parliament election in Ireland</p></header>\n";
    echo'
        <label for="show-menu" class="show-menu">Menu</label>
        <input type="checkbox" id="show-menu" role="button">
        <div id="cssmenu">
            <ul>
                <li><a href="/results/"><span>STV animation</span></a></li>
            </ul>
        </div>';
}


function europarl_foot($param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
{
echo '
    <!--Load local scripts-->
    <script type="text/javascript" src="js/europarl.js"></script>
    <script type="text/javascript" src="js/euro-stages.js"></script>
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
echo '					<option value="uk' . $suffix . '">United Kingdom</option>
					<option value="ireland' . $suffix . '">Republic of Ireland</option>
				</select>
			</div>';

}

// used on results page
function councilList ($id, $class, $name, $root)
{
    $councils = array(
    "uk" => "United Kingdom",
    "ireland"=> "Republic of Ireland");

    $html = "<select id=\"$id\" class=\"$class\" name=\"$name\">\n";
    $html .= "<option value=\"0\">Select an EU member state</option>\n";
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