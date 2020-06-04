function Init1(evt, nodeset){
    SVGRoot1 = document.getElementById('inline1');
    TrueCoords1 = SVGRoot1.createSVGPoint();
    GrabPoint1 = SVGRoot1.createSVGPoint();
    loadMap(1, SVGRoot1, nodeset);
}

function loadMap(svgno, svgr, mapno) {
    mwidth = $("#inline"+svgno).width();
    mheight = $("#inline"+svgno).height();

    nodeSetID = mapno;
    $.getJSON( "index.php?action=layjson&nodesetid="+nodeSetID, function(ldata) {
        $.getJSON( "json/"+nodeSetID, function(data) {
            var nodelist = {};

            $.each(data.nodes, function(idx, node) {
                visi = true;
                if(node.type == "YA" && node.text.indexOf('AnalysesAs') >= 0){
                    visi = false;
                }else if(node.type == "L" && node.text.indexOf('Annot: ') >= 0){
                    visi = false;
                }

                if(node.nodeID in ldata){
                    xpos = parseInt(ldata[node.nodeID]["x"]);
                    xpos = xpos*0.8;
                    ypos = parseInt(ldata[node.nodeID]["y"]);
                    if(xpos > mwidth-100){ mwidth = xpos+100; }
                    if(ypos > mheight-100){ mheight = ypos+100; }
                }else{
                    xpos = 10;
                    ypos = 10;
                    visi = false;
                }

                if(node.type == 'I' || node.type == 'L'){
                    newNode(node.nodeID, node.type, node.text, xpos, ypos);
                }

                if(visi){
                    AddNode(node.text, node.type, node.nodeID, xpos, ypos, svgr);
                    nodelist[node.nodeID] = 'x';
                }

            });
            $('#inline'+svgno).css({'height':mheight+'px'});
            $('#inline'+svgno).css({'width':mwidth+'px'});

            $.each(data.edges, function(idx, edge) {
                if(edge.fromID in nodelist && edge.toID in nodelist){
                    AddEdge(edge.fromID, edge.toID, svgr);
                }
            });

            $("#map").height($(window).height()-110);
            $("#map").css("background-image", "none");
            var panZoomTiger = svgPanZoom('#inline1', {
              zoomEnabled: true,
              controlIconsEnabled: true,
              fit: true,
              center: true,
              minZoom: 0.2,
              maxZoom: 2
            });
        });
    });
    return false;
}












function Grab(evt){
    if(evt.target.nodeName == 'rect'){
        var targetElement = evt.target.parentNode;
    }else if(evt.target.nodeName == 'tspan'){
        var targetElement = evt.target.parentNode.parentNode;
    }else{
        var targetElement = evt.target;
    }

    if ( targetElement.getAttributeNS(null, 'focusable') ){
        FromNode = targetElement;
        FromID = FromNode.getAttributeNS(null, 'id');
        Focus(evt, targetElement);
        if(window.linkfrom == 0){
            window.linkfrom = FromID;
            tdc = ".td" + FromID;
            //$("tr").hide();
            $("td").filter(tdc).parent().css("background-color", "#eef");
            //$("td").filter(tdc).parent().show();
        }else{
            window.linkto = FromID;
            editpopup();
            $("tr").show();
            $("tr").css('background-color', '');
        }
    }else{
        window.linkfrom = 0;
        UnFocus(null, CurrentFocus);
        //$("tr").show();
        $("tr").css('background-color', '');
    }
}

function NNclick(nid){
    if(window.linkfrom == 0){
        window.linkfrom = nid;
        tdc = ".td" + FromID;
    }else{
        window.linkto = nid;
        editpopup();
        $("tr").show();
        $("tr").css('background-color', '');
    }
}

function Focus(evt, focusElement){
    UnFocus(null, CurrentFocus);
    CurrentFocus = focusElement;
    currentIndex = CurrentFocus.getAttributeNS(null, 'nav-index');

    if ( !CurrentFocus && evt ){
        CurrentFocus = evt.target;
    }

    if ( CurrentFocus ){
        rect = focusElement.getElementsByTagName('rect')[0];
        rect.style.setProperty('stroke-width', 2);
    }
}

function UnFocus(evt, unfocusElement){
    var focusElement = unfocusElement;
    if ( !unfocusElement && evt ){
        unfocusElement = evt.target;
    }

    if ( unfocusElement ){
        rect = unfocusElement.getElementsByTagName('rect')[0];
        rect.style.setProperty('stroke-width', 1);
    }
}


