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
		var parties = json.parties;
        //set the top right bit
        var seats = parseInt(json.no_seats);
        var turnout = ((parseInt(json.total_poll)/parseInt(json.electorate)) * 100).toFixed(2);
		var rej_pc = ((parseInt(json.total_poll - json.valid_poll)/parseInt(json.total_poll)) * 100).toFixed(2);
        $("#quota").html("<p>Electorate: " + numberWithCommas(parseInt(json.electorate)) + ", turnout: " + numberWithCommas(parseInt(json.total_poll)) + " (" + turnout + "%), valid votes: " + numberWithCommas(parseInt(json.valid_poll)) + ", rejected: " + numberWithCommas(json.total_poll - json.valid_poll) + " (" + rej_pc + "%)</p>\n");
        $("#seats-span").text(seats);
        var qFactor = voteWidth/max; //all seat counts are multiplied by this to get a div width in proportion


        firstCount();  //run the first count
    }

    //the magic, simple enough, append some divs and animate their width's to final position
    //then animate their top to final position and move the name div at the same time
    function firstCount(){
		$("#overview").height(parties.length*barHeight);
        for(var j=0;j<parties.length;j++){
             $('<div id="cname'+parties[j].short+'" class="partyLabel" style="top:' + (topMargin + (j*barHeight)) + 'px;left:10px;">' + parties[j].short + '</div>')
            .appendTo("#overview");
            $('<div data-candidate="'+parties[j].short+'" id="candidate' + parties[j].short+'" class="votes ' + parties[j].name +'" style="top:' + (topMargin + j*barHeight) +'px;left:'+ startLeft +'px;"></div>')
            .appendTo("#overview")
            .animate({width:parties[j].no_seats * qFactor},1500*speed).text(parties[j].no_seats + " councillors")
            .animate({top:(topMargin + j*barHeight)},{
                duration:500*speed,
                start:function(){
//                    $("#cname"+$(this).data('candidate')).animate({top:topMargin+(j*barHeight)},500*speed)
                }
            });
        }
    }
}
