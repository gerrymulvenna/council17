<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";

// --------- council specific variables in this section --------
// this should be the council identifier consistent with Democracy Club data, this website and to a certain extent the map data
// used in the mapName js variable, the twitter image, the breadcrumb link
$slug = 'about';
$council_name = 'About this site';  // used in the title and breadcrumb

// ------ below here should be the same for each council (but this is the top-level, so it has different content --------


// function head($title, $mapName, $mapLat, $mapLong, $mapZoom, $mapProperty, $mapUnit, $mapWardDesc, $twimg)
head("#council17 $council_name - Map-based interface to crowd-sourced data for the Scottish Council elections 2017", $slug, NULL, NULL, NULL, NULL, NULL);
navigation("Scottish Council elections 2017");

echo'<div class="content">
<div id="about" class="about">
<a href="http://democracyclub.org.uk"><img src="/website/image/scotland.png" height="200"></a>
<h3>About this site</h3>
<p>In Scotland, the Scottish Parliament has <a href="http://www.parliament.scot/parliamentarybusiness/CurrentCommittees/101840.aspx">responsibility to oversee the local government elections</a>, but there are very few joined-up resources to look at the council elections as a whole, 
as each council runs their own election and publishes data on their own website. The <a href="http://www.electionsscotland.info/emb/homepage/2/about_us">Electoral Management Board</a> administrates the local government election process in Scotland and does provide some useful data.</p>
<p>However this site is one citizen&ksquo;s effort to provide a map-based interface to the data across all wards in Scotland. It uses the
crowd-sourced data extracted from the SOPNs (Statement of Persons Notified) produced as PDFs by individual councils.</p>
<p>This site is made possible by those users at Democracy Club who have given their time to manually enter all the candidate information from each SOPN published by the councils. Hopefully this interface to the data
will make a valuable contribution to this election process.</p>
</div>

<div id="author" class="about">
<a href="http://twiter.com/gerrymulvenna"><img src="/website/image/gerry-patlee.png" height="200"></a>
<h3>About the author, Gerry Mulvenna</h3>
<p>This site is provided without any statutory capacity. It has been put together by Gerry Mulvenna, a programmer living in Edinburgh, who wanted to help other voters reach the necessary information to cast their votes in the #council17 elections.
Gerry believes that STV (Single Transferable Vote) is the best available voting system and would like to see it introduced for all the elections in Scotland. He hopes that an improved voter engagement in these council elections will increase familiarity with STV among the voters of Scotland. </p>
Find Gerry Mulvenna at <a href="http://twiter.com/gerrymulvenna">@gerrymulvenna on Twitter</a>
</div>

<div id="electionsni" class="about">
<a href="http://electionsni.org"><img src="/website/image/favicon-192x192.png" height="200"></a>
<h3>Credit to @electionsNI</h3>
<p>This site took its starting point from the work carried out by Bob Harper (<a href="https://twitter.com/bobdata">@bobdata on twitter</a>) for the #AE17 assembly election in Northern Ireland, which is available at <a href="http://electionsni.org">http://electionsni.org</a></p>
</div> 

<div id="candidate-data" class="about">
<a href="http://democracyclub.org.uk"><img src="https://democracyclub.org.uk/static/dc_theme/images/logo-with-text-2017.png" width="300"></a>
<h3>About the candidate data</h3>
<p>The candidate data is crowdsourced by the Democracy Club. For more details on the Democracy Club data see <a href="https://candidates.democracyclub.org.uk/help/api">https://candidates.democracyclub.org.uk/help/api</a>.</p>
</div>

<div id="map-data" class="about">
<img src="/website/image/argyll-and-bute.png" height="200">
<h3>About the map data</h3>
<ol>
<li>Map data came primarily from the <a href="http://www.lgbc-scotland.gov.uk/maps/datafiles/index_1995_on.asp">Local Government Boundary Commission for Scotland</a> with the top-level Scottish council boundaries coming from the Ordnance Survey <a href="https://www.ordnancesurvey.co.uk/business-and-government/help-and-support/products/boundary-line.html">boundary line</a> package.</li>
<li>The open source application <a href="http://www.qgis.org/en/site/forusers/download.html">QGIS</a> was used to convert the shapefiles into GEOJSON format with latitute longitude coordinates (EPSG:4326)</li>
<li>The Map Shaper application was invaluable for simplifying the GEOJSON data to achieve the required level of detail for our purpose and to drastically reduce the size of the boundary
Map Shaper is a very efficient and easy to use online application at <a href="http://mapshaper.org/">http://mapshaper.org/</a>.</li>
</ol>
</div>



		</div>';
foot();
?>