function AddNode(txt, type, nid, nx, ny, svgr){
    var phraseArray = [];
    if(txt.length > 36){
        var wa = txt.split(' ');
        line = "";
        for (var i=0;i<wa.length;i++) {
            word = wa[i];
            if(line.length == 0){
                line = word;
            }else if(line.length + word.length <= 36){
                line = line + ' ' + word;
            }else{
                phraseArray.push(line);
                line = word;
            }
        }
        phraseArray.push(line);
    }else{
        phraseArray.push(txt);
    }

    var g = document.createElementNS("http://www.w3.org/2000/svg", "g");
    g.setAttribute('id', nid);
    g.setAttribute('focusable', 'true');

    var ntext=document.createElementNS("http://www.w3.org/2000/svg", "text");
    ntext.setAttribute('x', nx);
    ntext.setAttribute('y', ny);
    ntext.setAttribute('style', 'font-family: sans-serif; font-weight: normal; font-style: normal;font-size: 10px;');


    for(var i=0;i<phraseArray.length;i++){
        var tspan = document.createElementNS("http://www.w3.org/2000/svg", "tspan");
        tspan.setAttribute('text-anchor','middle');
        tspan.setAttribute('x', nx);
        tspan.setAttribute('dy', 14);
        var myText = document.createTextNode(phraseArray[i]);
        tspan.appendChild(myText);
        ntext.appendChild(tspan);
    }

    g.appendChild(ntext)

    svgr.appendChild(g)

    var textbox = ntext.getBBox();
    var textwidth = textbox.width;
    var textheight = textbox.height;
    var nbox=document.createElementNS("http://www.w3.org/2000/svg", "rect")
    if(type == 'I' || type == 'L'){
        nbox.setAttribute('x', nx-(textwidth/2)-16);
        nbox.setAttribute('y', ny-2);
        nbox.setAttribute('width', textwidth+32);
        nbox.setAttribute('height', textheight+14);
        nbox.setAttribute('rx', '5');
        nbox.setAttribute('ry', '5');
    }else{
        nbox.setAttribute('x', nx-(textwidth/2)-16);
        nbox.setAttribute('y', ny-7);
        nbox.setAttribute('width', textwidth+32);
        nbox.setAttribute('height', textheight+24);
        nbox.setAttribute('rx', (textwidth+32)/2);
        nbox.setAttribute('ry', (textheight+24)/2);
    }

    if(type == 'RA'){
        nbox.setAttribute('style', 'fill:#def8e9;stroke:#2ecc71;stroke-width:1;');
    }else if(type == 'CA'){
        nbox.setAttribute('style', 'fill:#fbdedb;stroke:#e74c3c;stroke-width:1;');
    }else if(type == 'YA'){
        nbox.setAttribute('style', 'fill:#fdf6d9;stroke:#f1c40f;stroke-width:1;');
    }else if(type == 'TA'){
        nbox.setAttribute('style', 'fill:#eee3f3;stroke:#9b59b6;stroke-width:1;');
    }else if(type == 'MA'){
        nbox.setAttribute('style', 'fill:#fbeadb;stroke:#e67e22;stroke-width:1;');
    }else{
        nbox.setAttribute('style', 'fill:#ddeef9;stroke:#3498db;stroke-width:1;');
    }

    g.appendChild(nbox)
    g.appendChild(ntext)
}

