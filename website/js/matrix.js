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
        $.getJSON('/' + year + "/NI/all-constituency-info.json", function(data) {
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
                countMatrix(year, constituencyFolder);
                animateStages(year, constituencyFolder);
            })
            yearSelect.change(function() {
                var constituencyFolder = $("#constituencySelect :selected").val();
                var year = $("#yearSelect :selected").text();
                getTransfersData(year);
                countMatrix(year, constituencyFolder);
                animateStages(year, constituencyFolder);
            })
            var constituencyFolder = $("#constituencySelect :selected").val();
            var year = $("#yearSelect :selected").text();
            getTransfersData(year);
            countMatrix(year, constituencyFolder);
            animateStages(year, constituencyFolder);
        })
