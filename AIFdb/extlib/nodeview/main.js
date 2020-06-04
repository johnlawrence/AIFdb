var odd=true

function getURLParameter(name) {
    return decodeURIComponent(
        (location.search.match(RegExp("[?|&]"+name+'=(.+?)(&|$)'))||[,null])[1]
    );  
}

function nodeVal(node) {
    if(node.type == 'RA'){
        color = "g";
        text = node.type
        mass = 0.6
    }else if(node.type == 'CA'){
        color = "r";
        text = node.type
        mass=0.6
    }else if(node.type == 'I'){
        color = "b";
        text = node.text
        mass=1.0
        if(odd){
            argt = '<div class="odd">'
            odd = false
        }else{
            argt = '<div class="even">'
            odd = true
        }
        argt = argt + '<h4><span class="nodeID">'
        argt = argt + node.nodeID
        argt = argt + '</span></h4><p class="text">'
        argt = argt + node.text + '</p></div>'
        $("#argtext").append(argt)
    }else{
        return false;
    }
    return [color,text,mass];
}

function roundRect(ctx, x, y, width, height, radius, fill, stroke) {
    if (typeof stroke == "undefined" ) { stroke = true; }
    if (typeof radius === "undefined") { radius = 5; }
    ctx.beginPath();
    ctx.moveTo(x + radius, y);
    ctx.lineTo(x + width - radius, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
    ctx.lineTo(x + width, y + height - radius);
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
    ctx.lineTo(x + radius, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
    ctx.lineTo(x, y + radius);
    ctx.quadraticCurveTo(x, y, x + radius, y);
    ctx.closePath();
    if (stroke) {
        ctx.lineWidth = 3;
        ctx.stroke();
    }
    if (fill) {
        ctx.fill();
    }       
}

(function($){

  var Renderer = function(canvas){
    var canvas = $(canvas).get(0)
    var dom = $(canvas)
    var ctx = canvas.getContext("2d");
    var gfx = arbor.Graphics(canvas)
    var particleSystem

    var that = {
      init:function(system){
        particleSystem = system
        particleSystem.screen({size:{width:dom.width(), height:dom.height()},
                    padding:[60,140,60,140]})

        $(window).resize(that.resize)
        that.resize()
        that.initMouseHandling()
      },

      resize:function(){
        canvas.width = $(window).width() * 0.66
        canvas.height = $(window).height() - 110
        particleSystem.screen({size:{width:canvas.width, height:canvas.height}})
        _vignette = null
        that.redraw()
      },
      
      redraw:function(){
        ctx.fillStyle = "white"
        ctx.fillRect(0,0, canvas.width, canvas.height)
        
        var nodeBoxes = {}
        particleSystem.eachNode(function(node, pt){
            var node_width = 200
            if(node.data.color == 'b' || node.data.color == 'w'){
                var node_start_height = 38;
            }else{
                var node_start_height = 20;
            }
            var node_height = node_start_height

            var node_text = (node.data.ndtext) ? node.data.ndtext : "x"

            var phraseArray = []
            //colors top_start, top_end, bottom_start, bottom_end, stroke
            var colors = []
            colors['r'] = ['#ffcdcd','#ffe0e0','#ffcece','#ffebeb','#cc3333']
            colors['g'] = ['#ceffce','#dfffdf','#cfffcf','#eaffea','#33cc33']
            colors['b'] = ['#eff5ff','#e6f0ff','#d4e6ff','#c5deff','#769bc7']
            colors['w'] = ['#f4f4f4','#efefef','#e5e5e5','#dddddd','#6b6b6b']
            if(ctx.measureText(node_text).width > node_width){
                var wa=node_text.split(" "),
                    lastPhrase="",
                    measure=0,
                    max_width = node_width;
                for (var i=0;i<wa.length;i++) {
                    var word=wa[i];
                    measure=ctx.measureText(lastPhrase+word).width;
                    if (measure<node_width-30) {
                        lastPhrase+=(" "+word);
                    }else {
                        phraseArray.push(lastPhrase);
                        node_height += 20
                        max_width = (max_width < measure) ? measure : max_width
                        lastPhrase=word;
                    }
                    if (i===wa.length-1) {
                        phraseArray.push(lastPhrase);
                        max_width = (max_width < measure) ? measure : max_width
                        node_width = (node_width-30 < max_width) ? max_width+30 : node_width
                        break;
                    }
                }
            }else{
                phraseArray.push(node_text)
                node_width = 30 + ctx.measureText(node_text).width
            }
            
            node_top_left_x = pt.x-node_width/2
            node_top_left_y = pt.y-node_height/2
            ctx.strokeStyle = colors[node.data.color][4]
            var grd=ctx.createLinearGradient(node_top_left_x, node_top_left_y, node_top_left_x, node_top_left_y+node_height);
            grd.addColorStop(0,colors[node.data.color][0]);
            grd.addColorStop(0.499,colors[node.data.color][1]);
            grd.addColorStop(0.5,colors[node.data.color][2]);
            grd.addColorStop(1,colors[node.data.color][3]);
            ctx.fillStyle=grd;
            roundRect(ctx, node_top_left_x, node_top_left_y, node_width, node_height, 10, true)
            nodeBoxes[node.name] = [node_top_left_x, node_top_left_y, node_width, node_height]

            for(var i=0;i<phraseArray.length;i++){
                var text_line = phraseArray[i]
                gfx.text(text_line, pt.x, node_top_left_y+(20*i)+(node_start_height/2), {color:"#333333", align:"center", font:"Arial", size:12, baseline:"middle"})
            }
        })
    
        particleSystem.eachEdge(function(edge, pt1, pt2){
            var weight = 1
            var tail = intersect_line_box(pt1, pt2, nodeBoxes[edge.source.name])
            var head = intersect_line_box(tail, pt2, nodeBoxes[edge.target.name])

            ctx.save()
              ctx.strokeStyle = (edge.data.agree) ? "rgba(43,189,77, 1)" : "rgba(214,0,0, 1)"
              ctx.lineWidth = 1
              ctx.beginPath()
              ctx.moveTo(tail.x, tail.y)
              ctx.lineTo(head.x, head.y)
              ctx.stroke()
            ctx.restore()

            // draw arrowhead
            ctx.save()
              // move to the head position of the edge we just drew
              var wt = !isNaN(weight) ? parseFloat(weight) : 1
              var arrowLength = 14 + wt
              var arrowWidth = 6 + wt
              ctx.fillStyle = (edge.data.agree) ? "rgba(43,189,77, 1)" : "rgba(214,0,0, 1)"
              ctx.translate(head.x, head.y);
              ctx.rotate(Math.atan2(head.y - tail.y, head.x - tail.x));

              // delete some of the edge that's already there (so the point isn't hidden)
              ctx.clearRect(-arrowLength/2,-wt/2, arrowLength/2,wt)

              // draw the chevron
              ctx.beginPath();
              ctx.moveTo(-arrowLength, arrowWidth);
              ctx.lineTo(0, 0);
              ctx.lineTo(-arrowLength, -arrowWidth);
              ctx.lineTo(-arrowLength * 0.8, -0);
              ctx.closePath();
              ctx.fill();
            ctx.restore()
        })
      },
      
      initMouseHandling:function(){
        var dragged = null;
        var handler = {
          clicked:function(e){
            var pos = $(canvas).offset();
            _mouseP = arbor.Point(e.pageX-pos.left, e.pageY-pos.top)
            dragged = particleSystem.nearest(_mouseP);

            if (dragged && dragged.node !== null){
                nodeID = dragged.node.name
                $.getJSON(dbURL+'/nodeviewlinks/'+nodeID, function(nv) {
                    var n = {};
                    var e = {};
                    $("#argtext").text('')
                    $.each(nv.nodes, function(i, node) {
                        nd = nodeVal(node);
                        if(node.nodeID == nodeID){
                            n[node.nodeID] = {"color":"w","ndtext":nd[1],"mass":nd[2], "x":0, "y":0, "fixed":true}; 
                        }else{
                            n[node.nodeID] = {"color":nd[0],"ndtext":nd[1],"mass":nd[2]};
                        }
                    });
                    $.each(nv.edges, function(i, edge) {
                        if(typeof(e[edge.from]) === 'undefined'){ e[edge.from] = {};}
                        e[edge.from][edge.to] = {"agree":edge.agree,"length":1};
                    });

                    particleSystem.merge({nodes:n,edges:e});
                });
                dragged.node.fixed = true
            }

            $(canvas).bind('mousemove', handler.dragged)
            $(window).bind('mouseup', handler.dropped)

            return false
          },
          dragged:function(e){
            var pos = $(canvas).offset();
            var s = arbor.Point(e.pageX-pos.left, e.pageY-pos.top)

            if (dragged && dragged.node !== null){
              var p = particleSystem.fromScreen(s)
              dragged.node.p = p
            }

            return false
          },

          dropped:function(e){
            if (dragged===null || dragged.node===undefined) return
            if (dragged.node !== null) dragged.node.fixed = false
            dragged.node.tempMass = 1000
            dragged = null
            $(canvas).unbind('mousemove', handler.dragged)
            $(window).unbind('mouseup', handler.dropped)
            _mouseP = null
            return false
          }
        }
        
        // start listening
        $(canvas).mousedown(handler.clicked);

      },
      
    }

    var intersect_line_line = function(p1, p2, p3, p4){
        var denom = ((p4.y - p3.y)*(p2.x - p1.x) - (p4.x - p3.x)*(p2.y - p1.y));
        if (denom === 0) return false // lines are parallel
        var ua = ((p4.x - p3.x)*(p1.y - p3.y) - (p4.y - p3.y)*(p1.x - p3.x)) / denom;
        var ub = ((p2.x - p1.x)*(p1.y - p3.y) - (p2.y - p1.y)*(p1.x - p3.x)) / denom;

        if (ua < 0 || ua > 1 || ub < 0 || ub > 1)  return false
        return arbor.Point(p1.x + ua * (p2.x - p1.x), p1.y + ua * (p2.y - p1.y));
    }

    var intersect_line_box = function(p1, p2, boxTuple){
        var p3 = {x:boxTuple[0], y:boxTuple[1]},
          w = boxTuple[2],
          h = boxTuple[3]

        var tl = {x: p3.x, y: p3.y};
        var tr = {x: p3.x + w, y: p3.y};
        var bl = {x: p3.x, y: p3.y + h};
        var br = {x: p3.x + w, y: p3.y + h};

        return intersect_line_line(p1, p2, tl, tr) ||
               intersect_line_line(p1, p2, tr, br) ||
               intersect_line_line(p1, p2, br, bl) ||
               intersect_line_line(p1, p2, bl, tl) ||
               false
    }

    return that
  }    

  $(document).ready(function(){
    var sys = arbor.ParticleSystem()
    //sys.parameters({stiffness:800, repulsion:2000, gravity:true, dt:0.015}
    sys.parameters({stiffness:900, repulsion:2000, gravity:true, dt:0.015, friction: 0.5})
    sys.renderer = Renderer("#argcanvas") // our newly created renderer will have its .init() method called shortly by sys...
    
    $.getJSON(dbURL+'/nodeviewlinks/'+nodeID, function(nv) {
        var n = {};
        var e = {};
        $("#argtext").text('')
        $.each(nv.nodes, function(i, node) {
            nd = nodeVal(node);
            if(node.nodeID == nodeID){
                n[node.nodeID] = {"color":"w","ndtext":nd[1],"mass":nd[2], "x":0, "y":0, "fixed":true};
            }else{
                n[node.nodeID] = {"color":nd[0],"ndtext":nd[1],"mass":nd[2]};
            }
        });
        $.each(nv.edges, function(i, edge) {
            if(typeof(e[edge.from]) === 'undefined'){ e[edge.from] = {};}
            if(edge.length == 'l'){
                length = 2.0
            }else{
                length = 0.1
            }
            e[edge.from][edge.to] = {"agree":edge.agree,"length":length};
        });

        sys.graft({nodes:n,edges:e});
    }); 
  })


})(this.jQuery)
