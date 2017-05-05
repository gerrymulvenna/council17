var warddata = [];
var canddata = [];
findWardInfo('2017', 'wardinfo.json');
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
	if (year == '2017'  && council)
	{
		loadCandidates(council, ward_code, year);
	}
};

$(wardSelect).on('change', function() {
	  loadCandidates($("#council-list :selected").val(), this.value,  $("#yearSelect :selected").text());
});

$(yearSelect).on('change', function() {
	  loadCandidates($("#council-list :selected").val(), $("#wardSelect :selected").val(), this.value);
});


function loadCandidates(council, ward_code, year)
{
	var names;
	var countdata = getCountInfo(council, ward_code, year);
	if (countdata.hasOwnProperty('Constituency') > 0)
	{
		var countInfo = countdata.Constituency.countInfo;
		$('#electorate').val(countInfo.Total_Electorate);
		$('#total_poll').val(countInfo.Total_Poll);
		$('#valid_poll').val(countInfo.Valid_Poll);
		$('#seats').val(countInfo.Number_Of_Seats);
	}
	else
	{
		$('#electorate').val('');
		$('#total_poll').val('');
		$('#valid_poll').val('');
		$('#seats').val('');
	}
	var candinfo = document.getElementById("candidates");
	candinfo.innerHTML='';
	var fname = 'local.' + council + '.2017-05-04.json';
	if (year == '2017')
	{
		findCandInfo(year, fname);
		var wardstats = getObjects(warddata, "map_ward_code", ward_code);
		if (wardstats.length > 0)
		{
			var cand_ward_code = wardstats[0].cand_ward_code;
			$('#seats').val(wardstats[0].seats);
			var ward = getObjects(canddata.wards, 'post_id', cand_ward_code);

			if (ward.length > 0)
			{
				var candidates = ward[0].candidates;
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
    var path = '/' + year + '/SCO/' + filename; 
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
    var path = '/' + year + '/SCO/' + council + '/' + ward_code + '/ResultsJson.json'; 
	console.log(path);
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
    var path = '/' + year + '/SCO/' + filename; 
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
