'use strict';

const commandReport = (function() {
    let common = $.Common || {};

    function setting() {
        var today = new Date();
        let yyyy = today.getFullYear();
        let dd = today.getDate();
        let mm = today.getMonth() + 1; //January is 0!

        var yesterday = new Date();
        yesterday.setDate(today.getDate());
        yesterday.setHours(today.getHours() - 5);

        let start_yyyy = yesterday.getFullYear();
        let start_dd = yesterday.getDate();
        let start_mm = yesterday.getMonth() + 1; //January is 0!
        let start_hour = yesterday.getHours();

        if (dd < 10) dd = '0' + dd
        if (mm < 10) mm = '0' + mm
        if (start_dd < 10) start_dd = '0' + start_dd
        if (start_mm < 10) start_mm = '0' + start_mm

        let end_today = yyyy + '-' + mm + '-' + dd;
        let start_today = start_yyyy + '-' + start_mm + '-' + start_dd;

        $("#datepicker").datepicker('setDate', start_today);
        $("#datepicker2").datepicker('setDate', end_today);

        $('#timepicker2').val(start_hour + ':00');
    };

    function grid() {
        let data = $('#grid').attr('data');
        if (util.isEmpty(data) === true) {
            return;
        }
        data = JSON.parse(data);

        let data_string = '';
        let $labelData = [];
        $labelData.push('Date');
        $labelData.push('Virtual Station ' + $('#device option:selected').text());

        for (var i in data) {
            let val = data[i];
            let curr_date = new Date(val["data_create_date"]);
            data_string += curr_date + "," + val["data_torque"] + "\n";
        }

        new Dygraph(
            document.getElementById("grid"),
            data_string, {
                customBars: false,
                title: '',
                ylabel: 'Value',
                labels: $labelData,
                legend: 'always',
                showRangeSelector: true,
                rangeSelectorPlotStrokeColor: '#6b5b80',
                rangeSelectorPlotFillColor: '',
                drawGrid: true,
                strokeWidth: 0.8,
                axisLineColor: '#dddddd',
                colors: ["rgb(252,210,2)",
                    "rgb(255,100,100)",
                    "#236ccc",
                    "rgba(50,50,200,0.4)"
                ],
                series: {
                    'Max': {
                        strokeWidth: 0.5
                    },
                    'Avg': {
                        strokeWidth: 2.5,
                        fillGraph: true
                    },
                    'Min': {
                        strokeWidth: 0.5
                    },
                }
            }
        );
    };

    function init() {
        var device = $('#device').val();
        var virtualId = $('#virtualId').val();

        if(device){
            common.ajax('/dashboard/device/virtuallist', {
                type : 'device',
                keyword : device,
            }, 'POST', (res) => {

                var deviceList = res.lists;
            var optionSelected = '';
            $('#virtual').empty();
            var option = $("<option value=''>Port Select</option>");
            $('#virtual').append(option);

            for(var count = 0; count < deviceList.length; count++){
                if(deviceList[count].virtual_idx == virtualId){
                    optionSelected = 'selected';
                }else{
                    optionSelected = '';
                }
                var option2 = $("<option value="+deviceList[count].virtual_idx+" "+ optionSelected + ">"+deviceList[count].virtual_port+"</option>");
                $('#virtual').append(option2);
            }

        }, (req) => {
                //alert('잘못된 정보가 있습니다.');
                common.err('잘못된 정보가 있습니다.');
            });
        }


        $("#datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            inline: true,
        });

        $("#datepicker2").datepicker({
            dateFormat: 'yy-mm-dd',
            inline: true,
        });

        $('#timepicker3').timepicker({
            showMeridian: false
        });
        $('#timepicker2').timepicker({
            showMeridian: false
        });

        $('#datepicker4').daterangepicker();

        if (util.isEmpty($('#datepicker').val()) === false) {
            grid();
            reportGrid();
            return;
        }
        setting();

    };

    function deviceChange(device){
        common.ajax('/dashboard/device/virtuallist', {
            type : 'device',
            keyword : device,
        }, 'POST', (res) => {

            var deviceList = res.lists;
        $('#virtual').empty();
        var option = $("<option value=''>Port Select</option>");
        $('#virtual').append(option);
        for(var count = 0; count < deviceList.length; count++){
            var option2 = $("<option value="+deviceList[count].virtual_idx+">"+deviceList[count].virtual_port+"</option>");
            $('#virtual').append(option2);
        }

    }, (req) => {
            //alert('잘못된 정보가 있습니다.');
            common.err('잘못된 정보가 있습니다.');
        });
    };

    function reportSubmit(){

        var type = $('#type').val();
        var virtual = $('#virtual').val();
        var theForm = document.reportForm;
        if(virtual){
            if(type == 'analysis'){
                theForm.action = "/dashboard/report/analysis";
                theForm.submit();
            }else{
                theForm.action = "/dashboard/report/summary";
                theForm.submit();
            }

        }else{
            common.err('포트 정보가 없습니다.');
        }

    };


    function downXls(){
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById('datatable');
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
        // Specify file name
        var filename = 'summary.xls';

        // Create download link element
        downloadLink = document.createElement("a");

        document.body.appendChild(downloadLink);

        if(navigator.msSaveOrOpenBlob){
            var blob = new Blob(['\ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob( blob, filename);
        }else{
            // Create a link to the file
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

            // Setting the file name
            downloadLink.download = filename;

            //triggering the function
            downloadLink.click();
        }
    }



    function reportGrid(){
        let data = $('#reportChart_1').attr('data');
        if (util.isEmpty(data) === true) {
            return;
        }
        data = JSON.parse(data);

        let data_string = '';
        let $labelData = [];
        $labelData.push('Date');
        $labelData.push('Virtual Station ' + $('#device option:selected').text());

        for (var i in data) {
            let val = data[i];
            let curr_date = moment(val["data_create_date"]).format('YYYY/MM/DD HH:mm:ss');
            data_string += curr_date + "," + val["data_torque"] + "\n";
        }

        if (document.getElementById("reportChart_1")) {
            function zeropad(x) {
                return (x < 10) ? '0' + x : x;
            }

            var g = new Dygraph(
                document.getElementById("reportChart_1"),
                data_string,
                {
                    labels: $labelData,
                    legend: "follow",
                    color: "#3481f6",
                    axisLineColor: "#888",
                    drawPoints: true,
                    pointSize: 5,
                    strokeWidth: 2,
                    ylabel: 'Value',
                    fillAlpha: 1,
                    fillGraph: true,
                    drawHighlightPointCallback: Dygraph.Circles.CIRCLE,
                    plotter: [
                        Dygraph.smoothFillPlotter,
                        Dygraph.smoothPlotter,
                    ],
                    highlightSeriesOpts: {
                        highlightCircleSize: 2,
                        fillAlpha: 0.1,
                    },

                    unhighlightCallback: function (e, x, pts, row) {
                        $("#reportChart_1_labeling").hide();
                    },
                    axes: {
                        x: {
                            gridLineColor: "#313d4b",
                            axisLabelWidth: 60,
                            axisLabelFormatter: function (d, gran) {
                                return zeropad(d.getHours()) + ":"
                                    + zeropad(d.getMinutes()) + ":"
                                    + zeropad(d.getSeconds());
                            }
                        },
                        y: {
                            gridLineColor: "#313d4b",
                            axisLabelWidth: 40
                        }
                    },
                    axisLabelWidth: 30,
                    legendFormatter: function (data) {
                        var html = "<span>" + data.xHTML + "</span> " + "<em class='tit'>" + this.user_attrs_.labels[1] + "</em> ";
                        var html2 = "<span>" + data.xHTML + "</span> " + " : <em class='tit'>" + this.user_attrs_.labels[1] + "</em> ";
                        data.series.forEach(function (series) {
                            if (!series.isVisible) return;
                            var labeledData = series.yHTML;
                            html += '<em> ' + labeledData + "</em>";
                            html2 += ' : <em> ' + labeledData + "</em>";
                        });
                        if (data.xHTML) $("#reportChart_1_labeling").show().html(html2);
                        return html
                    },
                    plugins: [
                        new Dygraph.Plugins.Crosshair({
                            direction: "vertical"
                        })
                    ]
                }
            );
        }
    }

    return {
        init: () => {
        init();
},
    downXls: () => {
        downXls();
    },
    deviceChange: (device) => {
        deviceChange(device);
    },
    /*portChange: (port) => {
        portChange(port);
    },*/
    reportSubmit: () => {
        reportSubmit();
    },

};
})();

;(function($) {
    commandReport.init();
    $(document).on('click', '#downXls', () => {
        commandReport.downXls();
});

    $( ".deviceSelect" ).change(function() {
        commandReport.deviceChange($(this).val());
    });

    /*
        $( ".virtual" ).change(function() {
            commandReport.portChange($(this).val());
        });
    */

    $(document).on('click', '.btn2', () => {
        commandReport.reportSubmit();
});


})(jQuery);
