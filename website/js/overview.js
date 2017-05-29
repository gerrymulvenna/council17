// display summary party data in a bar chart for a given year
// primary is the main variable, sorted by primary, secondary desc
// singular and plural are suffix string descriptors for the variable
// refvar is the top-level variable from which a percentage can be derived, leave blank if no percentage appropriate
// target_div is the div ID (with # prefix) in which the bar chart is rendered
function overview_by_var(year, dir, primary, secondary, singular, plural, refvar, target_div) {
	$('#tabs-container').css('display', 'block');
    $(target_div).html("");
	var target = target_div.substr(1);   // strip the first char (#) from target_div so we can use it as identifier segment
	var speed = 1;
    var leftPadding = 10;
    var nameSpace = 55;
    var startLeft = leftPadding+nameSpace;
    var voteWidth = 325;
	if (startLeft + voteWidth > screen.width - 40)
	{
		voteWidth = screen.width - startLeft - 40;
		$('.tabs-menu li').css('margin-right', '4px');
		$('#details').css('width', (startLeft + voteWidth + 10) + 'px');
	}
    var topMargin = 0;
	var barHeight = 30;

    var json = (function() {
            var json = null;
            $.ajax({
                'async': false,
                'global': false,
                'url': '/' + year + '/' + dir + '/overview.json',
                'dataType': "json",
                'success': function (data) {
                    json = data;
                },

            })
            .fail(function(e){console.log('failed log', e)});
            return json;
        })();
    if(json.parties.length){
		$('#ctab').text("Councillors (" + numberWithCommas(parseInt(json.no_seats,10)) + ")");
		var max = 0;
		var parties = [];
		var rankings = [];
		var rank = 0;
		// exclude parties without seats, store ranking by var descending, calculate max value
		$.each(json.parties.sort(cmpPrimary), function(index, element) {
			if (parseFloat(element[primary]) > 0)
			{	
				if (parseFloat(element[primary]) > max)
				{
					max = parseFloat(element[primary]);
				}
				rankings[element.short.toUpperCase()] = rank++;
				parties.push(element);
			}
		});
		// starting order: sort by short ascending
		parties = parties.sort(cmpShort);
        //set the top right bit
        var seats = parseInt(json.no_seats);
        var turnout = ((parseInt(json.total_poll)/parseInt(json.electorate)) * 100).toFixed(2);
		var rej_pc = ((parseInt(json.total_poll - json.valid_poll)/parseInt(json.total_poll)) * 100).toFixed(2);
        $("#electorate").html("<p>Electorate: " + numberWithCommas(parseInt(json.electorate)) + ", turnout: " + numberWithCommas(parseInt(json.total_poll)) + " (" + turnout + "%),<br>valid votes: " + numberWithCommas(parseInt(json.valid_poll)) + ", rejected: " + numberWithCommas(json.total_poll - json.valid_poll) + " (" + rej_pc + "%)</p>\n");
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
		if (parseFloat(a[primary]) == parseFloat(b[primary]))
		{
			if (parseFloat(a[secondary]) == parseFloat(b[secondary]))
			{
				return (0);
			}
			return ((parseFloat(a[secondary]) > parseFloat(b[secondary])) ? -1: 1);
		}
		return ((parseFloat(a[primary]) > parseFloat(b[primary])) ? -1: 1);
	}

	//the magic, simple enough, append some divs and animate their width's to final position
    //then animate their top to final position and move the name div at the same time
    function displayOverview(){
		$(target_div).height(parties.length*barHeight);
		$(target_div).html("");
        for(var j=0;j<parties.length;j++){
             $('<div id="' + target + '_name_' + parties[j].short.toUpperCase() + '" class="partyLabel" title="' + parties[j].name + '" style="top:' + (topMargin + (j*barHeight)) + 'px;left:10px;">' + parties[j].short + '</div>')
            .appendTo(target_div);
            $('<div data-' + target + '="' + parties[j].short.toUpperCase() + '" id="' + target + '_number_' + parties[j].short.toUpperCase() + '" class="no-seats ' + parties[j].name +'" style="top:' + (topMargin + j*barHeight) +'px;left:'+ startLeft +'px;"></div>')
            .appendTo(target_div).text(parseFloat(parties[j][primary]))
            .animate( {width:parties[j][primary] * qFactor}, {duration:1500*speed, complete:rankParties});
        }
    }

	function appendSuffix()
	{
		var num = parseFloat($(this).text());
		suffix = (num == 1) ? " " + singular : " " + plural;
		share = (json.hasOwnProperty(refvar)) ? " (" + Math.round(num * 1000 / json[refvar]) / 10 + "%)" : "";
		$(this).text(numberWithCommas(num) + suffix + share);
	}

	function rankParties()
	{
		var short = $(this).data(target);
		$('#' + target + '_name_'  + short).animate({top:topMargin+(rankings[short]*barHeight)},500*speed);
		$('#' + target + '_number_' + short).animate({top:topMargin+(rankings[short]*barHeight)},500*speed, appendSuffix);
	}

}

$(document).ready(function() {
    $(".tabs-menu a").click(function(event) {
		var dir = (mapName == "scotland") ? 'SCO' : 'SCO/' + mapName;
        event.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
		$(tab).html("");
        $(tab).fadeIn(400, function(){
			switch(tab)
			{
				case '#no_seats':
					overview_by_var(2017, dir, 'no_seats', 'first_prefs', 'councillor', 'councillors', 'no_seats', '#no_seats');
					break;
				case '#first_prefs':
					overview_by_var(2017, dir, 'first_prefs', 'no_seats', 'first pref', 'first prefs', 'valid_poll', '#first_prefs');
					break;
				case '#quotas':
					overview_by_var(2017, dir, 'quotas', 'no_wards', 'quota per ward', 'quotas per ward', '', '#quotas');
					break;
			}
		});
    });
});