function UpdateEdge(e){
    edgeID = 'n' + e.fromID + '-n' + e.toID;
    ee = document.getElementById(edgeID);
    nf = document.getElementById(e.fromID).getElementsByTagName('rect')[0];
    nt = document.getElementById(e.toID).getElementsByTagName('rect')[0];
    fw = parseInt(nf.getAttributeNS(null, 'width'));
    fh = parseInt(nf.getAttributeNS(null, 'height'));
    tw = parseInt(nt.getAttributeNS(null, 'width'));
    th = parseInt(nt.getAttributeNS(null, 'height'));

    fx = parseInt(nf.getAttributeNS(null, 'x'));
    fy = parseInt(nf.getAttributeNS(null, 'y'));
    tx = parseInt(nt.getAttributeNS(null, 'x'));
    ty = parseInt(nt.getAttributeNS(null, 'y'));

    curve_offset = 80;
    efx = fx + (fw/2);
    efy = fy + (fh/2);

    if(Math.abs(fy-ty) > Math.abs(fx-tx)) { // join top to bottom
        if(fy>ty){ // from below to
            if(fy-ty < curve_offset*2){
                curve_offset = (fy-ty)/2;
            }
            //efx = fx + (fw/2);
            //efy = fy;
            etx = tx + (tw/2);
            ety = ty + th;
            cp1y = efy-curve_offset;
            cp2y = ety+curve_offset;
        }else{
            if(ty-fy < curve_offset*2){
                curve_offset = (ty-fy)/2;
            }
            //efx = fx + (fw/2);
            //efy = fy + fh;
            etx = tx + (tw/2);
            ety = ty;
            cp1y = efy+curve_offset;
            cp2y = ety-curve_offset;
        }
        cp1x = efx;
        cp2x = etx;
    }else{ // join side to side
        if(fx>tx){ // from right of to
            if(fx-tx < curve_offset*2){
                curve_offset = (fx-tx)/2;
            }
            //efx = fx;
            //efy = fy + (fh/2);
            etx = tx + tw;
            ety = ty + (th/2);
            cp1x = efx-curve_offset;
            cp2x = etx+curve_offset;
        }else{
            if(tx-fx < curve_offset*2){
                curve_offset = (tx-fx)/2;
            }
            //efx = fx + fw;
            //efy = fy + (fh/2);
            etx = tx;
            ety = ty + (th/2);
            cp1x = efx+curve_offset;
            cp2x = etx-curve_offset;
        }
        cp1y = efy;
        cp2y = ety;
    }

    pd = 'M'+efx+','+efy+' C'+cp1x+','+cp1y+' '+cp2x+','+cp2y+' '+etx+','+ety;
    ee.setAttributeNS(null, 'd', pd);
}

function AddEdge(FromID, ToID, svgr){
    var nedge=document.createElementNS("http://www.w3.org/2000/svg", "path");
    nedge.setAttribute('id', 'n'+FromID+'-n'+ToID);
    nedge.setAttribute('stroke-width', '1');
    nedge.setAttribute('fill', 'none');
    nedge.setAttribute('stroke', 'black');
    nedge.setAttribute('d', 'M80,30 C200,30 30,380 200,380');
    nedge.setAttribute('marker-end', 'url(#head3)');
    svgr.insertBefore(nedge, svgr.childNodes[0]);
    var edge = new Edge;
    edge.fromID = FromID;
    edge.toID = ToID;
    edges.push(edge);
    UpdateEdge(edge);
}

function addLink(){
    fromnode = nodes[window.linkfrom];
    tonode = nodes[window.linkto];
    linktype = document.getElementById("s_type").value;
    if(linktype == 'RA'){
        var ssel = document.getElementById("s_ischeme");
        linkscheme = ssel.value;
        if(ssel.selectedIndex == 0){
            linkschemet = 'Default Inference';
            linkscheme = '72';
        }else{
            linkschemet = ssel.options[ssel.selectedIndex].text;
        }
    }else if(linktype == 'CA'){
        var ssel = document.getElementById("s_cscheme");
        linkscheme = ssel.value;
        if(ssel.selectedIndex == 0){
            linkschemet = 'Default Conflict';
            linkscheme = '71';
        }else{
            linkschemet = ssel.options[ssel.selectedIndex].text;
        }
    }else if(linktype == 'YA'){
        var ssel = document.getElementById("s_lscheme");
        linkscheme = ssel.value;
        if(ssel.selectedIndex == 0){
            linkschemet = 'Default Illocuting';
            linkscheme = '168';
        }else{
            linkschemet = ssel.options[ssel.selectedIndex].text;
        }
    }else if(linktype == 'MA'){
        var ssel = document.getElementById("s_mscheme");
        linkscheme = ssel.value;
        if(ssel.selectedIndex == 0){
            linkschemet = 'Default Rephrase';
            linkscheme = '144';
        }else{
            linkschemet = ssel.options[ssel.selectedIndex].text;
        }
    }else if(linktype == 'PA'){
        var ssel = document.getElementById("s_pscheme");
        linkscheme = ssel.value;
        if(ssel.selectedIndex == 0){
            linkschemet = 'Default Preference';
            linkscheme = '161';
        }else{
            linkschemet = ssel.options[ssel.selectedIndex].text;
        }
    }else if(linktype == 'TA'){
        var ssel = document.getElementById("s_tscheme");
        linkscheme = ssel.value;
        if(ssel.selectedIndex == 0){
            linkschemet = 'Default Transition';
            linkscheme = '82';
        }else{
            linkschemet = ssel.options[ssel.selectedIndex].text;
        }
    }
    window.linkindex = window.linkindex + 1;
    newNode('e'+window.linkindex, linktype, window.linkindex+' - '+linkschemet, -100, -100);
    newLink(window.linkindex, fromnode.nodeID, tonode.nodeID, linktype, linkschemet, linkscheme);
    $('#linktable').append('<tr><td style="width:4%;">' + window.linkindex + '</td><td style="width:27%; text-align: right;" class="td'+fromnode.nodeID+'">' + fromnode.text + '</td><td style="width:5%; text-align: right;">&rarr;</td><td style="width:27%; text-align: center; font-weight: bold;"><a onClick="NNclick(\'e' + window.linkindex + '\');" style="cursor:pointer;">' + linkschemet + '</a></td><td style="width:5%">&rarr;</td><td style="width:27%" class="td'+tonode.nodeID+'">' + tonode.text + '</td><td style="width: 5%;"><button onClick="delLink(' + window.linkindex + ');$(this).closest(\'tr\').remove();" type="button" class="btn btn-default" aria-label="Left Align"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></tr>');
    window.linkfrom = 0;
    window.linkto = 0;
    UnFocus(null, CurrentFocus);
}

