<?php

define('ARGS_PER_PAGE', 10);

class SearchAction extends Action {

    var $current_input = array();

    function handle($args) {
        parent::handle($args);

        if($this->arg('am')){
            $this->map_search();
        }elseif($this->arg('q')) {
            $this->do_search();
        } else {
            $this->show_form();
        }
    }

    function map_search() {
        $s_text = strip_tags(trim($this->arg('q')));
        $s_sname = strip_tags(trim($this->arg('s')));
        $s_prt = strip_tags(trim($this->arg('p')));

        $page = ($this->arg('page')) ? ($this->arg('page')+0) : 1;

        $sqlc ="FROM nodeSets " . 
               "INNER JOIN " .
               "nodeSetMappings ON nodeSets.nodeSetID = nodeSetMappings.nodeSetID " .
               "INNER JOIN " .        
               "nodes ON nodes.nodeID = nodeSetMappings.nodeID ";

        $w = array();

        if($this->arg('s')){
            $sqlc .= "INNER JOIN schemeFulfillment ON nodes.nodeID = schemeFulfillment.nodeID " .
                     "INNER JOIN schemes ON schemeFulfillment.schemeID = schemes.schemeID ";
            $w[] = "schemes.name='" . $s_sname . "'";
        }
        if($this->arg('p')){
            $sqlc .= "INNER JOIN locutions ON nodes.nodeID = locutions.nodeID " .
                     "INNER JOIN people ON locutions.personID = people.personID ";
            $w[] = "(LOWER(CONCAT(people.firstName, ' ', people.surname)) LIKE LOWER('%" . $s_prt . "%') OR (nodes.type='L' AND nodes.text LIKE '%" . $s_prt . "%'))";

        }
        if($this->arg('q')){
            $w[] = "nodes.type='I' AND nodes.text LIKE '%" . $s_text . "%'";
        }
        if($this->arg('date-1')){
            if($this->arg('date-1-dd')) { $dfrom[0] = $this->arg('date-1-dd'); }
            if($this->arg('date-1-mm')) { $dfrom[1] = $this->arg('date-1-mm'); }
            if($this->arg('date-1'))    { $dfrom[2] = $this->arg('date-1'); }
            if($this->arg('date-2-dd')) { $dto[0] = $this->arg('date-2-dd'); }
            if($this->arg('date-2-mm')) { $dto[1] = $this->arg('date-2-mm'); }
            if($this->arg('date-2'))    { $dto[2] = $this->arg('date-2'); }
            $df = $dfrom[2] . '-' . $dfrom[1] . '-' . $dfrom[0] . ' 00:00:01';
            $dt = $dto[2] . '-' . $dto[1] . '-' . $dto[0] . ' 23:59:59';
            $w[] = "nodes.timestamp>'".$df."' AND nodes.timestamp<'".$dt."'";
        }

        $sqlc .= "WHERE " . implode(" AND ", $w) . " ORDER BY nodeSets.nodeSetID ASC ";

        $asql = "SELECT COUNT(DISTINCT nodeSets.nodeSetID) AS num " .  $sqlc . ";";
        $lsql = "SELECT DISTINCT nodeSets.* " .  $sqlc;

        $nsc = new NodeSets;
        $nsc->query($asql);
        $nsc->fetch();
        $fcnt = $nsc->num;

        $l1 = ARGS_PER_PAGE*($page-1);
        $l2 = ARGS_PER_PAGE;
        $lsql .= "LIMIT " . $l1 . ", " . $l2;
        $lsql .= ';';

        $ns = new NodeSets;
        $ns->query($lsql);

        $r = $fcnt . " " . $cnt . " " . $lsql;
        $r = "";

        if($fcnt > 0){
            $r .= "<div id='rcount'>Page $page of $fcnt results</div>";
        }else{
            $r .= "<div id='rcount'>No results found</div>";
        }

        while($ns->fetch()) {
            $avurl = common_local_url('argview', array('nodesetid' => $ns->nodeSetID));
            $imgurl = common_local_url('diagram', array('nodesetid' => $ns->nodeSetID));

            $node = $ns->getNodes();
            $nsText = "";
            while($node->fetch()){
                if($node->type == "I"){
                    $nsText .= $node->text . ' ';
                }
            }
            $r .= '<div class="nodeset clearfix">';

            $r .= '<a href="' . $avurl . '" class="nodesetimglink">';
            $r .= '<div class="nodesetimg" style=\'background-image:url("' . $imgurl . '");\'></div>';
            $r .= '</a>';

            $r .= '<h3>Argument Map ' . $ns->nodeSetID . '</h3>';
            $r .= '<p class="text">' . preg_replace('/\s+?(\S+)?$/', '', substr($nsText, 0, 301)) . '...</p>';
            $r .= '</div>';
        }

        $ppage_links = "";
        $ppage = $page-1;
        while($ppage > 0 && ($page - $ppage) < 5){
            $ppage_link = $this->pager_link($ppage);
            $ppage_links = $ppage_link . $ppage_links;
            $ppage = $ppage - 1;
        }
        if($page > 1){
            $ppage_links = $this->pager_link($page-1, '&laquo; prev') . $ppage_links;
            $ppage_links = $this->pager_link(1, '&laquo; first') . $ppage_links;
        }

        $max_pages = ceil($fcnt/ARGS_PER_PAGE);
        $npage_links = "";
        $npage = $page+1;
        while($npage <= $max_pages && ($npage-$page) < 5){
            $npage_link = $this->pager_link($npage);
            $npage_links = $npage_links . $npage_link;
            $npage = $npage + 1;
        }
        if($page < $max_pages){
            $npage_links = $npage_links . $this->pager_link($page+1, 'next &raquo;');
            $npage_links = $npage_links . $this->pager_link($max_pages, 'last &raquo;');
        }

        $r .= "<div id='pager'>";
        $r .= $ppage_links . " <a class='apager spager'>$page</a>" . $npage_links;
        $r .= "</div>";

        $r .= "</div>";

        common_template('searchresultsam', 'AIFdb Search', $r, array('q' => $this->arg('q')));
    }

