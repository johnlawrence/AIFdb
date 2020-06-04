<?php

class DialogviewAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('nodesetid')){
            $nodeset_id = $this->arg('nodesetid');
            $nodeset = NodeSets::staticGet('nodeSetID', $nodeset_id);

            if (!$nodeset) {
                $this->no_such_nodeset();
                return;
            }

            $output = "";
            $output .= $this->generate_dialog($nodeset);

            common_template('dialogview', '', $output);
        }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }

    function generate_dialog($nodeset) {
        $node = $nodeset->getNodes();
        $dialog_out = "";

        while($node->fetch()){
            if($node->type == "L"){
                $dialog_out .= "<div class='locution'>";
                $dialog_out .= $this->show_locution($node);
                $dialog_out .= "</div>";
            }
        }

        return $dialog_out;
    }

    function show_locution($lnode) {
        $locution_out = "\n";

        $YA = $lnode->getNodesOut();
        while($YA->fetch()){
            if($YA->type == "YA"){
                $INode = $YA->getNodesOut();
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
        $locution_out .= $locutions->firstName . ' ' . $locutions->surname . '</a></p>' . "\n";

        return $locution_out;
    }

}