function editpopup() {
    fromnode = nodes[window.linkfrom];
    tonode = nodes[window.linkto];
    $('#fromNdetail').text(fromnode.text);
    $('#toNdetail').text(tonode.text);

    $('#n_text').hide(); $('#n_text_label').hide();
    $('#s_type').hide(); $('#s_type_label').hide();
    $('#s_ischeme').show(); $('#s_ischeme_label').show();
    $('#s_cscheme').hide(); $('#s_cscheme_label').hide();
    $('#s_lscheme').hide(); $('#s_lscheme_label').hide();
    $('#s_mscheme').hide(); $('#s_mscheme_label').hide();
    $('#s_pscheme').hide(); $('#s_pscheme_label').hide();
    $('#s_tscheme').hide(); $('#s_tscheme_label').hide();
    $('#descriptor_selects').hide();
    $('#cq_selects').hide();
    $('#s_sset').hide(); $('#s_sset_label').hide();

    $('#s_type').empty();
    $('#s_type').append('<option value="RA">RA</option>');
    $('#s_type').append('<option value="CA">CA</option>');
    $('#s_type').append('<option value="YA">YA</option>');
    $('#s_type').append('<option value="TA">TA</option>');
    $('#s_type').append('<option value="MA">MA</option>');
    $('#s_type').append('<option value="PA">PA</option>');

    $('#s_type').show();
    $('#s_type_label').show();
    //$('#s_type').val(node.type);

    $('#modal-bg').show();
    $('#node_edit').slideDown(100, function() {
        $('#n_text').focus();
    });
}

function swapNodes() {
    ff = window.linkfrom;
    window.linkfrom = window.linkto;
    window.linkto = ff;
    fromnode = nodes[window.linkfrom];
    tonode = nodes[window.linkto];
    $('#fromNdetail').text(fromnode.text);
    $('#toNdetail').text(tonode.text);
}


