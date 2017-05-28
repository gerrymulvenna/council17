function overview_by_var(year, max, primary, secondary, singular, plural) {
    $("#overview").html("");

	var speed = 1;
    var leftPadding = 10;
    var nameSpace = 55;
    var startLeft = leftPadding+nameSpace;
    var voteWidth = 325;
	if (startLeft + voteWidth > screen.width)
	{
		voteWidth = screen.width - startLeft;
	}
    var postPosition = leftPadding + nameSpace + voteWidth;
    var topMargin = 0;
	var barHeight = 30;

    var json = (function() {
            var json = null;
            $.ajax({
                'async': false,
                'global': false,
                'url': '/' + year + '/SCO/overview.json',
                'dataType': "json",
                'success': function (data) {
                    json = data;
                },

            })
            .fail(function(e){console.log('failed log', e)});
            return json;
        })();
    if(json.parties.length){
		var parties = [];
		var rankings = [];
		var rank = 0;
		// exclude parties without seats, store ranking by var descending
		$.each(json.parties.sort(cmpPrimary), function(index, element) {
			if (element[primary] > 0)
			{	
				rankings[element.short] = rank++;
				parties.push(element);
			}
		});
		// starting order: sort by short ascending
		parties = parties.sort(cmpShort);
        //set the top right bit
        var seats = parseInt(json.no_seats);
        var turnout = ((parseInt(json.total_poll)/parseInt(json.electorate)) * 100).toFixed(2);
		var rej_pc = ((parseInt(json.total_poll - json.valid_poll)/parseInt(json.total_poll)) * 100).toFixed(2);
        $("#quota").html("<p>Electorate: " + numberWithCommas(parseInt(json.electorate)) + ", turnout: " + numberWithCommas(parseInt(json.total_poll)) + " (" + turnout + "%),<br>valid votes: " + numberWithCommas(parseInt(json.valid_poll)) + ", rejected: " + numberWithCommas(json.total_poll - json.valid_poll) + " (" + rej_pc + "%)</p>\n");
        $("#seats-span").text(seats);
        var qFactor = voteWidth/max; //all seat counts are multiplied by this to get a div width in proportion

        displayOverview();  //show the animated bar chart
    }

	function cmpShort(a, b)
	{
		if (a.short.toUpperCase() == b.short.toUpperCase())
		{
			return (0);
		}
		return ((a.short.toUpperCase() > b.short.toUpperCase()) ? 1: -1);
	}

	function cmpPrimary(a, b)
	{
		if (a[primary] == b[primary])
		{
			if (a[secondary] == b[secondary])
			{
				return (0);
			}
			return ((a[secondary] > b[secondary]) ? -1: 1);
		}
		return ((a[primary] > b[primary]) ? -1: 1);
	}

	//the magic, simple enough, append some divs and animate their width's to final position
    //then animate their top to final position and move the name div at the same time
    function displayOverview(){
		$("#overview").height(parties.length*barHeight);
        for(var j=0;j<parties.length;j++){
             $('<div id="cname'+parties[j].short+'" class="partyLabel" title="' + parties[j].name + '" style="top:' + (topMargin + (j*barHeight)) + 'px;left:10px;">' + parties[j].short + '</div>')
            .appendTo("#overview");
            $('<div data-candidate="'+parties[j].short+'" id="candidate' + parties[j].short+'" class="no-seats ' + parties[j].name +'" style="top:' + (topMargin + j*barHeight) +'px;left:'+ startLeft +'px;"></div>')
            .appendTo("#overview").text(parties[j][primary])
            .animate( {width:parties[j][primary] * qFactor}, {duration:1500*speed, complete:rankParties});
        }
    }

	function appendSuffix()
	{
		suffix = ($(this).text() == "1") ? " " + singular : " " + plural;
		share = " (" + Math.round(parseInt($(this).text()) * 1000 / json[primary]) / 10 + "%)";
		$(this).text($(this).text() + suffix + share);
	}

	function rankParties()
	{
		$("#candidate"+$(this).data('candidate')).animate({top:topMargin+(rankings[$(this).data('candidate')]*barHeight)},500*speed, appendSuffix);
		$("#cname"+$(this).data('candidate')).animate({top:topMargin+(rankings[$(this).data('candidate')]*barHeight)},500*speed);
	}

}
