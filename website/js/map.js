	var url = new URL(window.location);
	var searchParams = new URLSearchParams(url.search);

	var layerStyle = {
		weight: 2,
		color: 'blue',
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
				weight: 4,
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
			// modify the query string in the URL to reflect the ward
			if (searchParams.has('ward'))
			{
				searchParams.set('ward', ward_code);
			}
			else
			{
				searchParams.append('ward', ward_code);
			}
			if (by_event)
			{
				window.history.replaceState({}, '', `${location.pathname}?${searchParams}`);
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
		tap: true,
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
			if (searchParams.has('ward'))
				{
					var initlayer = getLayer (boundaries, mapWardDesc, searchParams.get('ward'));
					if (initlayer)
					{
						layerSelect(initlayer, false);
					}
				}
	});
	
	
	// detect if user agent is a mobile device and if so disable map zooming panning etc
	if ( /Android|webOS|iPhone|iPad|iPod|Blackberry|IEMobile|Opera Mini|Mobi/.test(navigator.userAgent)) {
		console.log('mobile device detected');
	}
	
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
