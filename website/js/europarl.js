	// function to populate wards for a given council
	$("#region-list-2019").change(function()
	{
		manageChanges($(this));
	});

	// function to populate wards for a given council
	$("#region-list-2014").change(function()
	{
		manageChanges($(this));
	});

	$('#yearSelect').change(function() {
		var year = $("#yearSelect :selected").text();
		$('#raw-data').html("");
		switch(year)
		{
			case '2014':
				$("#region-list-2019").hide();
				$("#region-list-2014").show();
				$("#region-list-2014 option").filter('[value="0"]').prop("selected", true);
				break;
			case '2014':
				$("#region-list-2014").hide();
				$("#region-list-2019").show();
				$("#region-list-2019 option").filter('[value="0"]').prop("selected", true);
				break;
		}
		var council = $("#region-list-" + year + " :selected").val();

	});

	// used in sorting candidates
	function cmpSurnames(a, b)
	{
		var anames = splitName(a.name);
		var bnames = splitName(b.name);
		var anorm = anames.Surname.toUpperCase() + " " + anames.Firstname.toUpperCase();
		var bnorm = bnames.Surname.toUpperCase() + " " + bnames.Firstname.toUpperCase();
		if (anorm < bnorm) 
		{
			return -1;
		}
		if (anorm > bnorm) 
		{
			return 1;
		}
		// a must be equal to b
		return 0;
	}


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
		$('#raw-data').html("");
        var year = $("#yearSelect :selected").text();
		var region = cSelect.val();
	}

// change the title to reflect the ward selected
function updateTitle (ward, council)
{
	var title = document.title;
	document.title = region + " results visualisation for the European Parliament elections (Ireland)";
}


// straightfoward, take a number element e.g. 78521 and add thousand-separator comma to return '78,521' (n.b. this is a string)
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// return an array with firstname, surname elements
function splitName(name)
{
	var ret = [];
	var pos = strrpos(name, " ");
	if (pos)
	{
		ret['Surname'] = name.substr(pos + 1);
		ret['Firstname'] = name.substr(0, pos);
	}
	return (ret);
}

function strrpos (haystack, needle, offset) {
  //  discuss at: http://locutus.io/php/strrpos/
  // original by: Kevin van Zonneveld (http://kvz.io)
  // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //    input by: saulius
  //   example 1: strrpos('Kevin van Zonneveld', 'e')
  //   returns 1: 16
  //   example 2: strrpos('somepage.com', '.', false)
  //   returns 2: 8
  //   example 3: strrpos('baa', 'a', 3)
  //   returns 3: false
  //   example 4: strrpos('baa', 'a', 2)
  //   returns 4: 2
  var i = -1
  if (offset) {
    i = (haystack + '')
      .slice(offset)
      .lastIndexOf(needle) // strrpos' offset indicates starting point of range till end,
    // while lastIndexOf's optional 2nd argument indicates ending point of range from the beginning
    if (i !== -1) {
      i += offset
    }
  } else {
    i = (haystack + '')
      .lastIndexOf(needle)
  }
  return i >= 0 ? i : false
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
				if (isNaN(t[row][col]))
				{
					html +='<td>' + t[row][col] + '</td>';
				}
				else
				{
					html +='<td align="right">' + t[row][col] + '</td>';
				}
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

// honour URL params to display a particular result
$(window).load(function(e) {
	var searchParams = getSearchParams();
	if (searchParams['region'] && searchParams['year'])
	{
		$("#yearSelect").val(searchParams['year']);
		switch(searchParams['year'])
		{
			case '2014':
				$("#region-list-2019").hide();
				$("#region-list-2014").show();
				break;
			case '2019':
				$("#region-list-2014").hide();
				$("#region-list-2019").show();
				break;
		}
		$("#region-list-" + searchParams['year']).val(searchParams['region']);
		animateStages(searchParams['year'], searchParams['region']);
	}
});


// cross-browser search param functions
function getSearchParams(k){
 var p={};
 location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
 return k?p[k]:p;
}

//function to record ward_code in URL search query string (assumes it is only parameter)
function setSearchParams(year, council, ward){
  window.history.replaceState({}, '', location.pathname + '?region=' + region + '&year=' + year);
}