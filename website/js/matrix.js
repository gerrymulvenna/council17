var council = "city-of-edinburgh";
		
		$(document).ready(function() {
            $.ajaxSetup({
                cache: false
            });
        });

        seatsSummary();

        // tooltip
        $(document).bind('mousemove', function(e) {
            $('#matrixtooltip').css({
                "left": e.pageX + 20,
                "top": e.pageY
            });
        });

	// function to populate wards for a given council
	$("#council-list-2017").change(function()
	{
		manageChanges($(this));
	});

	// function to populate wards for a given council
	$("#council-list-2012").change(function()
	{
		manageChanges($(this));
	});

	$('#yearSelect').change(function() {
		var year = $("#yearSelect :selected").text();
		switch(year)
		{
			case '2012':
				$("#council-list-2017").hide();
				$("#council-list-2012").show();
				break;
			case '2017':
				$("#council-list-2012").hide();
				$("#council-list-2017").show();
				break;
		}
		var council = $("#council-list-" + year + " :selected").val();
		var constituencyFolder = $("#constituencySelect :selected").val();

		//getTransfersData(year);
		//countMatrix(year, council, constituencyFolder);
		animateStages(year, council, constituencyFolder);
	});

	function cmpNames(a, b)
	{
		if (a.Constituency_Name < b.Constituency_Name) 
		{
			return -1;
		}
		if (a.Constituency_Name > b.Constituency_Name) 
		{
			return 1;
		}
		// a must be equal to b
		return 0;
	}

	function manageChanges(cSelect)
	{
        var year = $("#yearSelect :selected").text();
		var council = cSelect.val();
		loadWards(year, council);
		var constituencyFolder = $("#constituencySelect :selected").val();
		var year = $("#yearSelect :selected").text();
		getTransfersData(year);
		//countMatrix(year, council, constituencyFolder);
		animateStages(year, council, constituencyFolder);
	}

// populate the ward select based on year and council
function loadWards(year, council, selected)
{
	console.log ("loadWards", year, council, selected);
	var path = '/' + year + "/SCO/" + council + "/all-constituency-info.json"; 
	$.getJSON(path, function(data) 
	{
		var constituencies = data.Constituencies.sort(cmpNames);
		var constituencySelect = $("#constituencySelect");
		constituencySelect.empty();
		var yearSelect = $("#yearSelect");
		$.each(constituencies, function(i, constituency)
		{
			var name = constituency.Constituency_Name;
			var folder = constituency.Directory;
			$(constituencySelect).append($("<option/>").val(folder).text(name))
		});
		if (selected)
		{
			$(constituencySelect).val(selected);
		}
		$("#constituencySelect").change(function()
		{
			var year = $("#yearSelect :selected").text();
			var council = $("#council-list-" + year + " :selected").val();
			var constituencyFolder = $("#constituencySelect :selected").val();
			//countMatrix(year, council, constituencyFolder);
			animateStages(year, council, constituencyFolder);
		});
	})
}

// create data for summary header
function seatsSummary() {
    $.ajax({
        'async': false,
        'global': false,
        'url': "/2017/NI/all-elected-d3.json",
        'dataType': "json",
        'success': function(data) {
            var result = _.fromPairs(_.sortBy(_.toPairs(_.countBy(_.map(data, 'Party_Name'))), function(a) {
                return a[1]
            }).reverse());
            _.forEach(result, function(value, key) {
                document.getElementById("seats_summary").innerHTML += '<span class="' + key.replace(/\s+/g, "-") + '">&nbsp;&nbsp;&nbsp;</span>&nbsp;<span class="' + key.replace(/\s+/g, "-") + '_abbr ' + '"></span>' + ': ' + value + '&nbsp;';
            })
        }
    });
}

// function to retrive vega spec to populate count matrix //
function countMatrix(year, council, directory) {
    $.get("/website/jsonspec/countSpec.json", function(json) {
        var spec = JSON5.parse(json);
        spec.data[0].url = '/' + year + '/SCO/' + council + "/" + directory + '/Count.csv'; // needed to dynamically change the data url in spec to our desired path
        vg.parse.spec(spec, function(chart) {
            var view = chart({
                    el: "#count_matrix"
                })
                .on("mouseover", function(event, item) {
                    if (item && item.datum.Surname && item.datum.Status) {
                        $('#matrixtooltip').show();
                        $('#matrixtooltip').html(
                            "<b>" + item.datum.Firstname + ' ' + item.datum.Surname + "</b><br/>" +
                            item.datum.Party_Name + "<br/>" +
                            item.datum.Status + ' on count ' + item.datum.Occurred_On_Count
                        );
                    } else if (item && item.datum.Surname) {
                        $('#matrixtooltip').show();
                        $('#matrixtooltip').html(
                            "<b>" + item.datum.Firstname + ' ' + item.datum.Surname + "</b><br/>" +
                            item.datum.Party_Name
                        );
                    } else {
                        $('#matrixtooltip').hide();
                    }
                })
                .update();
        });
    }, "text");
}

// straightfoward, take a number element e.g. 78521 and add thousand-separator comma to return '78,521' (n.b. this is a string)
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

