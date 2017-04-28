	var searchParams = getSearchParams();

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
			tips.update();
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
	});
	
	$(window).resize(function(e) {
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
	tips.update = function () {
		if ($(window).width()<792)
		{
			tips._div.style.display = "block";
			this._div.innerHTML = '<a href="#candidates">Go to candidates list below</a>';
		}
		else
		{
			tips._div.style.display = "none";
		}
	};
	tips.addTo(map);


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