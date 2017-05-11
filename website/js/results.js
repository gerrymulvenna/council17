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
				$("#council-list-2012 option").filter('[value="0"]').prop("selected", true);
				$("#constituencySelect").empty();
				break;
			case '2017':
				$("#council-list-2012").hide();
				$("#council-list-2017").show();
				$("#council-list-2017 option").filter('[value="0"]').prop("selected", true);
				$("#constituencySelect").empty();
				break;
		}
		var council = $("#council-list-" + year + " :selected").val();
		var constituencyFolder = $("#constituencySelect :selected").val();

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
	}

// populate the ward select based on year and council
function loadWards(year, council, selected)
{
	$(constituencySelect).empty();
	if ($('#pause-replay').hasClass("fa-replay")) {
		$('#pause-replay').removeClass("fa-replay");
		$('#pause-replay').addClass("fa-play");
	} else if ($('#pause-replay').hasClass("fa-pause")) {
		$('#pause-replay').removeClass("fa-pause");
		$('#pause-replay').addClass("fa-play");
	}
	console.log ("loadWards", year, council, selected);
	var path = '/' + year + "/SCO/" + council + "/all-constituency-info.json"; 
	$.getJSON(path, function(data) 
	{
		var constituencies = data.Constituencies.sort(cmpNames);
		var constituencySelect = $("#constituencySelect");
		$(constituencySelect).append($("<option/>").val(0).text("Select a ward"));
		var yearSelect = $("#yearSelect");
		$.each(constituencies, function(i, constituency)
		{
			var name = constituency.Constituency_Name;
			var folder = constituency.Directory;
			$(constituencySelect).append($("<option/>").val(folder).text(name));
			if (folder == selected)
			{
				$('#constituencySelect option').filter('[value=' + selected + ' ]').prop('selected', true);
			}
		});
		$("#constituencySelect").change(function()
		{
			if ($('#pause-replay').hasClass('fa-repeat')) {
				$('#pause-replay').removeClass('fa-repeat');
				$('#pause-replay').addClass('fa-play');
			} else if ($('#pause-replay').hasClass('fa-pause')) {
				$('#pause-replay').removeClass('fa-pause');
				$('#pause-replay').addClass('fa-play');
			}
			var year = $("#yearSelect :selected").text();
			var council = $("#council-list-" + year + " :selected").val();
			var constituencyFolder = $("#constituencySelect :selected").val();
			animateStages(year, council, constituencyFolder);
		});
	})
}

// straightfoward, take a number element e.g. 78521 and add thousand-separator comma to return '78,521' (n.b. this is a string)
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}


// return capitalised initials for a string split on space
function getInitials(name)
{
	var initials = name.match(/\b\w/g) || [];
	initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
	return initials;
}


// takes an array of countInfo elements and returns essentially a two-dimensional array for display
function tableCount(countGroup)
{
	var table = [];
	if (countGroup.length > 0)
	{
		var row = 0;
		var stage = 1;
		$.each(countGroup, function(i, countItem) 
		{
			if (countItem.Count_Number == 1)
			{
				table[row] = [];
				table[row][0] = countItem.Firstname;
				table[row][1] = countItem.Surname + ' <a title="Open this candidate\'s page on Democracy Club" target="_blank" href="http://candidates.democracyclub.org.uk/person/' + countItem.Candidate_Id +'/"><i class="fa fa-external-link"></i></a>';
				table[row][2] = countItem.Party_Name;
				table[row][3] = countItem.Candidate_First_Pref_Votes;
				table[row][4] = countItem.Status;
				table[row][5] = countItem.Occurred_On_Count;
				row++;
				col = 4;
			}
			else 
			{
				if (countItem.Count_Number > stage)
				{
					stage = countItem.Count_Number;
					row = 0;
					col +=2;
				}
				table[row][4] = countItem.Status;
				table[row][5] = countItem.Occurred_On_Count;
				table[row][col] = countItem.Transfers;
				table[row][col + 1] = countItem.Total_Votes;
				row++;
			}
		})
	}
	return (table);
}
// return table of results as html
function tableHTML(t)
{
	if (t.length > 0)
	{
		var html = '<tr><th>Firstname</th><th>Surname</th><th>Party</th><th>1st pref</th><th>Status</th><th>At stage</th>';
		var stage = 1;
		for (var col=6; col<t[0].length; col+=2)
		{
			stage++;
			html += '<th>Transfers</th><th>Stage ' + stage + '</th>';
		}
		html += '</tr>' + "\n";
		for (var row=0; row<t.length;row++ )
		{
			html += '<tr>';
			for (col=0; col<t[row].length;col++ )
			{
				html +='<td>' + t[row][col] + '</td>';
			}
			html += '</tr>' + "\n";
		}
		return '<table class="count-data">' + html + '</table>';
	}
	else
	{
		return "";
	}
}
