<?php

class GraphmlplussAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('multi')){
            $post_data = file_get_contents('php://input');
            $DT_nodes = array();
            $DT_edges = array();
            $nids = array();
            $ntypes = array();
            $insets = Nodes::getInSet($post_data);

            $plus = false;
            if($this->arg('plus')){
                $plus = true;
            }

            while($insets->fetch()){
                if(!$plus && $insets->type != 'I' && $insets->type != 'RA' && $insets->type != 'CA' && $insets->type != 'MA'){ continue; }
                $DT_nodes[] = $this->show_node($insets);
                $nids[] = $insets->nodeID;
                $ntypes[$insets->nodeID] = $insets->type;
            }

            $einsets = Edges::getInSet($post_data);
            while($einsets->fetch()){
                if(in_array($einsets->fromID, $nids) && in_array($einsets->toID, $nids)){
                    $DT_edges[] = $this->show_edge($einsets, $ntypes);
                }
            }

            $node_output = implode("\n", $DT_nodes);
            $edge_output = implode("\n", $DT_edges);

            $output= $node_output . "\n" . $edge_output;
            common_template('graphml', '', $output, array('nodeset' => $nodeset_id));
        }elseif($this->arg('nodesetid')){
            $nodeset_id = $this->arg('nodesetid');
            $DT_nodes = array();
            $DT_edges = array();
            $nids = array();
            $ntypes = array();
            $insets = Nodes::getInSet($nodeset_id);

            $plus = false;
            if($this->arg('plus')){
                $plus = true;
            }

            while($insets->fetch()){
                if(!$plus && $insets->type != 'I' && $insets->type != 'RA' && $insets->type != 'CA' && $insets->type != 'MA'){ continue; }
                $DT_nodes[] = $this->show_node($insets);
                $nids[] = $insets->nodeID;
                $ntypes[$insets->nodeID] = $insets->type;
            }

            $einsets = Edges::getInSet($nodeset_id);
            while($einsets->fetch()){
                if(in_array($einsets->fromID, $nids) && in_array($einsets->toID, $nids)){
                    $DT_edges[] = $this->show_edge($einsets, $ntypes);
                }
            }

            $node_output = implode("\n", $DT_nodes);
            $edge_output = implode("\n", $DT_edges);
            $nodeset = NodeSets::staticGet('nodeSetID', $nodeset_id);

            if (!$nodeset) {
                $this->no_such_nodeset();
                return;
            }

            $output = "";
            $output.= $node_output;
            $output.= $edge_output;

            common_template('graphml', '', $output, array('nodeset' => $nodeset_id));
        }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }

    function show_node($node) {
        $fc = 'FFFFFF';
        $sc = '';
        $lw = '3.0';
        $t = 'ellipse';
        $w = '12.0';
        $l = '';
        if($node->type == 'RA'){
            $sc = '2ECC71';
        }elseif($node->type == 'CA'){
            $sc = 'E74C3C';
        }elseif($node->type == 'YA'){
            $sc = 'F1C40F';
        }elseif($node->type == 'TA'){
            $sc = '9B59B6';
        }elseif($node->type == 'MA'){
            $sc = 'E67E22';
        }else{
            $sc = '3498DB';
            $fc = 'DDEEF9';
            $lw = '1.0';
            $t = 'roundrectangle';
            $w = '149.0';
            $l = $node->text;
        }
        $node_out = '<node id="n' . $node->nodeID . '"> <data key="d5"> <y:ShapeNode>';
        $node_out.= '<y:Fill color="#' . $fc . '" transparent="false"/> <y:BorderStyle cap="0" color="#' . $sc . '" dashPhase="0.0" join="0" miterLimit="10.0" raised="false" type="custom" width="1.0"/>';
        $node_out.= '<y:NodeLabel alignment="center" autoSizePolicy="node_width" configuration="CroppingLabel" fontFamily="Arial" fontSize="12" fontStyle="plain" hasBackgroundColor="false" hasLineColor="false" height="88.79296875" horizontalTextPosition="center" iconTextGap="4" modelName="internal" modelPosition="c" textColor="#000000" verticalTextPosition="bottom" visible="true" width="149.0" x="4.0" y="6.296875">' . $l . '<y:LabelModel>';
        $node_out.= '<y:SmartNodeLabelModel distance="4.0"/> </y:LabelModel> <y:ModelParameter> <y:SmartNodeLabelModelParameter labelRatioX="-0.5" labelRatioY="0.0" nodeRatioX="-0.5" nodeRatioY="0.0" offsetX="4.0" offsetY="0.0" upX="0.0" upY="-1.0"/> </y:ModelParameter> </y:NodeLabel>';
        $node_out.= '<y:Geometry height="12.0" width="' . $w . '" x="-113.5" y="-230.5"/>';
        $node_out.= '<y:BorderStyle cap="0" color="#' . $sc . '" dashPhase="0.0" join="0" miterLimit="10.0" raised="false" type="custom" width="' . $lw . '"/>';
        $node_out.= '<y:Shape type="' . $t . '"/>';
        $node_out.= '</y:ShapeNode> </data> </node>';
        return $node_out;
    }

    function show_edge($edge, $ntypes) {

        $st = $ntypes[$edge->fromID]; # John, I need a way to get the type of a node given the ID here
        $tt = $ntypes[$edge->toID];

        $et = '';
        $h = 'standard';
        if($st == 'L' || $st == 'I'){
            # edges from locutions or propositions don't have arrowheads and their type if that of the target
            $h = none;
            $et = $tt;
        }else{
            # otherwise, the type of the edge is that of the source
            $et = $st;
        }

        $c = '';
        $t = 'line';
        if($et == 'MA'){
            # edges from or to MAs are dashed
            $t = 'dashed_dotted';
            $c = 'E67E22';
        }elseif($et == 'RA'){
            $c = '2ECC71';
        }elseif($et == 'CA'){
            $t = 'dashed';
            $c = 'E74C3C';
        }elseif($et == 'YA'){
            $c = 'F1C40F';
        }elseif($et == 'TA'){
            $c = '9B59B6';
        }else{
            $c = 'FFFFFF';
        }

        $edge_out = '';
        $edge_out.= '<edge id="e' . $edge->fromID . 'x' .  $edge->toID . '" source="n' . $edge->fromID . '" target="n' . $edge->toID . '">';
        $edge_out.= '<data key="d9"> <y:PolyLineEdge> <y:Path sx="0.0" sy="0.0" tx="0.0" ty="0.0"/> <y:LineStyle color="#' . $c . '" type="' . $t . '" width="3.0"/> <y:Arrows source="none" target="' . $h . '"/> <y:BendStyle smoothed="false"/> </y:PolyLineEdge> </data> </edge>';
        return $edge_out;
    }
}
