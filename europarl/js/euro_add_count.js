var warddata = [];
var canddata = [];
findWardInfo('2019', 'wardinfo.json');
var wardSelect = $("#wardSelect");
var yearSelect = $("#yearSelect");
var council = '';

// function to populate wards for a given council
function selectCouncil() {
	$("#council-list").change(function()
	{
		council = $(this).val();
		var wards = getObjects(warddata, 'election', council);
		$(wardSelect).empty();
		$.each(wards, function(i, ward) {
			var name = ward.ward_name;
			var folder = ward.map_ward_code;
			$(wardSelect).append($("<option/>").val(folder).text(name))
		})
	});
    var year = $("#yearSelect :selected").text();
    var ward_code = $("#wardSelect :selected").val();
	if (year  && council)
	{
		loadCandidates(council, ward_code, year);
	}
};

$(wardSelect).on('change', function() {
	loadCandidates($("#council-list :selected").val(), this.value,  $("#yearSelect :selected").text());
	$('#pastebin').val('');
	$('#turnout').val('');
});

$(yearSelect).on('change', function() {
	loadCandidates($("#council-list :selected").val(), $("#wardSelect :selected").val(), this.value);
	$('#pastebin').val('');
});

function loadCandidates(council, ward_code, year)
{
	var candinfo = document.getElementById("candidates");
	var wardvars = document.getElementById("wardvars");
	candinfo.innerHTML='';

	var names;
	var countdata = getCountInfo(council, ward_code, year);
	if (countdata.hasOwnProperty('Constituency') > 0)
	{
		var countInfo = countdata.Constituency.countInfo;
		$('#electorate').val(countInfo.Total_Electorate);
		$('#total_poll').val(countInfo.Total_Poll);
		$('#valid_poll').val(countInfo.Valid_Poll);
		$('#seats').val(countInfo.Number_Of_Seats);
		candinfo.innerHTML= tableHTML(tableCount(countdata.Constituency.countGroup));
	}
	else
	{
		$('#electorate').val('');
		$('#total_poll').val('');
		$('#valid_poll').val('');
		$('#seats').val('');
	}
	var fname = 'europarl.json';
	if (year)
	{
		findCandInfo(year, fname);
		var wardstats = getObjects(warddata, "map_ward_code", ward_code);
		if (wardstats.length > 0)
		{
			var cand_ward_code = wardstats[0].cand_ward_code;
			$('#seats').val(wardstats[0].seats);
			var ward = getObjects(canddata.wards, 'post_id', cand_ward_code);
			wardvars.innerHTML = '<input type="hidden" name="ward_name" value="' + wardstats[0].ward_name + '">';

			if (ward.length > 0)
			{
				var candidates = ward[0].candidates.sort(cmpSurnames);
				for (i = 0; i < candidates.length; i++) {
					names = splitName(candidates[i].name);
					candinfo.innerHTML += "<div class=\"votes " + candidates[i].party_name.replace(/\s+/g, "-").replace(/[\'\",()]/g,"").replace(/\u2013/g, '_') + "\"></div><div id=\"candidate " + candidates[i].id + "\" class=\"tooltip " + candidates[i].party_name.replace(/\s+/g, "-").replace(/[\'\",()]/g,"").replace(/\u2013/g, '_') + "_label\">" + candidates[i].name + "</div><br/>";
					candinfo.innerHTML += '<input type="hidden" name="Candidate_Id[]" value="' + candidates[i].id + '"><input type="hidden" name="Party_Name[]" value="' + candidates[i].party_name + '"><input type="hidden" name="Firstname[]" value="' + names.Firstname + '"><input type="hidden" name="Surname[]" value="' + names.Surname + '">';
				}
				$('#pastebin').attr('rows', candidates.length);
			}
		}
	}
}

// load council / ward data to global var 'warddata'
function findWardInfo(year, filename) {
    var request = new XMLHttpRequest();
    var path = '/' + year + '/EU/' + filename; 
	
	wardpath.innerHTML = path;

    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status >= 200 && request.status < 400) {
            warddata = JSON.parse(request.responseText);
        }
    };
    request.open('GET', path, false);
    request.send();
    request.onerror = function() {
        candinfo.innerHTML = 'Connection error retrieving data from the server'
    };
}

// load count info for a given ward
function getCountInfo(council, ward_code, year) {
	var countdata = [];
    var request = new XMLHttpRequest();
    var path = '/' + year + '/EU/' + council + '/' + ward_code + '/ResultsJson.json?' + new Date().getTime(); // add ? with timestamp to force XMLHttpRequest not to cache
	console.log(path);

	resultspath.innerHTML = path;
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status >= 200 && request.status < 400) {
            countdata = JSON.parse(request.responseText);
        }
    };
    request.open('GET', path, false);
    request.send();
    request.onerror = function() {
        candinfo.innerHTML = 'Connection error retrieving data from the server'
    };
	return(countdata);
}


// load candidate info
function findCandInfo(year, filename) {
    var request = new XMLHttpRequest();
    var path = '/' + year + '/EU/' + council + '/' + filename; 
	console.log(path);

	candidatepath.innerHTML = path;
	summarypath.innerHTML = '/' + year + '/EU/' + council + '/all-constituency-info.json';
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status >= 200 && request.status < 400) {
            canddata = JSON.parse(request.responseText);
        }
    };
    request.open('GET', path, false);
    request.send();
    request.onerror = function() {
        candinfo.innerHTML = 'Connection error retrieving data from the server'
    };
}


// examine an object array (obj) for a key (key) matching a value (val) and return the matching object
function getObjects(obj, key, val) {
    var objects = [];
    for (var i in obj) {
        if (!obj.hasOwnProperty(i)) continue;
        if (typeof obj[i] == 'object') {
            objects = objects.concat(getObjects(obj[i], key, val));
        } else
        //if key matches and value matches or if key matches and value is not passed (eliminating the case where key matches but passed value does not)
        if (i == key && obj[i] == val || i == key && val == '') { //
            objects.push(obj);
        } else if (obj[i] == val && key == '') {
            //only add if the object is not already in the array
            if (objects.lastIndexOf(obj) == -1) {
                objects.push(obj);
            }
        }
    }
    return objects;
}

$('#turnout').change(function() {
	if ($('#electorate').val().length == 0)
	{
		if ( !isNaN(parseFloat( $(this).val() )) && isFinite( $(this).val() ))
		{
			$('#electorate').val((100 * parseFloat($('#total_poll').val()) / parseFloat($(this).val())).toFixed(0));
		}
	}
});

$('#total_poll').change(function() {
	if ($('#turnout').val().length == 0)
	{
		if ( !isNaN(parseFloat( $(this).val() )) && isFinite( $(this).val() ) && !isNaN(parseFloat( $('#electorate').val() )) && isFinite( $('#electorate').val() ))
		{
			$('#turnout').val((100 * parseFloat($(this).val()) / parseFloat($('#electorate').val())).toFixed(2));
		}
	}
});