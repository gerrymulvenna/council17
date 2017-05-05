var warddata = [];
findWardInfo('2017', 'wardinfo.json');
var wardSelect = $("#wardSelect");

// function to 
function selectCouncil() {
	$("#council-list").change(function()
	{
		var wards = getObjects(warddata, 'election', $(this).val());
		$(wardSelect).empty();
		$.each(wards, function(i, ward) {
			var name = ward.ward_name;
			var folder = ward.map_ward_code;
			$(wardSelect).append($("<option/>").val(folder).text(name))
		})
	});
};


// load council / ward data to global var 'warddata'
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
