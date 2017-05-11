<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";
require $_SERVER["DOCUMENT_ROOT"] . "/scripts/functions.php";
$slug = 'add-result';

add_count_head("#council17 unofficial data entry for Scottish Council elections", $slug, "/website/image/scotland.png");
navigation("Scottish Council elections 2017");
?>
        <div class="cta">
            <strong>Please note: official results are published on each council website upon full completion of the counts in all wards for that council.</strong>
            <div id="seats_summary" style="background-color: #ffffff; margin: auto; color: #212121; border-bottom: solid; border-bottom-width: 1px;"></div>
        </div>

        <div class="content">
            <form method="POST" action="update.php">
                <h2>Parse some count data</h2>
                <div class="row">
                    <p>Choose a council, ward and election year.</p>
                    <div id="menuBar">
<?php
echo "<!-- " . $_SERVER['SERVER_ADDR'] . "-->\n";
selectCouncil('Select a council', 'add-form', '');
?>
                        <select id="wardSelect" class="add-form" name="ward"><option>Wards appear when council selected</option></select>
                        <select id="yearSelect" class="add-form" name="year">
                            <option>2017</option>
                            <option>2012</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div id="wardinfo">
                      <div id="wardvars"></div>
                      <label for="electorate">Electorate</label>
                      <input type="text" name="electorate" id="electorate" size="10">
                      <label for="total_poll">Total poll</label>
                      <input type="text" name="total_poll" id="total_poll" size="10">
                      <label for="valid_poll">Valid poll</label>
                      <input type="text" name="valid_poll" id="valid_poll" size="10">
                      <label for="seats">Seats</label>
                      <input type="text" name="seats" id="seats" maxlength="1" size="2">
                      <label for="turnout">Turnout %</label>
                      <input type="text" name="turnout" id="turnout" size="10">
                    </div>
                </div>
                <div class="row">
                    <div id="candidates"></div>
                    <div id="data-entry"><textarea id="pastebin" name="pastebin" rows="6" cols="50" placeholder="Paste data here"></textarea></div><br>
                                          <input type="submit" value="Submit">

                </div>
            </form>
        </div>
        <div id="promotional">
<?php
if (isset($_GET['council']))
{
    echo "<h3>Potential tweets</h3>\n";
    showTweets('../2017/SCO/', $_GET['council']);
}
?>

        </div>
</div>

<!--Load local scripts-->
<script type="text/javascript" src="/website/js/results.js"></script>
<script type="text/javascript" src="/website/js/add_count.js"></script>

<!--google analytics-->
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
<?php

function showTweets($dataDir, $council)
{
    $wardinfo = readJSON($dataDir . "wardinfo.json");
    $wards = array();
    foreach ($wardinfo->Wards as $ward)
    {
        if (!empty($ward->election))
        {
            $wards[$ward->cand_ward_code] = $ward;
        }
    }

    $cdata = readJSON("$dataDir" . "local." . $council . ".2017-05-04.json");
    foreach($cdata->wards as $ward)
    {
        $wdata = $wards[$ward->post_id];
        $url = "http://council17.mulvenna.org/results/?year=2017&council=$council&ward=" . $wdata->map_ward_code;
        $tweet = "#council17 #dataviz for " . $wdata->ward_name . ", " . $wdata->council ;
        $ctwitter = array();
        foreach($ward->candidates as $cdata)
        {
            if ($cdata->twitter_username)
            {
                $ctwitter[] = "@" . $cdata->twitter_username;
            }
        }
        if (count($ctwitter))
        {
            $tweet .= " " . implode(' ', $ctwitter);
        }
        $tweet .= " $url";
        echo "$tweet<br>\n";
    }
}

?>