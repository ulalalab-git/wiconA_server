
/**
* Theme: Adminto Admin Template
* Author: Coderthemes
* Dashboard
*/

!function($) {
    "use strict";

    var Dashboard1 = function() {
    	this.$realData = []
    };

    //creates Bar chart
    Dashboard1.prototype.createBarChart  = function(element, data, xkey, ykeys, labels, lineColors) {
        Morris.Bar({
            element: element,
            data: data,
            xkey: xkey,
            ykeys: ykeys,
            labels: labels,
            hideHover: 'auto',
            resize: true, //defaulted to true
            gridLineColor: '#2f3e47',
            barSizeRatio: 0.5,
            gridTextColor: '#98a6ad',
            barColors: lineColors
        });
    },

    //creates line chart
    Dashboard1.prototype.createLineChart = function(element, data, xkey, ykeys, labels, opacity, Pfillcolor, Pstockcolor, lineColors) {
        Morris.Line({
          element: element,
          data: data,
          xkey: xkey,
          ykeys: ykeys,
          labels: labels,
          fillOpacity: opacity,
          pointFillColors: Pfillcolor,
          pointStrokeColors: Pstockcolor,
          behaveLikeLine: true,
          gridLineColor: '#2f3e47',
          hideHover: 'auto',
          resize: true, //defaulted to true
          pointSize: 0,
          gridTextColor: '#98a6ad',
          lineColors: lineColors
        });
    },

    //creates Donut chart
    Dashboard1.prototype.createDonutChart = function(element, data, colors) {
        Morris.Donut({
            element: element,
            data: data,
            resize: true, //defaulted to true
            colors: colors,
            backgroundColor: '#2f3e47',
            labelColor: '#fff'
        });
    },
    
    
    Dashboard1.prototype.init = function() {

        //creating bar chart
       
 //       this.createBarChart('morris-line-example', $barData, 'y', ['a'], ['Traffic'], ['#188ae2']);
/*
        //create line chart
        var $data  = [
            { y: '27', a: 75},
            { y: '28', a: 30 },
            { y: '29', a: 50 },
            { y: '30', a: 75 },
            { y: '31', a: 50 },
            { y: '01', a: 75 },
            { y: '02', a: 100 }
          ];
        this.createLineChart('morris-line-example', $data, 'y', ['a'], ['Traffic'],['0.9'],['#ffffff'],['#999999'], ['#10c469']);
*/

		//creating donut chart
		// if(g_chart_caution==0 && g_chart_warring == 0) {
			 // var $donutData = [
                // {label: "안전", value: 100}
            // ];
        	// this.createDonutChart('morris-donut-example', $donutData, ['#10c469', '#10c469']);
		// } else {
			 // var $donutData = [
                // {label: "주의", value: g_chart_caution},
                // {label: "경고", value: g_chart_warring}
            // ];
       		// this.createDonutChart('morris-donut-example', $donutData, ['#ff8acc', '#5b69bc']);
		// }
        
        
        
        //creating bar chart
        var $barData  = [
            { y: '04/05', a: 100, b: 90 , c: 40 },
            { y: '04/06', a: 75,  b: 65 , c: 20 },
            { y: '04/07', a: 50,  b: 40 , c: 50 },
            { y: '04/08', a: 75,  b: 65 , c: 95 },
            { y: '04/09', a: 50,  b: 40 , c: 22 }
        ];
        this.createBarChart('morris-bar-example', $barData, 'y', ['a', 'b'], ['Series A', 'Series B'], ['#ff8acc', "#35b8e0"]);
        
       
    },
    //init
    $.Dashboard1 = new Dashboard1, $.Dashboard1.Constructor = Dashboard1
}(window.jQuery),

//initializing 
function($) {
    "use strict";
    $.Dashboard1.init();
}(window.jQuery);


			