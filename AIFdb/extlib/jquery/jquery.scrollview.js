/**
 * ScrollView - jQuery plugin 0.1
 *
 * This plugin supplies contents view by grab and drag scroll.
 *
 * Copyright (c) 2009 Toshimitsu Takahashi
 *
 * Released under the MIT license.
 *
 * == Usage =======================
 *   // apply to block element.
 *   $("#map").scrollview();
 *   
 *   // with setting grab and drag icon urls.
 *   //   grab: the cursor when mouse button is up.
 *   //   grabbing: the cursor when mouse button is down.
 *   //
 *   $("#map".scrollview({
 *     grab : "images/openhand.cur",
 *     grabbing : "images/closedhand.cur"
 *   });
 * ================================
 */
(function() {
    function ScrollView(){ this.initialize.apply(this, arguments) }
    ScrollView.prototype = {
        initialize: function(container, config){
                // setting cursor.
                var gecko = navigator.userAgent.indexOf("Gecko/") != -1;
                var opera = navigator.userAgent.indexOf("Opera/") != -1;
                var mac = navigator.userAgent.indexOf("Mac OS") != -1;
                if (opera) {
                    this.grab = "default";
                    this.grabbing = "move";
                } else if (!(mac && gecko) && config) {
                    if (config.grab) {
                       this.grab = "url(\"" + config.grab + "\"),default";
                    }
                    if (config.grabbing) {
                       this.grabbing = "url(" + config.grabbing + "),move";
                    }
                } else if (gecko) {
                    this.grab = "-moz-grab";
                    this.grabbing = "-moz-grabbing";
                } else {
                    this.grab = "default";
                    this.grabbing = "move";
                }
                
                // Get container and image.
                this.m = $(container);
                this.i = this.m.children().css("cursor", this.grab);
                
                this.isgrabbing = false;
                
                // Set mouse events.
                var self = this;
                this.i.mousedown(function(e){
                        self.startgrab();
                        this.xp = e.pageX;
                        this.yp = e.pageY;
                        return false;
                }).mousemove(function(e){
                        if (!self.isgrabbing) return true;
                        self.scrollTo(this.xp - e.pageX, this.yp - e.pageY);
                        this.xp = e.pageX;
                        this.yp = e.pageY;
                        return false;
                })
                .hover(
                    function () {
                        if(self.m.children("#wmap").children("#imap").data("zoom")){
                            $('#ctz').fadeIn(500);
                        }
                    }, 
                    function () {
                        $('#ctz').fadeOut(200);
                    }
                )
                .mouseout(function(){ self.stopgrab(); })
                .mouseup(function(){ self.stopgrab() })
                .dblclick(function(){
                    var _m = self.m;
                    var imap = _m.children("#wmap").children("#imap");

                    if(imap.data("zoom")){
                        // if zoomed in
                        if(imap.width() == imap.data("w") || imap.height() == imap.data("h")){
                            if(imap.data("zoomby") == "w"){
                                imap.css("width", _m.width()+"px");
                                imap.css("top", (_m.height()-imap.height())/2+"px");
                            }else{
                                imap.css("height", _m.height()+"px");
                                imap.css("left", (_m.width()-imap.width())/2+"px");
                            }
                            $('#ctz').text("Double click to zoom in");
                        }else{
                            var ioff = imap.offset();
                            if(imap.data("zoomby") == "w"){
                                imap.css("width", imap.data("w")+'px');
                                scalev = imap.data("w")/_m.width();
                            }else{
                                imap.css("height", imap.data("h")+'px');
                                scalev = imap.data("h")/_m.height();
                            }
                            
                            var off = _m.offset();
                            var dx = this.xp - off.left;
                            dx = (dx * scalev) - _m.width() / 2;
                            dx = "+=" + dx + "px";
                            var dy = this.yp - ioff.top;
                            dy = (dy * scalev) - _m.height() / 2;
                            dy = "+=" + dy + "px";
                            _m.animate({ scrollLeft:  dx, scrollTop: dy }, 0);

                            $('#ctz').text("Double click to zoom out");
                        }
                    }

                });
                
                this.centering();
        },
        centering: function(){
                var _m = this.m;
                var w = this.i.width() - _m.width();
                var h = this.i.height() - _m.height();
                _m.scrollLeft(w / 2).scrollTop(h / 2);
        },
        startgrab: function(){
                this.isgrabbing = true;
                this.i.css("cursor", this.grabbing);
        },
        stopgrab: function(){
                this.isgrabbing = false;
                this.i.css("cursor", this.grab);
        },
        scrollTo: function(dx, dy){
                var _m = this.m;
                var x = _m.scrollLeft() + dx;
                var y = _m.scrollTop() + dy;
                _m.scrollLeft(x).scrollTop(y);
        }
    };
    
    jQuery.fn.scrollview = function(config){
        return this.each(function(){
            new ScrollView(this, config);
        });
    };
})(jQuery);
