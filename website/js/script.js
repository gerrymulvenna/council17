var checkedYear = 2017;
var electionDate = '2017-05-04';
var jsondata = [];
var warddata = [];

// though 2016 is the default checked radio element in the html some users who have reloaded may have checked '2011'
// this function examines the two radios to see which has been checked
var inputElements = document.getElementsByName("year");
for (var i = 0; inputElements[i]; ++i) {
    if (inputElements[i].checked) {
        checkedYear = inputElements[i].value;
        break;
    }
}

// function to 
function selectCouncil() {
	$("#council-list").change(function()
	{
		document.location.href = $(this).val();
	});
};

// change the title to reflect the ward selected
function updateTitle (ward, council)
{
	var title = document.title;
	document.title = ward + ", " + council + " candidates for local elections 2017";
}




// load all candidates info for the checkedYear
if (typeof(mapUnit) != 'undefined' && mapUnit == 'Ward')
{
	findInfo(checkedYear, 'local.' + mapName +'.' + electionDate + '.json');   //populate jsondata
	findWardInfo(checkedYear, 'wardinfo.json');   //populate warddata
}

// request candidate info for the specified year (can use this for other request by changing filename arg)
// outputs the parse Json responseText to global var jsondata
function findInfo(year, filename) {
    var request = new XMLHttpRequest();
    var path = '/' + year + '/SCO/' + filename;
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status >= 200 && request.status < 400) {
            jsondata = JSON.parse(request.responseText);
        }
    };
    request.open('GET', path, false);
    request.send();
    request.onerror = function() {
        candidates.innerHTML = 'Connection error retrieving data from the server'
    };
}

