<?php
require "common.php";
require "functions.php";
$slug = 'add-result';

add_count_head("unofficial data entry for European Parliament elections (Ireland)", $slug, "/website/image/results.png");
navigation("European Parliament elections (Ireland)");
?>
        <div class="cta">
            <div id="seats_summary" style="background-color: #ffffff; margin: auto; color: #212121; border-bottom: solid; border-bottom-width: 1px;"></div>
        </div>

        <div class="content">
            <form method="POST" action="update.php">
                <h2>Parse some count data</h2>
                <div class="row">
                    <p>Choose a state, region and election year.</p>
                    <div id="menuBar">
<?php
selectCouncil('Select an EU member state', 'add-form', '');
?>
                        <select id="wardSelect" class="add-form" name="ward"><option>Regions appear when state selected</option></select>
                        <select id="yearSelect" class="add-form" name="year">
                            <option>2019</option>
                            <option>2014</option>
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
<h4>Data entry notes</h4>
<ol>
<li>Paste data as tab-delimited array of numbers, so prepare the results array in a spreadsheet</li>
<li>There must be an odd number of columns, beginning with the first preferences, then a pair of transfers&lt;TAB&gt;totals for subsequent counts<br>
i.e. <em>Count1&lt;TAB&gt;Transfers2&lt;TAB&gt;Count2&lt;TAB&gt;Transfers3&lt;TAB&gt;Count3&lt;TAB&gt;Transfers4&lt;TAB&gt;Count4&lt;TAB&gt;Transfers5&lt;TAB&gt;Count5 etc.</em></li>
<li>The number of rows must equal the number of candidates</li>
<li>The rows will be allocated to candidates, sorted in order of <em>surname, firstname</em></li>
<li>Enter explicit zeros rather than blanks</li>
<li>Commas can be included, as they will be ignored</li>
<li>Look out for surnames, which include spaces or accented characters. These should be replaced in the candidate data file with html entities, e.g. &amp;nb; or &amp;iacute;</li>
<li>If you need to make corrections to the results data or amend the names of candidates, it is best to 
<ul>
<li>load up the existing data in this page by selecting Member state, Region and Year,</li>
<li>then delete the results data file and</li>
<li>submit an entire new array into the form above.</li>
</ul>
In this way the electorate and voting data are preserved.</li>
</ol>
<h4>Results data file</h4>
<div id="resultspath"></div>
<h4>Summary data file</h4>
<div id="summarypath"></div>
<h4>Candidate data file</h4>
<div id="candidatepath"></div>
<h4>Ward data file</h4>
<div id="wardpath"></div>
        </div>
</div>

<!--Load local scripts-->
<script type="text/javascript" src="js/europarl.js"></script>
<script type="text/javascript" src="js/euro_add_count.js"></script>

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

?>