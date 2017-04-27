var statesData = readJSON('/2017/SCO/boundaries/scotland.geojson');

// function to use mapit API to get postcode data
function getPostcodeData (postcode)
{
	var ptrim = postcode.replace(/\s/g, '');
    var request = new XMLHttpRequest();
    var path = 'https://mapit.democracyclub.org.uk/postcode/' + ptrim; 
	var pdata = null;

    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status >= 200 && request.status < 400) {
            pdata = JSON.parse(request.responseText);
        }
    };
    request.open('GET', path, false);
    request.send();
    request.onerror = function() {
        alert( 'Connection error retrieving data from the server');
    };
	return (pdata);
}

// function to read JSON file into object
function readJSON(path)
{
    var request = new XMLHttpRequest();
	var data = null;

    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status >= 200 && request.status < 400) {
            data = JSON.parse(request.responseText);
        }
    };
    request.open('GET', path, false);
    request.send();
    request.onerror = function() {
        alert( 'Connection error retrieving data from the server');
    };
	return (data);
}


