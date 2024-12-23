/**
* Theme: Adminto Admin Template
* Author: Coderthemes
* Component: Other Chart
* 
*/
$( document ).ready(function() {
    
    
    
    var resizeChart;

    $(window).resize(function(e) {
        clearTimeout(resizeChart);
        resizeChart = setTimeout(function() {
         
        }, 300);
    });
});