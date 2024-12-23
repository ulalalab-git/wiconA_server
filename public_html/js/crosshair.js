/**
 * @license
 * Copyright 2015 Petr Shevtsov (petr.shevtsov@gmail.com)
 * MIT-licensed (http://opensource.org/licenses/MIT)
 */

/*global Dygraph:false */
/*jshint globalstrict: true */
Dygraph.Plugins.Crosshair = (function() {
  "use strict";

  /**
   * Creates the crosshair
   *
   * @constructor
   */

  var crosshair = function(opt_options) {
    this.canvas_ = document.createElement("canvas");
    opt_options = opt_options || {};
    this.direction_ = opt_options.direction || null;
  };

  crosshair.prototype.toString = function() {
    return "Crosshair Plugin";
  };

  /**
   * @param {Dygraph} g Graph instance.
   * @return {object.<string, function(ev)>} Mapping of event names to callbacks.
   */
  crosshair.prototype.activate = function(g) {
    g.graphDiv.appendChild(this.canvas_);

    return {
      select: this.select,
      deselect: this.deselect
    };
  };

  crosshair.prototype.select = function(e) {
    if (this.direction_ === null) {
      return;
    }

    var width = e.dygraph.width_;
    var height = e.dygraph.height_ -20;
    this.canvas_.width = width;
    this.canvas_.height = height;
    this.canvas_.style.width = width + "px";    // for IE
    this.canvas_.style.height = height + "px";  // for IE


    var ctx = this.canvas_.getContext("2d");
    ctx.clearRect(0, 0, width, height);
    ctx.strokeStyle = "#3481f6";
    ctx.beginPath();

    var canvasx = Math.floor(e.dygraph.selPoints_[0].canvasx) + 0.5; // crisper rendering
    var canvasy = Math.floor(e.dygraph.selPoints_[0].canvasy) + 0.5; 
    if (this.direction_ === "vertical" || this.direction_ === "both") {
      ctx.moveTo(canvasx, canvasy);
      ctx.lineTo(canvasx, height);
    }

    if (this.direction_ === "horizontal" || this.direction_ === "both") {
      for (var i = 0; i < e.dygraph.selPoints_.length; i++) {
        var canvasy = Math.floor(e.dygraph.selPoints_[i].canvasy) + 0.5; // crisper rendering
        ctx.moveTo(0, canvasy);
        ctx.lineTo(width, canvasy);
      }
    }

    ctx.stroke();
    ctx.closePath();
  };

  crosshair.prototype.deselect = function(e) {
    var ctx = this.canvas_.getContext("2d");
	/*
	$(".dygraph-legend").css({
		left : this.canvas_.width,
		top : this.canvas_.height
	})*/
		ctx.clearRect(0, 0, this.canvas_.width, this.canvas_.height);	
	
  };

  crosshair.prototype.destroy = function() {
    this.canvas_ = null;
  };

  return crosshair;
})();