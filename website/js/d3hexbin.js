//Get the data
var data; // a global

getdata(2017);

//The number of columns and rows of the heatmap. Default 90 for 2017, need 108 for other years
var MapColumns, MapRows;

function getdata(year) {
    if (year == '2017') {
        MapColumns = 10;
        MapRows = 9
    } else {
        MapColumns = 12;
        MapRows = 9;
    }
    d3.json("/" + year + "/NI/all-elected-d3.json", function(error, json) {
        if (error) return console.warn(error);
        data = json;
        pushdata(data, "Party_Name", "asc");
//        document.getElementById("number-elected").innerHTML = candidate.length + ' candidates elected. Hover to see who and where.';
        visualise();
    });
}

// sort the data
function sortit() {
    console.log(data);
}

// order (by argument) and push data to arrays
function pushdata(data, prop, asc) {
    data = _.orderBy(data, prop, asc) // if we want to resort, we need a prop string to sort the data by
    color = [];
    party = [];
    candidate = [];
    constituency = [];
    fpv = [];
    for (i in data) {
        color.push(data[i].Colour.toUpperCase());
        candidate.push(data[i].Firstname + ' ' + data[i].Surname);
        party.push(data[i].Party_Name);
        constituency.push(data[i].Constituency_Name);
        fpv.push(data[i].Candidate_First_Pref_Votes);
    };
}

// visualise the data
function visualise() {
		 d3.select("#chart").select('svg').remove();
    //svg sizes and margins
    var margin = {
        top: 50,
        right: 20,
        bottom: 10,
        left: 50
    };

    //var width = ($(window).width()*0.6) - margin.left - margin.right;
    //var height = (width/12)*9 - margin.top - margin.bottom;

    var width = 850;
    var height = 350;

    //The maximum radius the hexagons can have to still fit the screen
    var hexRadius = d3.min([width / ((MapColumns + 0.5) * Math.sqrt(3)),
        height / ((MapRows + 1 / 3) * 1.5)
    ]);

    //Set the new height and width of the SVG based on the max possible
    width = MapColumns * hexRadius * Math.sqrt(3);
    heigth = MapRows * 1.5 * hexRadius + 0.5 * hexRadius;

    //Set the hexagon radius
    var hexbin = d3.hexbin()
        .radius(hexRadius);

    //Calculate the center positions of each hexagon
    var points = [];
    for (var i = 0; i < MapRows; i++) {
        for (var j = 0; j < MapColumns; j++) {
            points.push([hexRadius * j * 1.75, hexRadius * i * 1.5]);
        } //for j
    } //for i

    var boxX = width + margin.left + margin.right,
        boxY = height + margin.top + margin.bottom;

    //Create SVG element
    var svg = d3.select("#chart").append("svg")
        .attr("viewBox", '0 0 ' + boxX + ' ' + boxY)
        //	.attr("width", width + margin.left + margin.right)
        //	.attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    // svg.selectAll("*").remove();
    svg.append("g")
        .selectAll(".hexagon")
        .data(hexbin(points))
        .enter().append("path")
        .attr("class", "hexagon")
        .attr("constituency", function(d, i) {
            return constituency[i];
        })
        .attr("party", function(d, i) {
            return party[i];
        })
        .attr("name", function(d, i) {
            return candidate[i];
        })
        .attr("fpv", function(d, i) {
            return fpv[i];
        })
        .attr("d", function(d) {
            return "M" + d.x + "," + d.y + hexbin.hexagon();
        })
        .attr("stroke", function(d, i) {
            return "#000";
        })
        .attr("stroke-width", "1px")
        .style("fill", function(d, i) {
            if (color[i]) {
                return color[i];
            } else {
                return '#fff'
            }
        })
        .style("fill-opacity", 0.75)
        .on("mouseover", mover)
        .on("mouseout", mout);
}


///////////////////////////////////////////////////////////////////////////
////////////// Initiate SVG and create hexagon centers ////////////////////
///////////////////////////////////////////////////////////////////////////

//Function to call when you mouseover a node
function mover(d) {
    var el = d3.select(this)
        .transition()
        .duration(200)
        .attr("stroke-width", "2px")
        .style("fill-opacity", 1);
    //Update the tooltip value
    d3.select("#charttooltip")
        .style("left", (d3.event.pageX - 30) + "px")
        .style("top", (d3.event.pageY + 30) + "px")
        .select("#name")
        .text(d3.select(this).attr("name"))
    d3.select("#charttooltip")
        .select('#constituencyname')
        .text(d3.select(this).attr("constituency"))
    d3.select("#charttooltip")
        .select('#fpv')
        .text(d3.select(this).attr("fpv") + ' 1st Pref Votes')
    d3.select("#charttooltip")
        .select("#party")
        .text(d3.select(this).attr("party"));
    //Show the tooltip
    d3.select("#charttooltip").classed("hidden", false);
}

//Mouseout function
function mout(d) {
    var el = d3.select(this)
        .transition()
        .duration(400)
        .attr("stroke-width", "1px")
        .style("fill-opacity", 0.75);
    //Hide the tooltip
    d3.select("#charttooltip").classed("hidden", true);
};