function showschemes(type) {
    if(type == 'RA'){
        $('#s_ischeme').show();
        $('#s_ischeme_label').show();
        $('#s_cscheme').hide();
        $('#s_cscheme_label').hide();
        $('#s_lscheme').hide();
        $('#s_lscheme_label').hide();
        $('#s_mscheme').hide();
        $('#s_mscheme_label').hide();
        $('#s_pscheme').hide();
        $('#s_pscheme_label').hide();
        $('#s_tscheme').hide();
        $('#s_tscheme_label').hide();
    }else if(type == 'CA'){
        $('#s_ischeme').hide();
        $('#s_ischeme_label').hide();
        $('#s_cscheme').show();
        $('#s_cscheme_label').show();
        $('#s_lscheme').hide();
        $('#s_lscheme_label').hide();
        $('#s_mscheme').hide();
        $('#s_mscheme_label').hide();
        $('#s_pscheme').hide();
        $('#s_pscheme_label').hide();
        $('#s_tscheme').hide();
        $('#s_tscheme_label').hide();
    }else if(type == 'YA'){
        $('#s_ischeme').hide();
        $('#s_ischeme_label').hide();
        $('#s_cscheme').hide();
        $('#s_cscheme_label').hide();
        $('#s_lscheme').show();
        $('#s_lscheme_label').show();
        $('#s_mscheme').hide();
        $('#s_mscheme_label').hide();
        $('#s_pscheme').hide();
        $('#s_pscheme_label').hide();
        $('#s_tscheme').hide();
        $('#s_tscheme_label').hide();
    }else if(type == 'MA'){
        $('#s_ischeme').hide();
        $('#s_ischeme_label').hide();
        $('#s_cscheme').hide();
        $('#s_cscheme_label').hide();
        $('#s_lscheme').hide();
        $('#s_lscheme_label').hide();
        $('#s_mscheme').show();
        $('#s_mscheme_label').show();
        $('#s_pscheme').hide();
        $('#s_pscheme_label').hide();
        $('#s_tscheme').hide();
        $('#s_tscheme_label').hide();
    }else if(type == 'PA'){
        $('#s_ischeme').hide();
        $('#s_ischeme_label').hide();
        $('#s_cscheme').hide();
        $('#s_cscheme_label').hide();
        $('#s_lscheme').hide();
        $('#s_lscheme_label').hide();
        $('#s_mscheme').hide();
        $('#s_mscheme_label').hide();
        $('#s_pscheme').show();
        $('#s_pscheme_label').show();
        $('#s_tscheme').hide();
        $('#s_tscheme_label').hide();
    }else if(type == 'TA'){
        $('#s_ischeme').hide();
        $('#s_ischeme_label').hide();
        $('#s_cscheme').hide();
        $('#s_cscheme_label').hide();
        $('#s_lscheme').hide();
        $('#s_lscheme_label').hide();
        $('#s_mscheme').hide();
        $('#s_mscheme_label').hide();
        $('#s_pscheme').hide();
        $('#s_pscheme_label').hide();
        $('#s_tscheme').show();
        $('#s_tscheme_label').show();
    }

}


var sort_by = function(field, reverse, primer){
    var key = function (x) {return primer ? primer(x[field]) : x[field]};
    return function (a,b) {
        var A = key(a), B = key(b);
        return ((A < B) ? -1 : (A > B) ? +1 : 0) * [-1,1][+!!reverse];
    }
}

function init() {
    $('#container1').css({'height':(($(window).height())-($('#head-bar').height())-120)+'px'});
    $('#container2').css({'height':(($(window).height())-($('#head-bar').height())-120)+'px'});
    $('#modal-bg').css({'height':(($(window).height())-($('#head-bar').height()))+'px'});
    $('#modal-bg').css({'width':($('#container').width())+'px'});

    $.getJSON("helpers/schemes.php", function(json_data){
        schemes = json_data.schemes;
        schemes.sort(sort_by('name', true, function(a){return a.toUpperCase()}));
        for(index in schemes){
            scheme = schemes[index];
            scheme_name = scheme.name.replace(/([a-z])([A-Z])/g, "$1 $2");
            scheme_type = scheme.schemeTypeID

            if(scheme_type == 1 || scheme_type == 2 || scheme_type == 3 || scheme_type == 9){
                $('#s_ischeme').append('<option value="' + scheme.schemeID + '">' + scheme_name + '</option>');
            }else if(scheme_type == 4 || scheme_type == 5){
                $('#s_cscheme').append('<option value="' + scheme.schemeID + '">' + scheme_name + '</option>');
            }else if(scheme_type == 7){
                $('#s_lscheme').append('<option value="' + scheme.schemeID + '">' + scheme_name + '</option>');
            }else if(scheme_type == 11){
                $('#s_mscheme').append('<option value="' + scheme.schemeID + '">' + scheme_name + '</option>');
            }else if(scheme_type == 6){
                $('#s_pscheme').append('<option value="' + scheme.schemeID + '">' + scheme_name + '</option>');
            }else if(scheme_type == 8){
                $('#s_tscheme').append('<option value="' + scheme.schemeID + '">' + scheme_name + '</option>');
            }

        }
    });

    $.getJSON("helpers/schemesets.php", function(json_data){
        window.ssets = {};
        schemesets = json_data.schemesets;
        schemesets.sort(sort_by('name', true, function(a){return a.toUpperCase()}));
        for(index in schemesets){
            schemeset = schemesets[index];
            $('#s_sset').append('<option value="' + schemeset.id + '">' + schemeset.name + '</option>');
            window.ssets[schemeset.id] = schemeset.schemes;
        }
    });
}

