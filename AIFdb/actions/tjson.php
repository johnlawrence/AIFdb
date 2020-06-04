<?php

class TjsonAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($this->arg('multi')){
            $post_data = file_get_contents('php://input');
            $insets = Nodes::getInSet($post_data);

            $JSON_nodes = array();
            $JSON_edges = array();
            $JSON_locutions = array();
            while($insets->fetch()){
                $json = array();
                $json['nodeID'] = $insets->nodeID;
                $json['text'] = $insets->text;
                $json['type'] = $insets->type;
                $json['timestamp'] = $insets->timestamp;
                $JSON_nodes[] = json_encode($json);
            }

            $einsets = Edges::getInSet($post_data);
            while($einsets->fetch()){
                $JSON_edges[] = $einsets->encodeJSON();
            }
    
            $linsets = Locutions::getInSet($post_data);
            while($linsets->fetch()){
                $JSON_locutions[] = $linsets->encodeJSON();
            }

            $node_output = implode(',', $JSON_nodes);
            $edge_output = implode(',', $JSON_edges);
            $locution_output = implode(',', $JSON_locutions);
            $output = '{"nodes":[' . $node_output . '],"edges":[' . $edge_output . '],"locutions":[' . $locution_output . ']}';
            common_template('clean', '', $output);
        }elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
        }elseif($this->arg('nodesetid')){
        }elseif($this->arg('nodesets')){
        }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }
}
