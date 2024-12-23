'use strict';

const commandDashboard = (function() {
    var common = $.Common || {};

    var latest = [];
    var multiLatest = [];
    var virtualLists = [];
    var graphType = 'single';
    var dataChk = false;
    var graphCount = 0;
    var deviceCount = 0;
    var refresh_ok_count = 0;
    var refresh_nok_count = 0;
    var last = [];
    var chartData = [];
    var tableData = [];
    var maxData = [];
    var abc = 0;
    var is_topbottom = false;
    var $realData = [];
    var multiHtml = '';
    var status = false;

    function init() {
        $(window).resize(() => {
            var documentWidth = $(document).width();
            if (documentWidth < 754) {
                if (is_topbottom)
                    $('#wrapper').addClass("enlarged");
                is_topbottom = false;
            } else {
                is_topbottom = true;
                if ($('#wrapper').attr('class') == 'forced') {
                    $('#wrapper').removeClass("enlarged");
                }
            }
        });

        grid();
    };

    function grid() {
        //일단 가리고 시작
        $( ".single" ).hide();
        $( ".dashboardCont" ).show();


        var userIdx = $('#userIdx').val();
        common.ajax('/dashboard/device/virtuallist', {
            type : 'user',
            keyword : userIdx,
        }, 'POST', (res) => {
            virtualLists = res.lists;
        }, (req) => {
            common.err('잘못된 정보가 있습니다.');
        });

        var device = null;
        var dataChk = false;
        deviceCount = virtualLists.length;  //초기화
        graphCount = 0;                     //초기화

        if(deviceCount <= 0){
            $( ".deviceNone" ).show();

        }else if(deviceCount > 1){
            graphType = 'multiple';
            multiHtml = '';
            $('.multiDashboard').empty();

        }

        virtualLists.forEach(function(item,index) {
            chartData[item.virtual_idx] ='';    //초기화

            common.ajax('/dashboard/packet', {
                device : item.device_idx,
                last : last[item.virtual_idx],
                virtual : item.virtual_idx
            }, 'POST', (res) => {
                if(util.isEmpty(res) === true){
                    deviceCount--;
                    return;
                }
                if (util.isEmpty(res) === true) {

                }else{
                    dataChk = true;
                    console.log(res);
                }
                virtual(res,item);
        }, (req) => {
                common.err('잘못된 정보가 있습니다.');
            });
        });


        if (!dataChk) {
            $( ".frameWrap" ).hide();
            $( ".deviceNone" ).show();
        }

        setInterval(packet, 1000 * 1);

    };

    function rollup(event) {
        var $target = $(event);
        if (abc == 0) {
            $('#widget_1').attr('class', "col-md-12");

            $('#widget_2').hide();
            $('#widget_3').hide();
            $('#widget_4').hide();
            $('#virtual1').css('min-height', "700px");
            abc++;
        } else {
            $('#widget_1').attr('class', "col-md-6");

            $('#widget_2').show();
            $('#widget_3').show();
            $('#widget_4').show();
            $('#virtual1').css('min-height', "290px");
            okNokStat(refresh_ok_count, refresh_nok_count, 0);
            abc = 0;
        }
    };


    function packet(){
        virtualLists.forEach(function(item,index) {
            common.ajax('/dashboard/packet', {
                device : item.device_idx,
                last : last[item.virtual_idx],
                virtual : item.virtual_idx
            }, 'POST', (res) => {
                if (util.isEmpty(res) === true) {

                }else{
                    afterVirtual(res,item);

                }
        }, (req) => {
                common.err('잘못된 정보가 있습니다.');
            });
        });

    }

    function virtual(data,item) {
        if (util.isEmpty(data) === true) {
            return ;
        }

        latest = data;
        var lastData = data[0];
        //last = lastData['data_idx'];
        last[lastData['virtual_idx']] = lastData['data_idx'];

        var deviceName = '장비명';
        var virtual_port = '포트번호';
        var torgue_max = get_max_value(lastData);
        if (util.isEmpty(item) === true) {
        }else{
            deviceName = item.wp_name;
            virtual_port = item.virtual_port;
        }

        multiLatest[virtual_port] = data;
        tableData[virtual_port] = data;
        if(graphType == 'single'){
            $('#graphWrap').addClass('graphSingleWrap');
            virtual_randering(lastData['data_status'], lastData['data_torque'],lastData['data_angle'],virtual_port,deviceName,torgue_max);
        }else{
            $('#graphWrap').addClass('graphMultipleWrap');

            $('.dashboardCont').addClass('multiDashboard');
            virtual_multi_randering(lastData['data_status'], lastData['data_torque'],lastData['data_angle'],virtual_port,deviceName,torgue_max);
        }

        //그래프 업데이트
        circle(".circleItem");

        //라인 및 rowTable
        if(graphType == 'single'){
            $( ".single" ).show();
            state(lastData);
            structure(data);    //structure 테이블
            statistic(data);    // static그래프

        }

    };

    function get_max_value(lastData){

        if(lastData.wd_torque_max > 0){
            return 1 / lastData.wd_torque_max * lastData.data_torque;
        }else if( lastData.wd_angle_max > 0 ){
            return 1 /  lastData.wd_angle_max * lastData.data_angle;
        }else{
            return lastData.data_torque;
        }

    }

    function afterVirtual(data,item) {
        if (util.isEmpty(data) === true) {
            return ;
        }

        latest = data;
        var lastData = data[0];
        last[lastData['virtual_idx']] = lastData['data_idx'];

        var deviceName = '장비명';
        var virtual_port = '포트번호';
        var torgue_max = get_max_value(lastData);
        if (util.isEmpty(item) === true) {
        }else{
            deviceName = item.wp_name;
            virtual_port = item.virtual_port;

        }
        multiLatest[virtual_port] = data;

        data.forEach(function(val){
            tableData[virtual_port].unshift(val);
        });



        if(graphType == 'single'){
            $('#graphWrap').addClass('graphSingleWrap');
            virtual_randering(lastData['data_status'], lastData['data_torque'],lastData['data_angle'],virtual_port,deviceName,torgue_max);
        }else{
            if(status){
                $('#graphWrap').addClass('graphSingleWrap');
            }else{
                $('#graphWrap').addClass('graphMultipleWrap');
            }
            $('.dashboardCont').addClass('multiDashboard');
            after_virtual_multi_randering(lastData['data_status'], lastData['data_torque'],lastData['data_angle'],virtual_port,deviceName,torgue_max);
        }

        //그래프 업데이트
        after_circle(".circleItem");

        //라인 및 rowTable
        if(graphType == 'single' ){
            state(lastData);
            structure_add(data);
            statistic(data);
        }else{
            if(status){

                var now_virtual_port = $('#graphWrap.graphSingleWrap').find('#pSet').text();
                var deviceName = $('#graphWrap.graphSingleWrap').find('#frameTit').data('devicename');

                var multiLastData = multiLatest[now_virtual_port][0];

                state(multiLastData);
                structure_add( multiLatest[now_virtual_port]);
                statistic( multiLatest[now_virtual_port]);
                multiple_click_virtual_randering(multiLatest['data_status'], multiLastData['data_torque'],multiLastData['data_angle'],now_virtual_port,deviceName,torgue_max);
                after_circle(".circleItem");
            }

        }


    };

    function after_virtual_multi_randering(status, torgue,angle,virtual_port,deviceName,torgue_max) {

        $('#virtual_port_'+virtual_port).find('#circleItem_'+virtual_port).data('status',status);
        $('#virtual_port_'+virtual_port).find('#circleItem_'+virtual_port).val( torgue_max);
        $('#virtual_port_'+virtual_port).find('#circleItem_'+virtual_port).data('setval',torgue_max);
        $('#virtual_port_'+virtual_port).find('#torgueItem_'+virtual_port).text(torgue);
        $('#virtual_port_'+virtual_port).find('#angleItem_'+virtual_port).text(Math.ceil(angle)+'°');

    }

    function virtual_multi_randering(status, torgue,angle,virtual_port,deviceName,torgue_max) {

        var multiHtml = '';
        multiHtml += '<div class="multi  multiFrameWrap" id="virtual_port_'+virtual_port+'">';
        multiHtml += '<div class="multiframe frame setting">';
        multiHtml += '<div class="frameTit" id="frameTit" data-deviceName = "'+deviceName+'">';
        multiHtml += deviceName + ' - <span id="pSet">'+virtual_port+'</span>';
        multiHtml += '</div>';
        multiHtml += '<div class="psetCont">';
        multiHtml += '<div class="prog">';
        multiHtml += '<div class="circleItem" id="circleItem_'+virtual_port+'" value="'+ torgue_max +'" status="'+ status +'" style=""></div>';
        multiHtml += '<span class="">TORQUE</span>';
        multiHtml += '</div>';
        multiHtml += '<div class="info">';
        multiHtml += '<strong><span id="virtual_txt_1"></span> <span class="" id="torgueItem_'+virtual_port+'" >'+torgue+'</span></strong>';
        multiHtml += '<em id="angleItem_'+virtual_port+'">'+Math.ceil(angle)+'°</em>';
        multiHtml += '</div>';
        multiHtml += '</div>';
        multiHtml += '</div>';
        multiHtml += '</div>';


        $('.multiDashboard').append(multiHtml);
    };

    function virtual_randering(status, torgue,angle,virtual_port,deviceName,torgue_max) {

        $('#graphWrap').empty();

        var html = '';
        html += '<div class="frame setting">';
        html += '<div class="frameTit"  id="frameTit" data-deviceName = "'+deviceName+'">';
        html += deviceName + ' - <span id="pSet">'+virtual_port+'</span>';
        html += '</div>';
        html += '<div class="psetCont">';
        html += '<div class="prog">';
        html += '<div class="circleItem"  value="'+ torgue_max+'" style="" data-setval="'+ torgue_max +'" status="'+ status +'"></div>';
        html += '<span class="">TORQUE</span>';
        html += '</div>';
        html += '<div class="info">';
        html += '<strong><span id="virtual_txt_1"></span> <span class="">'+torgue+'</span></strong>';
        html += '<em >'+Math.ceil(angle)+'°</em>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#graphWrap').append(html);

    };

    function multiple_click_virtual_randering(status, torgue,angle,virtual_port,deviceName,torgue_max) {


        $('#graphWrap').empty();

        var html = '';
        html += '<div class="frame setting">';
        html += '<div class="frameTit"  id="frameTit" data-deviceName = "'+deviceName+'">';
        html += deviceName + ' - <span id="pSet">'+virtual_port+'</span>';
        html += '</div>';
        html += '<div class="psetCont">';
        html += '<div class="prog">';
        html += '<div class="circleItem"  value="'+  torgue_max +'" style="" data-setval="'+  torgue_max +'" status="'+  status +'"></div>';
        html += '<span class="">TORQUE</span>';
        html += '</div>';
        html += '<div class="info">';
        html += '<strong><span id="virtual_txt_1"></span> <span class="">'+torgue+'</span></strong>';
        html += '<em >'+Math.ceil(angle)+'°</em>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#graphWrap').append(html);

    };

    var statusColor = {
        0 : [["#E82630",0.1],["#E82630",0.7]],
        1 : [["#28b14a",0.1],["#28b14a",0.7]],
    };

    var circleOption = {
        size:210,
        lineCap: 'round',
        startAngle : 4.7,
        emptyFill : "#1f232d",
        fill : { gradient: [["#06c0fe",0.1],["#774ef7",0.7]] , gradientDirection: [150, 100,0, 150] }
    }
    function circle(str){
        $(str).each(function(){
            var option = circleOption;
            option.value = $(this).attr("value");
            var status = $(this).attr("status");
            circleOption['fill']['gradient'] = statusColor[status];
            $(this).circleProgress(circleOption);
        });

    }

    function after_circle(str){
        $(str).each(function(){
            var option = circleOption;
            option.value = $(this).data("setval");
            var status = $(this).attr("status");
            circleOption['fill']['gradient'] = statusColor[status];
            $(this).circleProgress(circleOption);
        });

    }

    function state(data) {
        refresh_ok_count = data['calc_ok_status'];
        refresh_nok_count = data['calc_nok_status'];
        okNokStat(refresh_ok_count, refresh_nok_count, 0);
    };

    function structure(data) {

        $('#latest_result').empty();
        var da_status = 'NOK';
        var da_status_style = 'style ="background-color:#cc5555; color:#fff" ';
        var html = '';

        data.forEach(function(val){
            da_status = 'NOK';
            da_status_style = 'style ="color:#E82630" ';
            if (val['data_status'] == 1) {
                da_status = 'OK';
                da_status_style = 'style ="color:#28b14a" ';
            }

            html += '<tr >';
            html += '<td >' + val['data_set'] + '</td>';
            html += '<td  ' + da_status_style + '>' + da_status + '</td>';
            html += '<td >' + parseFloat(val['data_torque']).toFixed(2) + ' </td>';
            html += '<td >' + parseFloat(val['data_angle']).toFixed(2) + '</td>';
            html += '<td >' + val['data_create_date'] + '</td>';
            html += '</tr>';
        });

        $('#latest_result').append(html);
    };

    function structure_add(data) {

        var da_status = 'NOK';
        var da_status_style = 'style ="background-color:#cc5555; color:#fff" ';
        var html = '';

        data.forEach(function(val){
            da_status = 'NOK';
            da_status_style = 'style ="color:#E82630" ';
            if (val['data_status'] == 1) {
                da_status = 'OK';
                da_status_style = 'style ="color:#28b14a" ';
            }

            html += '<tr >';
            html += '<td >' + val['data_set'] + '</td>';
            html += '<td  ' + da_status_style + '>' + da_status + '</td>';
            html += '<td >' + parseFloat(val['data_torque']).toFixed(2) + ' </td>';
            html += '<td >' + parseFloat(val['data_angle']).toFixed(2) + '</td>';
            html += '<td >' + val['data_create_date'] + '</td>';
            html += '</tr>';
        });
        $('#latest_result').prepend( html);
    };

    function statistic(data) {
        var lastData = data[0];
        var min_value = parseFloat(lastData['calc_min_torque']).toFixed(2);
        var max_value = parseFloat(lastData['calc_max_torque']).toFixed(2);
        var avg_value = parseFloat(lastData['calc_avg_torque']).toFixed(2);
        var s3_value = parseFloat(lastData['calc_s3_torque']).toFixed(2);
        var cmk_value = Math.min(((avg_value - max_value) / s3_value), ((min_value - avg_value) / s3_value));

        $('#min_value').html(min_value);
        $('#max_value').html(max_value);
        $('#avg_value').html(avg_value);
        $('#cmk_value').html(cmk_value.toFixed(3));

        StatisticChart(data,min_value, max_value);
        var html ='';
        html +=   "<span style='background:none;'><em class='tit'>CPK</em><em class='info'>" + cmk_value.toFixed(3) + "</em></span>";
        html +=   "<span style='background:none;'><em class='tit'>MIN</em><em class='info'>" + min_value + "</em></span>";
        html +=   "<span><em class='tit'>MAX</em><em class='info'>" + max_value + "</em></span>";
        html +=  "<span><em class='tit'>AVG</em><em class='info'>" + avg_value + "</em></span>";
        $("#reportChart_1_labeling").html(html);

    };

    function StatisticChart(items,min_value, p_max_value) {



        var virtual_idx = '';
        items.forEach(function(item) {
            if (!item.data_torque) return;
            chartData[item.virtual_idx] += moment(item.data_create_date).format("YYYY/MM/DD HH:mm:ss") + ',';
            chartData[item.virtual_idx] += item.data_torque + '\n'
            virtual_idx = item.virtual_idx;

        });

        function zeropad(x) {
            return (x < 10) ? '0' + x : x;
        }
        var chart_1 = new Dygraph(
            document.getElementById("reportChart_1"),
            chartData[virtual_idx],
            {
                labels: ['x','Virtual Station 5'],
                legend : "follow",
                color: "#3481f6",
                axisLineColor : "#4a586a",
                drawPoints: true,
                pointSize : 5,
                strokeWidth : 2,
                fillAlpha : 1,
                fillGraph : true,
                drawHighlightPointCallback : Dygraph.Circles.CIRCLE,
                plotter: [
                    Dygraph.smoothFillPlotter,
                    Dygraph.smoothPlotter,
                ],
                highlightSeriesOpts: {
                    highlightCircleSize: 2,
                    fillAlpha : 0.1,
                },
                unhighlightCallback: function(e, x, pts, row) {
                    //$("#reportChart_1_labeling").hide();
                },
                axes: {
                    x: {
                        gridLineColor: "#384350",
                        axisLabelWidth: 60,
                        axisLabelFormatter: function (d, gran) {
                            return zeropad(d.getHours()) + ":"
                                + zeropad(d.getMinutes()) + ":"
                                + zeropad(d.getSeconds());
                        }
                    },
                    y: {
                        gridLineColor: "#384350",
                        axisLabelWidth: 30
                    }
                },
                legendFormatter: function(data){
                    var html =  "<span class='date'>" + data.xHTML +  "</span> "
                    var html2 = ""
                    var min = 0;
                    var max = 0;
                    var avg = 0;
                    data.dygraph.rawData_.forEach(function(arr){
                        var a = arr[1];
                        if(min==0){ min=a;}
                        else{min = Math.min(min,a)}
                        max = Math.max(max,a);
                        avg += parseInt(a,10);
                    });
                    avg = Math.round( (avg/data.dygraph.rawData_.length) *100 )/100
                    min = Math.round( min*100 )/100
                    max = Math.round( max*100 )/100
                    data.series.forEach(function(series) {
                        if (!series.isVisible) return;
                        var labeledData =  series.yHTML;
                        html +=   "<span class='pos'><em class='tit'>CPK</em><em class='info'>" + labeledData + "</em></span>";
                        html +=   "<span><em class='tit'>MIN</em><em class='info'>" + min + "</em></span>";
                        html +=   "<span><em class='tit'>MAX</em><em class='info'>" + max + "</em></span>";
                        html2 +=   "<span class='pos'><em class='tit'>CPK</em><em class='info'>" + labeledData + "</em></span>";
                        html2 +=   "<span><em class='tit'>MIN</em><em class='info'>" + min + "</em></span>";
                        html2 +=   "<span><em class='tit'>MAX</em><em class='info'>" + max + "</em></span>";
                        html2 +=  "<span><em class='tit'>AVG</em><em class='info'>" + avg + "</em></span>";
                    });
                    //if(data.xHTML) $("#reportChart_1_labeling").show().html(html2);
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

    function okNokStat(ok_count, nok_count, none_count) {

        var stat_total_count = parseInt(ok_count) + parseInt(nok_count); //+ parseInt(none_count);
        var stat_tickSize = 30;
        var stat_topSize = 30;
        $('#stat_total_count').html(stat_total_count);


        if (stat_total_count > 500) {
            stat_tickSize = 100;
            stat_topSize = 100;
        } else if (stat_total_count > 100) {
            stat_tickSize = 50;
            stat_topSize = 50;
        }

        var maxY = (ok_count > nok_count) ? ok_count + 5: nok_count + 5;

        /* bar Chart left */
        var chartBar = document.getElementById("chart_bar_1");
        var chartBar_1 = echarts.init(chartBar);
        var chartBar_1_option =  {
            legend: {
                data:['OK'],
                bottom:0,
                itemWidth : 30,
                itemHeight : 5,
                textStyle :{
                    color:'#fff',
                    fontFamily : 'Noto Sans'
                }
            },
            animation : false,
            barWidth:60,
            grid: {
                left: '20',
                right: '0',
                top: '10',
                bottom: '10%',
                containLabel: true
            },
            xAxis : [
                {
                    type : 'category',
                    data : ['NOK'],
                    axisLine: {lineStyle:{color:'#4a586a'}},
                    axisTick: {show: false},
                    axisLabel: {color:'#ffffff',margin:3},
                    splitLine: {lineStyle:{color:'#313d4b'}},
                    margin:0
                }
            ],
            yAxis : [
                {
                    min: 0,
                    max: maxY,
                    position : "left",
                    type : 'value',
                    axisLine: {lineStyle:{color:'#4a586a'}},
                    axisTick: {show: false},
                    axisLabel: {color:'#ffffff',margin:3},
                    splitLine: {lineStyle:{color:'#313d4b'}}
                }
            ],
            series : [
                {
                    name: "name",
                    type:'bar',
                    stack: 'aa',
                    color:'#E82630',
                    data:[parseInt(nok_count)],
                    animation : false,
                    label: {
                        normal: {
                            show: true,
                            position: 'outside',
                            color:'#fff',
                            padding : [5,10],
                            backgroundColor:"#111418",
                        }
                    },
                }
            ]
        };

        chartBar_1.setOption(chartBar_1_option, true);



        /* bar Chart right */
        var chartBar = document.getElementById("chart_bar_2");
        var chartBar_2 = echarts.init(chartBar);
        var chartBar_2_option =  {
            legend: {
                data:['OK'],
                bottom:0,
                itemWidth : 30,
                itemHeight : 5,
                textStyle :{
                    color:'#fff',
                    fontFamily : 'Noto Sans'
                }
            },
            animation : false,
            barWidth:60,
            grid: {
                left: '0',
                right: '2',
                top: '10',
                bottom: '10%',
                containLabel: true
            },
            xAxis : [
                {
                    type : 'category',
                    data : ['OK'],
                    axisLine: {lineStyle:{color:'#4a586a'}},
                    axisTick: {show: false},
                    axisLabel: {color:'#ffffff',margin:3},
                    splitLine: {lineStyle:{color:'#313d4b'}},
                    margin:0
                }
            ],
            yAxis : [
                {
                    min: 0,
                    max: maxY,
                    position : "right",
                    type : 'value',
                    axisLine: {lineStyle:{color:'#4a586a'}},
                    axisTick: {show: false},
                    axisLabel: {show: false,color:'#ffffff',margin:3},
                    splitLine: {lineStyle:{color:'#313d4b'}}
                }
            ],
            series : [
                {
                    name: "name",
                    type:'bar',
                    stack: 'aa',
                    color:'#28b14a',
                    data:[parseInt(ok_count)],
                    animation : false,
                    label: {
                        normal: {
                            show: true,
                            position: 'outside',
                            color:'#fff',
                            padding : [5,10],
                            backgroundColor:"#111418",
                        }
                    },
                }
            ]
        };

        chartBar_2.setOption(chartBar_2_option, true);
    }

    $(document).on("click","#graphWrap > .frame",function(){
        if(!status){
            if($(this).parent().hasClass('reload')){
                location.reload();
            }else{
                $(".single").hide();
                $(this).addClass('graphBlock');
                $(this).parent().parent().addClass("full");
                $('.full').show();
                status = true;
            }
        }else{
            if(graphType == 'multiple'){
                $(".single").hide();
                $(this).addClass('graphBlock');
                $('#graphWrap').addClass('reload');
                $(this).parent().parent().addClass("full");
                $('.full').show();
                status = true;
            }else{
                location.reload();
            }
            status = false;
        }

    });

    $(document).on('click', '.multiframe', function () {
        if(!status){
            $(".multiframe").hide();
            $(".multi").hide();
            $(".single").show();
            $('#graphWrap').removeClass('graphMultipleWrap');
            $('#graphWrap').addClass('graphSingleWrap');
            $('#graphWrap').parent().show();

            var virtual_port = $(this).parent().find('#pSet').text();

            var deviceName = $(this).parent().find('#frameTit').data('devicename');

            var lastData = multiLatest[virtual_port][0];
            var torgue_max = get_max_value(lastData);

            virtual_randering(lastData['data_status'], lastData['data_torque'],lastData['data_angle'],virtual_port,deviceName,torgue_max);
            circle(".circleItem");

            state(lastData);
            structure(tableData[virtual_port]);
            statistic(multiLatest[virtual_port]);

            status = true;
        }else{
            $(".multiframe").show();
            $(".multi").show();
            status = false;
        }


    });

    return {
        init: () => {
            init();
        },
        rollup: (event) => {
            rollup(event);
        },
    };
})();

;(function($) {
    commandDashboard.init();

    $(document).on('click', '#virtual1', (event) => {
        commandDashboard.rollup(event.target);
    });


})(jQuery);

