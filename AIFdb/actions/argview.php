<?php

class ArgviewAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('nodesetid')){
            if($this->arg('plus')){
                $plus = true;
            }else{
                $plus = false;
            }
            $nodeset_id = $this->arg('nodesetid');
            $nodeset = NodeSets::staticGet('nodeSetID', $nodeset_id);

            if (!$nodeset) {
                $this->no_such_nodeset();
                return;
            }

            $output = "";
            $output .= $this->generate_arg($nodeset, $nodeset_id);

            common_template('argview', '', $output, array('nodeset' => $nodeset_id, 'plus' => $plus));
        }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }

    function generate_arg($nodeset, $nodeset_id) {
        $node = $nodeset->getNodes2();
        $nodes_out = "";
        $dialogue_out = "";
        $show_list = TRUE;
        $node->find();
        $alt = "odd";
        while($node->fetch()){
            if($node->type == "I"){
                $alt=($alt=="even")?"odd":"even";
                $nodes_out .= "<div class='".$alt."'>";
                $nodes_out .= $this->show_node($node);
                $nodes_out .= "</div>";
            }
            if($node->type == "L"){
                $show_list = FALSE;
                $dialog_out .= "<div class='locution'>";
                $dialog_out .= $this->show_locution($node, $nodeset_id);
                $dialog_out .= "</div>";
            }
        }

        if(! $this->arg('plus')){
            $show_list = TRUE;
        }

        $return = $show_list ? $nodes_out : $dialog_out;
        return $return;
    }

    function show_node($node) {
        $node_out = "\n";
        $node_out .= '<h4><span class="nodeID">' . $node->nodeID . '</span></h4>' . "\n";
        $node_out .= '<p class="text">' . $node->text. '</p>' . "\n";

        $edgeTo = $node->getNodesOutInSet($this->arg('nodesetid'));
        while($edgeTo->fetch()){
            if($edgeTo->type == "RA"){
                $node_out .= "<p><span class='support'>Supports: </span>";
            }elseif($edgeTo->type == "CA"){
                $node_out .= "<p><span class='attack'>Attacks: </span>";
            }
            $target = $edgeTo->getNodesOut();
            while($target->fetch()){
                $node_out .= $target->nodeID . "</p>";
            }
        }
        return $node_out;
    }

    function show_locution($lnode, $nodeset_id) {
        $locution_out = "\n";

        $YA = $lnode->getNodesOutInSet($nodeset_id);
        while($YA->fetch()){
            if($YA->type == "YA"){
                $INode = $YA->getNodesOutInSet($nodeset_id);
                $INode->fetch();
                $locution_out .= '<div class="bubble"><p>' . $INode->text. '</p></div>' . "\n";
            }
        }

        $locutions = new locutions;
        $nodes = new nodes;
        $people = new people;
        $locutions->joinAdd($nodes);
        $locutions->joinAdd($people);
        $locutions->whereAdd("nodes.nodeID='$lnode->nodeID'");
        $l_cnt = $locutions->find();
        $locutions->fetch();
        $locution_out .= '<p class="locutor">';
        $locution_out .= '<a href="' . $locutions->source . '">';
        $locutor = $locutions->firstName . ' ' . $locutions->surname;
        $pos = strpos($locutor, 'tumblr');
        if($pos !== false){
            $pattern = '/.*http:\/\/(.*).tumblr.com.*/';
            $replace = '$1 on tumblr';
            $locutor = preg_replace($pattern, $replace, $locutor);
        }
        $pos = strpos($locutor, 'blogspot');
        if($pos !== false){
            $pattern = '/.*http:\/\/(.*).blogspot.*/';
            $replace = '$1 on Blogger';
            $locutor = preg_replace($pattern, $replace, $locutor);
        }
        $locution_out .= $locutor . '</a></p>' . "\n";

        return $locution_out;
    }

}
