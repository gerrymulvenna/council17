<?php
require $_SERVER["DOCUMENT_ROOT"] . "/website/php/functions.php";
$slug = 'add-result';
add_count_head("#council17 unofficial data entry for Scottish Council elections", $slug, "/website/image/scotland.png");
navigation("Scottish Council elections 2017");
?>
        <div class="cta">
            <strong>Please note: official results are published on each council website upon full completion of the counts in all wards for that council.</strong>
            <div id="seats_summary" style="background-color: #ffffff; margin: auto; color: #212121; border-bottom: solid; border-bottom-width: 1px;"></div>
        </div>

        <div class="content">

            <div class="row">
                <h2>Parse some count data</h2>
                <p>Choose a council, ward and election year.</p>
                <form method="POST" action="update.php">
                    <div id="menuBar">
<?php
selectCouncil('Select a council', 'add-form', '');
?>
                        <select id="wardSelect" class="add-form"><option>Wards appear when council selected</option></select>
                        <select id="yearSelect" class="add-form">
                            <option>2017</option>
                            <option>2012</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
</div>

<!--Load local scripts-->
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
