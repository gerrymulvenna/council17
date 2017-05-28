function overview_by_seats(year, max) {
    $("#overview").html("");

	var speed = 1;
    var leftPadding = 10;
    var nameSpace = 55;
    var startLeft = leftPadding+nameSpace;
    var voteWidth = 325; // default = 600
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
		// exclude parties without seats, store ranking by no_seats descending
		$.each(json.parties.sort(cmpSeats), function(index, element) {
			if (element.no_seats > 0)
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
        $("#quota").html("<p>Electorate: " + numberWithCommas(parseInt(json.electorate)) + ", turnout: " + numberWithCommas(parseInt(json.total_poll)) + " (" + turnout + "%), valid votes: " + numberWithCommas(parseInt(json.valid_poll)) + ", rejected: " + numberWithCommas(json.total_poll - json.valid_poll) + " (" + rej_pc + "%)</p>\n");
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

	function cmpSeats(a, b)
	{
		if (a.no_seats == b.no_seats)
		{
			if (a.first_prefs == b.first_prefs)
			{
				return (0);
			}
			return ((a.first_prefs > b.first_prefs) ? -1: 1);
		}
		return ((a.no_seats > b.no_seats) ? -1: 1);
	}

	//the magic, simple enough, append some divs and animate their width's to final position
    //then animate their top to final position and move the name div at the same time
    function displayOverview(){
		$("#overview").height(parties.length*barHeight);
        for(var j=0;j<parties.length;j++){
             $('<div id="cname'+parties[j].short+'" class="partyLabel" style="top:' + (topMargin + (j*barHeight)) + 'px;left:10px;">' + parties[j].short + '</div>')
            .appendTo("#overview");
            $('<div data-candidate="'+parties[j].short+'" id="candidate' + parties[j].short+'" class="votes ' + parties[j].name +'" style="top:' + (topMargin + j*barHeight) +'px;left:'+ startLeft +'px;"></div>')
            .appendTo("#overview")
            .animate({width:parties[j].no_seats * qFactor},1500*speed).text(parties[j].no_seats)
			.animate({top:(topMargin + j*barHeight)},{duration:500*speed
				,start:function(){
                    $("#candidate"+$(this).data('candidate')).animate({top:topMargin+(rankings[$(this).data('candidate')]*barHeight)},500*speed)
                    $("#cname"+$(this).data('candidate')).animate({top:topMargin+(rankings[$(this).data('candidate')]*barHeight)},500*speed)
                }
            });
        }
    }
}