// similar to findInfo but used to get constituency count (votes polled etc) and output to global var 'warddata'
function findWardInfo(year, filename) {
    var request = new XMLHttpRequest();
//    var path = '/' + year + '/SCO/' + filename + '?' + new Date().getTime(); // add ? with timestamp to force XMLHttpRequest not to cache
    var path = '/' + year + '/SCO/' + filename; 
	console.log(path);
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status >= 200 && request.status < 400) {
            warddata = JSON.parse(request.responseText);
        }
    };
    request.open('GET', path, false);
    request.send();
    request.onerror = function() {
        candidates.innerHTML = 'Connection error retrieving data from the server'
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

////// FUNCTIONS TO HANDLE HTML ELEMENT POPULATION OF CANDIDATE AND WARD INFORMATION //////
var candidates = document.getElementById('candidates');
var wardinfo = document.getElementById('wardinfo');

candidates.update = function() {
   var ack = '<div id="ack"><div id="dc-caption">This full set of candidate data was collated by</div><div id="dc-logo"><a href="http://democracyclub.org.uk"><img src="/website/image/democracyclub-logo-with-text.png" width="250"></a></div><div id="disclaimer">DISCLAIMER The ordering of the candidates above is a best guess. The actual ballot paper for this ward may interpret the alphabetical ordering of candidates\' names differently.</div>';
	$("#tabs-container").css('display', 'none');
	this.innerHTML = '';
	var wardstats = getObjects(warddata, "map_ward_code", ward_code);
	var tw;
	var fb;
	var fbp;
	var web;
	var linkedin;
	var wiki;
	var status;

	if (wardstats.length > 0)
	{
		var no_seats = wardstats[0].seats + ' council seats, ';
		var cand_ward_code = wardstats[0].cand_ward_code;
	    var ward = getObjects(jsondata, 'post_id', cand_ward_code);

		if (ward.length > 0)
		{
			var result ="";
            $.ajax({
                'async': false,
                'global': false,
                'url': '/2017/SCO/' + wardstats[0].election + '/' + wardstats[0].map_ward_code + '/ResultsJson.json?' + new Date().getTime(),
                'dataType': "json",
                'success': function (data) {
					var cinfo = data.Constituency.countInfo;
					var quota = parseInt(cinfo.Quota);
					var seats = parseInt(cinfo.Number_Of_Seats);
					result = '<a class="result" href="/results?year=2017&council=' + wardstats[0].election + '&ward=' + wardstats[0].map_ward_code + '">View result</a>';
					var turnout = ((parseInt(cinfo.Total_Poll)/parseInt(cinfo.Total_Electorate)) * 100).toFixed(2);
					$("#electorate").html("<p>Electorate: " + numberWithCommas(parseInt(cinfo.Total_Electorate)) + ", Turnout: " + numberWithCommas(parseInt(cinfo.Total_Poll)) + " (" + turnout + "%), Valid votes: " + numberWithCommas(parseInt(cinfo.Valid_Poll)) + ", Quota: " + numberWithCommas(quota) + "</p>\n");
                }});

			var candidates = ward[0].candidates.sort(cmpSurnames);
			wardinfo.innerHTML = '<h3><a name="candidates">' + wardstats[0].ward_name + ' ward</a><br><span class="seats">' + no_seats + candidates.length + ' candidates</span><br>' + result+ '</h3>';
			for (i = 0; i < candidates.length; i++) {
				tw = (candidates[i].twitter_username) ? '<a href="http://twitter.com/' + candidates[i].twitter_username + '" target="~_blank"><i class="fa fa-twitter fa-fw" title="@' +  candidates[i].twitter_username + ' on Twitter"></i></a>' : '';
				fb = (candidates[i].facebook_page_url) ? '<a href="' + candidates[i].facebook_page_url + '" target="_blank"><i class="fa fa-facebook fa-fw"  title="Facebook page"></i></a>' : '';
				fbp = (candidates[i].facebook_personal_url) ? '<a href="' + candidates[i].facebook_personal_url + '" target="_blank"><i class="fa fa-facebook-official fa-fw" title="Personal Facebook profile"></i></a>' : '';
				web = (candidates[i].homepage_url) ? '<a href="' + candidates[i].homepage_url + '" target="_blank"><i class="fa fa-globe fa-fw" title="Homepage for this candidate"></i></a>' : '';
				linkedin = (candidates[i].linkedin_url) ? '<a href="' + candidates[i].linkedin_url + '" target="_blank"><i class="fa fa-linkedin fa-fw" title="This candidate has a LinkedIn profile"></i></a>' : '';
				wiki = (candidates[i].wikipedia_url) ? '<a href="' + candidates[i].wikipedia_url + '" target="_blank"><i class="fa fa-wikipedia-w fa-fw" title="This candidate has an entry on Wikipedia"></i></a>' : '';
				edit = '<a href="http://candidates.democracyclub.org.uk/person/' + candidates[i].id + '/" target="_blank"><i class="fa fa-check-square-o fa-fw" title="View or edit the Democracy Club details for this candidate"></i></a>';
				switch(candidates[i].elected)
				{
					case "True":
						status = "elected";
						break;
					case "False":
						status = "excluded";
						break;
					default:
						status = "unkonwn";
				}
				this.innerHTML += "<div class=\"votes " + candidates[i].party_name.replace(/\s+/g, "-").replace(/[\'\",()]/g,"").replace(/\u2013/g, '_') + "\"></div><div id=\"candidate " + candidates[i].id + "\" class=\"tooltip " + candidates[i].party_name.replace(/\s+/g, "-").replace(/[\'\",()]/g,"").replace(/\u2013/g, '_') + "_label\"><span class=\"tooltiptext\">" + candidates[i].party_name + "</span>" + '<span class="' + status +'">' + candidates[i].name + "</span><div class=\"cand-icons\">" + tw + fb + fbp + web  + linkedin + wiki  + edit + "</div></div><br/>";
			}
			this.innerHTML += ack;
			updateTitle(wardstats[0].ward_name, wardstats[0].council);
		}
	}
};

// used in sorting candidates
function cmpSurnames(a, b)
{
	anames = splitName(a.name);
	bnames = splitName(b.name);
	anorm = anames.Surname.toUpperCase() + " " + anames.Firstname.toUpperCase();
	bnorm = bnames.Surname.toUpperCase() + " " + bnames.Firstname.toUpperCase();
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



// optional message on clearing 'candidates' element. If none set arg to ''
function clearCandidates(msg) {
    candidates.innerHTML = msg;
}

// function to populate 'candidates' element with all candidates by party
function partiesAll() {
    findInfo(checkedYear, 'all-party-candidates.json');
    for (p = 0; p < jsondata.Parties.length; p++) {
        var id = jsondata.Parties[p].Party_Number;
        var title = jsondata.Parties[p].Party_Name;
        candidates.update('Party_Number', id, title);
    }
};

// function to populate 'candidates' element with all candidates by ward
function wardsAll() {
    findInfo(checkedYear, 'two-tier-candidates.json');
    for (p = 0; p < jsondata.Wards.length; p++) {
        var id = jsondata.Wards[p].Ward_Code;
        var title = jsondata.Wards[p].Ward_Name;
        candidates.update('Ward_Code', id, title);
    }
};


// straightfoward, take a number element e.g. 78521 and add thousand-separator comma to return '78,521' (n.b. this is a string)
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
