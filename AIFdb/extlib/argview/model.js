var edges = [];
var nodes = [];
var links = {};

function Edge() {
    this.fromID = '';
    this.toID = '';
}

function Node() {
    this.nodeID = 0;
    this.type = '';
    this.text = '';
    this.x = 0;
    this.y = 0;
}

function newNode(nodeID, type, text, x, y){
    var n = new Node;
    n.nodeID = nodeID;
    n.type = type;
    n.text = text;
    n.x = x;
    n.y = y;
    nodes[nodeID] = n;
}

function Link() {
    this.linkID = 0;
    this.fromID = 0;
    this.toID = 0;
    this.type = '';
    this.text = '';
    this.schemeID = 0;
}

function newLink(lID, fromID, toID, type, text, schemeID){
    var l = new Link;
    l.linkID = lID;
    l.fromID = fromID;
    l.toID = toID;
    l.type = type;
    l.text = text;
    l.schemeID = schemeID;
    links[lID] = l;
}

function delLink(lID){
    //links.splice(lID,1);
    delete links[lID];
}

function saveSet(){
    $.post("helpers/save.php", { data: JSON.stringify(links) },
        function(reply) {
            alert(reply);
        }
    );

    //alert(JSON.stringify(links));
    //l = links.length;
    //for (var i = l-1; i >= 0; i--) {
    //    alert(links[i].fromID);
    //}
}
