// tooltip
$(document).bind('mousemove', function(e) {
	$('#matrixtooltip').css({
		"left": e.pageX + 20,
		"top": e.pageY
	});
});



var slug = (mapName == "scotland") ? "." : mapName;
var transferData = [];
getTransferData(slug);
loadViz();

function getTransferData(slug) {
	var path = "/2017/SCO/" + slug + "/transfers.json"; 
	console.log("Transfer data: ", path);
	$.getJSON(path, function(data) {
	transferData = data;
		});
}


		function loadViz() {
				$.get("/website/jsonspec/transferSpec.json", function (json) {
				var spec = JSON5.parse(json);
				data = transferData;
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