    function do_search() {
        $s_text = strip_tags(trim($this->arg('q')));

        $nodes = new Nodes;
        $nodes->whereAdd("nodes.text LIKE '%$s_text%'");
        $nodes->whereAdd("nodes.type='I'");
        $nodes->orderBy('nodes.nodeID');

        $page = ($this->arg('page')) ? ($this->arg('page')+0) : 1;

        $fcnt = $nodes->find();

        $nodes->limit(ARGS_PER_PAGE*($page-1),ARGS_PER_PAGE+1);
        $cnt = $nodes->find();

        if($fcnt > 0){
            $args_out = "<div id='rcount'>Page $page of $fcnt results</div>";
        }else{
            $args_out = "<div id='rcount'>No results for <strong>$s_text</strong></div>";
        }
        $args_out .= "<div id='notices'>";
        $nodes->fetch();

        for ($i = 0; $i < min($cnt, ARGS_PER_PAGE); $i++) {
            $args_out .= "<div class='arg' id='arg-" . $nodes->nodeID . "'>";
            $args_out .= $this->show_node($nodes, $i, $s_text, $page);

            $ns_out= '<div id="ns'.$nodes->nodeID.'" class="nslist" style="display:none;">';
            $a = $nodes->nodeID;
            $nodeSets = new nodeSets;
            $nodeSetMappings = new nodeSetMappings;
            $nodeSetMappings->joinAdd($nodeSets);
            $nodeSetMappings->whereAdd("nodeSetMappings.nodeID='$a'");
            $ns_cnt = $nodeSetMappings->find();
            while($nodeSetMappings->fetch()){
                $ns_out .= $this->show_nodeset($nodeSetMappings->nodeSetID);
            }
            $ns_out.= '</div>';
            $args_out.= $ns_out;

            $args_out .='</div>';
            $nodes->fetch();
        }

        $ppage_links = "";
        $ppage = $page-1;
        while($ppage > 0 && ($page - $ppage) < 5){
            $ppage_link = $this->pager_link($ppage);
            $ppage_links = $ppage_link . $ppage_links;
            $ppage = $ppage - 1;
        }
        if($page > 1){
            $ppage_links = $this->pager_link($page-1, '&laquo; prev') . $ppage_links;
            $ppage_links = $this->pager_link(1, '&laquo; first') . $ppage_links;
        }

        $max_pages = ceil($fcnt/ARGS_PER_PAGE);
        $npage_links = "";
        $npage = $page+1;
        while($npage <= $max_pages && ($npage-$page) < 5){
            $npage_link = $this->pager_link($npage);
            $npage_links = $npage_links . $npage_link;
            $npage = $npage + 1;
        }
        if($page < $max_pages){
            $npage_links = $npage_links . $this->pager_link($page+1, 'next &raquo;');
            $npage_links = $npage_links . $this->pager_link($max_pages, 'last &raquo;');
        }

        $args_out .= "<div id='pager'>";
        $args_out .= $ppage_links . " <a class='apager spager'>$page</a>" . $npage_links;
        $args_out .= "</div>";

        $args_out .= "</div>";

        $output .= $args_out;

        if (is_numeric($s_text)){
            $nodeSets = new NodeSets;
            $nodeSets->whereAdd("nodeSets.nodeSetID='$s_text'");
            $nscnt = $nodeSets->find();

            if($nscnt > 0){
                $avurl = common_local_url('argview', array('nodesetid' => $s_text));
                $diurl = common_local_url('diagram', array('nodesetid' => $s_text));
                $nssql = "SELECT nodes.* FROM nodes INNER JOIN nodeSetMappings ON nodes.nodeID = nodeSetMappings.nodeID WHERE nodeSetMappings.nodeSetID=".$s_text." AND nodes.type='I';";
                $nsn = new nodes;
                $nsn->query($nssql);
                while($nsn->fetch()){
                    $nsText .= $nsn->text . ' ';
                }
                $nsr = "<div class='nsr clearfix'>";
                $nsr.= '<a href="' . $avurl . '" class="nodesetimglink"><div class="nodesetimg" style="background-image:url(' . $diurl . ');"></div></a>';
                $nsr.= "<h3><a href='" . $avurl . "'>Argument Map " . $s_text . "</a></h3>";
                $nsr.= '<p class="text">' . preg_replace('/\s+?(\S+)?$/', '', substr($nsText, 0, 301)) . '...</p>';
                $nsr.= '</div>';
                $output = $nsr . $output;
            }
        }


        common_template('searchresults', 'AIFdb Search', $output, array('q' => $this->arg('q')));
    }

