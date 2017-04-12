var checkedYear = 2017;
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
            console.log(result)
            _.forEach(result, function(value, key) {
                document.getElementById("seats_summary").innerHTML += '<span class="' + key.replace(/\s+/g, "-") + '">&nbsp;&nbsp;&nbsp;</span>&nbsp;<span class="' + key.replace(/\s+/g, "-") + '_abbr ' + '"></span>' + ': ' + value + '&nbsp;';
            })
        }
    });
}

// when a radio button is clicked change checkedYear global var (attached to element onchange)
function changeyear(year) {
    checkedYear = year;
    console.log(checkedYear);
}

// load all candidates info for the checkedYear
findInfo(checkedYear, 'all-candidates.json');   //populate jsondata
findWardInfo(checkedYear, 'all-ward-info.json');   //populate warddata

// request candidate info for the specified year (can use this for other request by changing filename arg)
// outputs the parse Json responseText to global var jsondata
function findInfo(year, filename) {
    var request = new XMLHttpRequest();
    var path = '/' + year + '/SCO/' + filename;
    console.log(path);
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
    var path = '/' + year + '/SCO/' + filename; // add ? with timestamp to force XMLHttpRequest not to cache
    console.log(path);
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status >= 200 && request.status < 400) {
            warddata = JSON.parse(request.responseText);
            console.log(warddata);
        }
    };
    request.open('GET', path, false);
    request.send();
    request.onerror = function() {
        candidates.innerHTML = 'Connection error retrieving data from the server'
    };
}

// again, similar to the above but we're trying to find if any elected candidates exist
function findElectedInfo(year) {
    electedOutput = [];
    var request = new XMLHttpRequest();
    var path = '/' + year + '/SCO/all-elected.json?' + new Date().getTime(); // add ? with timestamp to force XMLHttpRequest not to cache
    console.log(path);
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status >= 200 && request.status < 400) {
            electedOutput = JSON.parse(request.responseText);
        }
    };
    request.open('GET', path, false);
    request.send();
    request.onerror = function() {
        electedOutput = [];
        console.log('not ready');
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

// straightfoward, take a number element e.g. 78521 and add thousand-separator comma to return '78,521' (n.b. this is a string)
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

////// FUNCTIONS TO HANDLE HTML ELEMENT POPULATION OF CANDIDATE AND WARD INFORMATION //////
var candidates = document.getElementById('candidates');
var wardinfo = document.getElementById('wardinfo');

candidates.update = function() {
    this.innerHTML = '';
	var no_seats = '';
    var ward = getObjects(jsondata, 'Ward_Code', ward_code);
	var wardstats = getObjects(warddata, "Ward_Code", ward_code);
    console.log(wardstats);

	if (wardstats.length > 0)
	{
		no_seats = wardstats[0].Seats + ' council seats, ';
	}
    var candidates = ward[0].Candidates;
    console.log(candidates);
    wardinfo.innerHTML = '<h2>' + ward[0].Ward_Name + ' ward (' + no_seats + candidates.length + ' candidates)</h2>';
//    console.log(constituency_directory);
    for (i = 0; i < candidates.length; i++) {
        this.innerHTML += '<div class="votes ' + candidates[i].Party_Name.replace(/\s+/g, "-") + '" style="width: 20px;"></div><div id="candidate ' + candidates[i].Candidate_Id + '" class="tooltip ' + candidates[i].Party_Name.replace(/\s+/g, "-") + '_label">' + candidates[i].Firstname + ' ' + candidates[i].Surname + '<span class="tooltiptext">' + candidates[i].Party_Name + '</span></div><br/>';
    }
};

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
    findInfo(checkedYear, 'all-candidates.json');
    for (p = 0; p < jsondata.Wards.length; p++) {
        var id = jsondata.Wards[p].Ward_Code;
        var title = jsondata.Wards[p].Ward_Name;
        candidates.update('Ward_Code', id, title);
    }
};

// function to retrive vega spec to populate count matrix //
function countMatrix(year, directory) {
    $.get("/website/jsonspec/countSpec.json", function(json) {
        var spec = JSON5.parse(json);
        spec.data[0].url = '/' + year + '/constituency/' + directory + '/Count.csv'; // needed to dynamically change the data url in spec to our desired path
        console.log(spec);
        vg.parse.spec(spec, function(chart) {
            var view = chart({
                    el: "#count_matrix"
                })
                .on("mouseover", function(event, item) {
                    if (item && item.datum.Surname && item.datum.Status) {
                        console.log(item);
                        $('#matrixtooltip').show();
                        $('#matrixtooltip').html(
                            "<b>" + item.datum.Firstname + ' ' + item.datum.Surname + "</b><br/>" +
                            item.datum.Party_Name + "<br/>" +
                            item.datum.Status + ' on count ' + item.datum.Occurred_On_Count
                        );
                    } else if (item && item.datum.Surname) {
                        console.log(item);
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

//////<-------------------------------------------------->//////

////// FUNCTIONS TO HANDLE SELECT MENUS (OPTIONS FILLING) //////
function partyoptions() {
    findInfo(checkedYear, 'all-party-candidates.json');
    for (p = 0; p < jsondata.Parties.length; p++) {
        partySelect.innerHTML += '<option value="' + jsondata.Parties[p].Party_Number + '">' + jsondata.Parties[p].Party_Name + '</option>';
    }
}

function wardoptions() {
    findInfo(checkedYear, 'all-ward-info.json');
    console.log(jsondata);
    for (c = 0; c < jsondata.Wards.length; c++) {
        wardSelect.innerHTML += '<option value="' + jsondata.Wards[c].Ward_Code + '" data-dir="' + jsondata.Wards[c].Directory + '">' + jsondata.Wards[c].Ward_Name + '</option>';
    }
}

function resetselect(select, defaulttext) {
    select.innerHTML = '<option value=null>' + defaulttext + '</option>';
}
////// <-------------------------------------> //////
