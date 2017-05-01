var council = "simulation";
		
		$(document).ready(function() {
            $.ajaxSetup({
                cache: false
            });
        });

        seatsSummary();

        // tooltip
        $(document).bind('mousemove', function(e) {
            $('#matrixtooltip').css({
                "left": e.pageX + 20,
                "top": e.pageY
            });
        });

        var year = $("#yearSelect :selected").text();
        $.getJSON('/' + year + "/SCO/" + council + "/all-constituency-info.json", function(data) {
            var constituencies = data.Constituencies;
            var constituencySelect = $("#constituencySelect");
            var yearSelect = $("#yearSelect");
            $.each(constituencies, function(i, constituency) {
                var name = constituency.Constituency_Name;
                var folder = name.toLowerCase().replace(" and ", "-").replace(/\s/, "-");
                $(constituencySelect).append($("<option/>").val(folder).text(name))
            })
            constituencySelect.change(function() {
                var constituencyFolder = $("#constituencySelect :selected").val();
                var year = $("#yearSelect :selected").text();
                //countMatrix(year, council, constituencyFolder);
                animateStages(year, council, constituencyFolder);
            })
            yearSelect.change(function() {
                var constituencyFolder = $("#constituencySelect :selected").val();
                var year = $("#yearSelect :selected").text();
                getTransfersData(year);
                //countMatrix(year, council, constituencyFolder);
                animateStages(year, council, constituencyFolder);
            })
            var constituencyFolder = $("#constituencySelect :selected").val();
            var year = $("#yearSelect :selected").text();
            getTransfersData(year);
            //countMatrix(year, council, constituencyFolder);
            animateStages(year, council, constituencyFolder);
        })

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
            _.forEach(result, function(value, key) {
                document.getElementById("seats_summary").innerHTML += '<span class="' + key.replace(/\s+/g, "-") + '">&nbsp;&nbsp;&nbsp;</span>&nbsp;<span class="' + key.replace(/\s+/g, "-") + '_abbr ' + '"></span>' + ': ' + value + '&nbsp;';
            })
        }
    });
}

// function to retrive vega spec to populate count matrix //
function countMatrix(year, council, directory) {
    $.get("/website/jsonspec/countSpec.json", function(json) {
        var spec = JSON5.parse(json);
        spec.data[0].url = '/' + year + '/SCO/' + council + "/" + directory + '/Count.csv'; // needed to dynamically change the data url in spec to our desired path
        vg.parse.spec(spec, function(chart) {
            var view = chart({
                    el: "#count_matrix"
                })
                .on("mouseover", function(event, item) {
                    if (item && item.datum.Surname && item.datum.Status) {
                        $('#matrixtooltip').show();
                        $('#matrixtooltip').html(
                            "<b>" + item.datum.Firstname + ' ' + item.datum.Surname + "</b><br/>" +
                            item.datum.Party_Name + "<br/>" +
                            item.datum.Status + ' on count ' + item.datum.Occurred_On_Count
                        );
                    } else if (item && item.datum.Surname) {
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

// straightfoward, take a number element e.g. 78521 and add thousand-separator comma to return '78,521' (n.b. this is a string)
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