    function show_form($error=NULL) {
        $output = "";
        $scm = new Schemes;
        $scf = new SchemeFulfillment;
        $scm->joinAdd($scf, "RIGHT");
        $scm->groupBy('schemes.name');
        $scm->orderBy('schemes.name');
        $scm->selectAdd();
        $scm->selectAdd('name');
        $scm->find();
        while($scm->fetch()){
            $schemes[] = $scm->name;
        }

        $asql = "SELECT COUNT(DISTINCT nodeSets.nodeSetID) AS num FROM nodeSets;";
        $nsc = new NodeSets;
        $nsc->query($asql);
        $nsc->fetch();
        $fcnt = $nsc->num;

        $asql = "SELECT COUNT(DISTINCT nodes.nodeID) AS num FROM nodes;";
        $nsc = new Nodes;
        $nsc->query($asql);
        $nsc->fetch();
        $ncnt = $nsc->num;

        $output = "Search <strong>" . number_format($ncnt) . "</strong> nodes in <strong>" . number_format($fcnt) . "</strong> argument maps";
        common_template('searchform', 'AIFdb Search', $output, array('schemes' => $schemes)); 
    }

    function show_nodeset($nodeSetID) {
        $avurl = common_local_url('argview', array('nodesetid' => $nodeSetID));
        $r = "<div class='nslink'>";
        $r.= "<span class='nsarrow'> &#8627; </span>";
        $r.= "<a href='$avurl'>Argument Map $nodeSetID</a>";
        $r.= "</div>";

        return $r;
    }

    function show_node($arg, $num, $s_text, $page) {
        $num = (ARGS_PER_PAGE*($page-1))+$num+1;
        $pos = stripos($arg->text, $s_text);
        $m1 = substr($arg->text, 0, $pos);
        $m2 = substr($arg->text, $pos, strlen($s_text));
        $m3 = substr($arg->text, $pos+strlen($s_text));
        $match_text = $m1 . '<strong>' . $m2 . '</strong>' . $m3;

        $arg_out = "\n                ";
        $argurl = common_local_url('nodeview', array('node' => $arg->nodeID));
        $arg_out .= "<span class='content'><a href='$argurl' class='stext'>";
        $arg_out .= $match_text;
        $arg_out .= "</a></span>";
        $arg_out .= "<a href='#' onClick='";
        $arg_out .= 'document.getElementById("ns' . $arg->nodeID . '").style.display="block";document.getElementById("hns' . $arg->nodeID . '").style.display="block";this.style.display="none";return false;';
        $arg_out .= "' class='showns' id='sns" . $arg->nodeID . "'>Show Argument Maps &raquo;</a>";
        $arg_out .= "<a href='#' onClick='";
        $arg_out .= 'document.getElementById("ns' . $arg->nodeID . '").style.display="none";document.getElementById("sns' . $arg->nodeID . '").style.display="block";this.style.display="none";return false;';
        $arg_out .= "' class='showns' id='hns" . $arg->nodeID . "' style='display:none;'>Hide Argument Maps &laquo;</a>";

        return $arg_out;
    }

    function pager_link($page, $text=''){
        if($text == ''){
            $text = $page;
        }
        $pager_link = " <a href='" . common_local_url('search');
        $pager_link .= "?q=" . $this->arg('q');
        if($this->arg('date-1-dd')) { $pager_link .= "&date-1-dd=" . $this->arg('date-1-dd'); }
        if($this->arg('date-1-mm')) { $pager_link .= "&date-1-mm=" . $this->arg('date-1-mm'); }
        if($this->arg('date-1'))    { $pager_link .= "&date-1=" . $this->arg('date-1'); }
        if($this->arg('date-2-dd')) { $pager_link .= "&date-2-dd=" . $this->arg('date-2-dd'); }
        if($this->arg('date-2-mm')) { $pager_link .= "&date-2-mm=" . $this->arg('date-2-mm'); }
        if($this->arg('date-2'))    { $pager_link .= "&date-2=" . $this->arg('date-2'); }
        if($this->arg('am'))   { $pager_link .= "&am=" . $this->arg('am'); }
        if($this->arg('s'))    { $pager_link .= "&s=" . $this->arg('s'); }
        if($this->arg('p'))    { $pager_link .= "&p=" . $this->arg('p'); }

        $pager_link .= "&page=" . $page;
        $pager_link .= "' class='apager'>" . $text . "</a>";
        return $pager_link;
    }
}
