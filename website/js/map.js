	var searchParams = getSearchParams();

	var layerStyle = {
		weight: 1,
		color: 'black',
		fillOpacity: 0.1,
		opacity: 1
		};
		
	function highlightFeature(e) {
		var layer = e.target;
			info.update(layer.feature.properties);
		}
	
	var boundaries;
	
	function resetHighlight(e) {
		info.update();
	}	
	
	function clickFeature(e) {
		var layer = e.target;
		layerSelect(layer, true);
	}

	function layerSelect(layer, by_event)
	{
		boundaries.setStyle(layerStyle);
		layer.setStyle({
				weight: 2,
				fillOpacity: 0.7
		});
		if (!L.Browser.ie && !L.Browser.opera) {
			layer.bringToFront();
			}
			info.update(layer.feature.properties);
		if (mapUnit == 'Ward')
		{
			ward_code = layer.feature.properties[mapWardDesc];
			candidates.update();
			wardinfo.update;
			tips.update('<a href="#candidates">Go to candidates list below</a>');
			if (by_event)
			{
				setWard(ward_code);
			}
		}
		else
		{
			councilPath = layer.feature.properties.FILE_NAME.toLowerCase().replace(/_/g,'-');
			location.href = '/councils/' + councilPath  + '.php';
		}
	}
	
	function onEachFeature(feature, layer) {
		layer.on({
			mouseover: highlightFeature,
			mouseout: resetHighlight,
			click: clickFeature
		});
	}
		
	var boundaries = new L.GeoJSON.AJAX('/2017/SCO/boundaries/' + mapName + '.geojson', {
		style: layerStyle,
		onEachFeature: onEachFeature
		});

    var map = L.map('map', {
		tap: false,
		minZoom: 5,
		maxZoom: 16
		});

	map.setView([mapLat, mapLong], mapZoom);
	mapLink = '<a href="http://openstreetmap.org">OpenStreetMap</a>';
    
	L.tileLayer(
            'https://a.tiles.mapbox.com/v4/mapbox.light/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiYm9iaGFycGVyIiwiYSI6ImQwOTg1YTg2MTQzYzk3Mzc5MWVjYzFkZDQzN2M1NTUzIn0.mA2WO4WAZzh-qwoqN4QVjg', {
            attribution: '&copy; ' + mapLink + ' | <a href=\"https://www.mapbox.com/about/maps/\" target=\"_blank\">&copy; Mapbox</a> | Boundaries: <a href="http://www.lgbc-scotland.gov.uk/maps/datafiles/index_1995_on.asp">LGBC</a>',
            maxZoom: 18,
            }).addTo(map);
	
	boundaries.addTo(map);

	$(window).load(function(e) {
			if (searchParams['ward'])
			{
					var initlayer = getLayer (boundaries, mapWardDesc, searchParams['ward']);
					if (initlayer)
					{
						layerSelect(initlayer, false);
					}
			}
			else if (mapName == 'scotland')
			{
				var stripes = [];   // an array of stripes objects
				// if we're looking at map of Scotland, we can colour the councils according to the results
				$.getJSON('/2017/SCO/summary.json', function (data) {
					$.each( data, function( index, element ) {
						if (element.biggest_parties.length == 1)
						{
							var council = element.council.toUpperCase().replace(/-/g,'_');
							var thisLayer = getLayer(boundaries, 'FILE_NAME', council);
							if (thisLayer)
							{
								thisLayer.setStyle({fillColor: element.biggest_parties[0].color, fillOpacity: 0.9});
							}
						}
						else if (element.biggest_parties.length == 2)  // we'll use the leaflet.pattern plugin to do stripes. Luckily we don't have a three-way tie
						{
							// Custom Stripes.
							stripes[element.council] = new L.StripePattern({
								color: element.biggest_parties[0].color,
								opacity: 1,
								spaceColor: element.biggest_parties[1].color,
								spaceOpacity: 1,
								weight: 4,
								spaceWeight: 4,
								angle: 45
							});
							var council = element.council.toUpperCase().replace(/-/g,'_');
							stripes[element.council].addTo(map);
							var thisLayer = getLayer(boundaries, 'FILE_NAME', council);
							if (thisLayer)
							{
								thisLayer.setStyle({fillPattern: stripes[element.council], fillOpacity: 0.9});
							}
						}
					});		
					legend.update('Colours denote parties with<br>most seats per council');
				});
				overview_by_var(2017, 'no_seats', 'first_prefs', 'councillor', 'councillors', 'no_seats', '#no_seats');
			}

	});
	
	// detect if user agent is a mobile device and if so disable map zooming panning etc
	if ( /Android|webOS|iPhone|iPad|iPod|Blackberry|IEMobile|Opera Mini|Mobi/.test(navigator.userAgent)) {
		console.log('mobile device detected');
	}
	// element to display council / ward information on map
	var info = L.control();
	info.onAdd = function (map) {
		this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info" inside the map
		this.update();
		return this._div;
	};
	// method that we will use to update the map info control based on feature properties passed
	info.update = function (props) {
		this._div.innerHTML = '<h4>' + mapTitle + '</h4>' +  (props ? '<strong>' + props[mapProperty] + '</strong>' : 'Select a ' + mapUnit.toLowerCase());
	};
	info.addTo(map);

	// element to display tips
	var tips = L.control();
	tips.onAdd = function (map) {
		this._div = L.DomUtil.create('div', 'tips'); // create a div with a class "tips" inside the map
		return this._div;
	};
	// display a prompt to look at candidates below on a small screen
	tips.update = function (msg) {
		if ($(window).width()<792)
		{
			tips._div.style.display = "block";
			this._div.innerHTML = msg;
		}
		else
		{
			tips._div.style.display = "none";
		}
	};
	tips.addTo(map);

	// element to display a legend about colouring
	var legend = L.control();
	legend.setPosition("bottomleft");
	legend.onAdd = function (map) {
		this._div = L.DomUtil.create('div', 'legend'); // create a div with a class "legend" inside the map
		return this._div;
	};
	legend.update = function (msg) {
		this._div.innerHTML = msg;
	};
	legend.addTo(map);

	// detect if user agent is iOS and provide two-tap guidance
	if ( /iPhone|iPad|iPod/.test(navigator.userAgent)) {
		tips.update('Tap once to preview<br>a second time to select');
	}



// examine the boundaries object (b) for a feature with a matching property (key == val)
function getLayer(b, key, val) {
    for (var i in b._layers)
	{
		if (b._layers[i].feature.properties[key] == val)
		{
			return b._layers[i];
		}
    }
}

// cross-browser search param functions
function getSearchParams(k){
 var p={};
 location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
 return k?p[k]:p;
}

//function to record ward_code in URL search query string (assumes it is only parameter)
function setWard(ward_code){
  window.history.replaceState({}, '', location.pathname + '?ward=' + ward_code );
}