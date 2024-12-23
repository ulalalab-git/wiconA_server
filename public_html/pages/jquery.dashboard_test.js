
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

    //cr
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

		
       
    },
    //init
    $.Dashboard1 = new Dashboard1, $.Dashboard1.Constructor = Dashboard1
}(window.jQuery),

//initializing 
function($) {
    "use strict";
    $.Dashboard1.init();
}(window.jQuery);


			