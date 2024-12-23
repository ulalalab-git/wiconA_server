/**
 * Theme: Adminto Admin Template
 * Author: Coderthemes
 * Module/App: Flot-Chart
 */

! function($) {
	"use strict";

	var FlotChart = function() {
		this.$body = $("body")
		this.$realData = []
	};

	

	//returns some random data
	FlotChart.prototype.randomData = function() {
		var totalPoints = 300;
		if (this.$realData.length > 0)
			this.$realData = this.$realData.slice(1);

		// Do a random walk
		while (this.$realData.length < totalPoints) {

			var prev = this.$realData.length > 0 ? this.$realData[this.$realData.length - 1] : 50,
			    y = prev + Math.random() * 10 - 5;

			if (y < 0) {
				y = 0;
			} else if (y > 100) {
				y = 100;
			}

			this.$realData.push(y);
		}

		// Zip the generated y values with the x values
		var res = [];
		for (var i = 0; i < this.$realData.length; ++i) {
			res.push([i, this.$realData[i]])
		}

		return res;
	}, FlotChart.prototype.createRealTimeGraph = function(selector, data, colors) {
		var plot = $.plot(selector, [data], {
			colors : colors,
			series : {
				grow : {
					active : false
				}, //disable auto grow
				shadowSize : 0, // drawing is faster without shadows
				lines : {
					show : true,
					fill : true,
					lineWidth : 2,
					steps : false
				}
			},
			grid : {
				show : true,
				aboveData : false,
				color : '#36404a',
				labelMargin : 15,
				axisMargin : 0,
				borderWidth : 0,
				borderColor : null,
				minBorderMargin : 5,
				clickable : true,
				hoverable : true,
				autoHighlight : false,
				mouseActiveRadius : 20
			},
			tooltip : true, //activate tooltip
			tooltipOpts : {
				content : "Value is : %y.0" + "%",
				shifts : {
					x : -30,
					y : -50
				}
			},
			yaxis : {
				min : 0,
				max : 100,
				tickColor : '#transparent',
				tickLength: 0
			},
			xaxis : {
				show : false
			}
		});

		return plot;
	},

	//initializing various charts and components
	FlotChart.prototype.init = function() {
		/*
		//plot graph data
		var uploads = [[0, 9], [1, 8], [2, 5], [3, 8], [4, 5], [5, 14], [6, 10],[7, 8], [8, 5], [9, 14], [10, 10]];
		var downloads = [[0, 5], [1, 12], [2, 4], [3, 3], [4, 12], [5, 11], [6, 14],[7, 12], [8, 8], [9, 4], [10, 8]];
		var plabels = ["Visits", "Page views"];
		var pcolors = ['#188ae2', '#10c469'];
		var borderColor = 'transparent';
		var bgColor = 'transparent';
		this.createPlotGraph("#website-stats", uploads, downloads, plabels, pcolors, borderColor, bgColor);

		//Pie graph data
		var pielabels = ["Series 1", "Series 2", "Series 3"];
		var datas = [20, 30, 15];
		var colors = ['#ff8acc', '#5b69bc', "#f9c851"];
		this.createPieGraph("#pie-chart #pie-chart-container", pielabels, datas, colors);
*/

		//real time data representation
		var plot = this.createRealTimeGraph('#flotRealTime', this.randomData(), ['#71b6f9']);
		plot.draw();
		var $this = this;
		function updatePlot() {
			plot.setData([$this.randomData()]);
			// Since the axes don't change, we don't need to call plot.setupGrid()
			plot.draw();
			setTimeout(updatePlot, $('html').hasClass('mobile-device') ? 1000 : 1000);
		}

		updatePlot();
/*
		//Donut pie graph data
		var donutlabels = ["Series 1", "Series 2", "Series 3", "Series 4"];
		var donutdatas = [35, 20, 10, 20];
		var donutcolors = ['#188ae2', '#10c469', "#f9c851", "#ff8acc"];
		this.createDonutGraph("#donut-chart #donut-chart-container", donutlabels, donutdatas, donutcolors);

		//Combine graph data
		var data24Hours = [[0, 201], [1, 520], [2, 337], [3, 261], [4, 157], [5, 95], [6, 200], [7, 250], [8, 320], [9, 500], [10, 152], [11, 214], [12, 364], [13, 449], [14, 558], [15, 282], [16, 379], [17, 429], [18, 518], [19, 470], [20, 330], [21, 245], [22, 358], [23, 74]];
		var data48Hours = [[0, 311], [1, 630], [2, 447], [3, 371], [4, 267], [5, 205], [6, 310], [7, 360], [8, 430], [9, 610], [10, 262], [11, 324], [12, 474], [13, 559], [14, 668], [15, 392], [16, 489], [17, 539], [18, 628], [19, 580], [20, 440], [21, 355], [22, 468], [23, 184]];
		var dataDifference = [[23, 727], [22, 128], [21, 110], [20, 92], [19, 172], [18, 63], [17, 150], [16, 592], [15, 12], [14, 246], [13, 52], [12, 149], [11, 123], [10, 2], [9, 325], [8, 10], [7, 15], [6, 89], [5, 65], [4, 77], [3, 600], [2, 200], [1, 385], [0, 200]];
		var ticks = [[0, "22h"], [1, ""], [2, "00h"], [3, ""], [4, "02h"], [5, ""], [6, "04h"], [7, ""], [8, "06h"], [9, ""], [10, "08h"], [11, ""], [12, "10h"], [13, ""], [14, "12h"], [15, ""], [16, "14h"], [17, ""], [18, "16h"], [19, ""], [20, "18h"], [21, ""], [22, "20h"], [23, ""]];
		var combinelabels = ["Last 24 Hours", "Last 48 Hours", "Difference"];
		var combinedatas = [data24Hours, data48Hours, dataDifference];

		this.createCombineGraph("#combine-chart #combine-chart-container", ticks, combinelabels, combinedatas);
*/
		
	},

	//init flotchart
	$.FlotChart = new FlotChart, $.FlotChart.Constructor =
	FlotChart

}(window.jQuery),

//initializing flotchart
function($) {
	"use strict";
	$.FlotChart.init()
}(window.jQuery);


