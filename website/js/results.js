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
				table[row][1] = countItem.Surname + ' <a target="_blank" href="http://candidates.democracyclub.org.uk/person/' + countItem.Candidate_Id +'/"><i class="fa fa-external-link"></i></a>';
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
