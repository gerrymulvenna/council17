var transferData = {};
function getTransfersData(year) {
	$.getJSON("/2017/SCO/simulation/party-transfers.json", function (json) {
		var transfers = {};
		$.each(json, function (i, constituency) {
			var con = constituency.Constituency_Name;
			transfers[con] = {};
			$.each(constituency.Counts, function (j, count) { // loop thru each count
				for (var donor in count.From) break; // get the name of the donor party
				// create party if does not exist
				if (!transfers[con][donor]) {
					transfers[con][donor] = {total: count.From[donor]}; //initialise and add total
				} else {
					transfers[con][donor].total += count.From[donor]; // add to existing total
				}
				if (!transfers[con][donor]["Not_transferred"]) {
					transfers[con][donor].not_transferred = count.Not_transferred; // create not_transferred value
				} else {
					transfers[con][donor].not_transferred += count.Not_transferred; // add to exisiting n_t value
				}
				$.each(count.To, function (recipient, amount) { // loop thru each recipient
					if (!transfers[con][donor][recipient]) { // if recipient does not exist
						transfers[con][donor][recipient] = amount; // set recipient value = amount
					} else {
						transfers[con][donor][recipient] += amount // add to existing recipient amount
					};
				});
			});
		});
		// convert constituency objects to arrays of individual transfers
		$.each(transfers, function (cname, constituency) { // loop thru constituencies
			var data = [];
			$.each(constituency, function (donor, transfers) { // loop thru donor parties
				var totalTransfers = -transfers.total || 0;
			});
			transferData[cname] = data;
		});


		function loadViz() {
				if ($("#pause-replay").hasClass("fa-repeat")) {
					$("#pause-replay").removeClass("fa-repeat");
					$("#pause-replay").addClass("fa-play");
				}			
				$.get("/website/jsonspec/transferSpec.json", function (json) {
				var spec = JSON5.parse(json);
				var constituency = $("#constituencySelect :selected").text();
                document.getElementById('transfers_constituency').innerHTML = constituency;
				var data = transferData[constituency];
				spec.data = [
					{
						name: "transfers",
						values: data
					},
					{
					  name: "ty",
					  source: "transfers",
					  transform: [
						{
							type: "aggregate",
							groupby: "donor",
							summarize: {donor: "distinct"}
						},
						{
							type: "rank",
							field: "donor"
						},                    {
							type: "formula",
							field: "height",
							expr: "datum.rank*30"
						}
					  ]
					},
					{
					  name: "tx",
					  source: "transfers",
					  transform: [
						{
							type: "aggregate",
							groupby: "recipient",
							summarize: {recipient: "distinct"}
						},
						{
							type: "rank",
							field: "recipient"
						},                    {
							type: "formula",
							field: "width",
							expr: "datum.rank*35"
						}
					  ]
					}
				];
				vg.parse.spec(spec, function (chart) {
					var view = chart({
							el: "#transfers"
						})
						.on("mouseover", function (event, item) {
							if (item && item.datum.amount) {
								$('#matrixtooltip').show();
								$('#matrixtooltip').html(
									Math.round(item.datum.amount * 10) /10 + "%"
								);
							} else {
								$('#matrixtooltip').hide();
							}
						})
						.update();
				});
			}, "text");
		}

		$("#constituencySelect").change(loadViz);
		loadViz();
});
}

// honour URL params to display a particular result
$(window).load(function(e) {
	var searchParams = getSearchParams();
	if (searchParams['council'] && searchParams['ward'] && searchParams['year'])
	{
		$("#yearSelect").val(searchParams['year']);
		switch(searchParams['year'])
		{
			case '2012':
				$("#council-list-2017").hide();
				$("#council-list-2012").show();
				break;
			case '2017':
				$("#council-list-2012").hide();
				$("#council-list-2017").show();
				break;
		}
		$("#council-list-" + searchParams['year']).val(searchParams['council']);
		loadWards(searchParams['year'], searchParams['council'], searchParams['ward']);
		$("#constituencySelect").val(searchParams['ward']);
		animateStages(searchParams['year'], searchParams['council'], searchParams['ward']);
		$('#mapLink').html('<a href="/councils/' + searchParams['council'] + '.php?ward=' + searchParams['ward'] + '">View on map</a>');
	}
});


// cross-browser search param functions
function getSearchParams(k){
 var p={};
 location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
 return k?p[k]:p;
}

//function to record ward_code in URL search query string (assumes it is only parameter)
function setSearchParams(year, council, ward){
  window.history.replaceState({}, '', location.pathname + '?council=' + council + '&ward=' + ward + '&year=' + year);
}