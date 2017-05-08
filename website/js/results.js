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
				table[row][1] = countItem.Surname;
				table[row][2] = countItem.Party_Name;
				table[row][3] = countItem.Candidate_Id;
				table[row][4] = countItem.Candidate_First_Pref_Votes;
				table[row][5] = countItem.Status;
				table[row][6] = countItem.Occurred_On_Count;
				row++;
				col = 5;
			}
			else 
			{
				if (countItem.Count_Number > stage)
				{
					stage = countItem.Count_Number;
					row = 0;
					col +=2;
				}
				table[row][5] = countItem.Status;
				table[row][6] = countItem.Occurred_On_Count;
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
	var html = '<tr><th>Firstname</th><th>Surname</th><th>Party</th><th>ID</th><th>1st pref</th><th>Status</th><th>At stage</th>';
	var stage = 1;
	for (var col=7; col<t[0].length; col+=2)
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
