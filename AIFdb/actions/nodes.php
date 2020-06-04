<?php

class NodesAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $node_data = json_decode(file_get_contents("php://input"));
            $node_text = str_replace("\n", " ", trim($node_data->{'text'}));
            $existing_node = Nodes::staticGet('text', $node_text);

            if($this->arg('search')){
                $return = preg_replace( "/\"(\d+)\"/", '$1', $existing_node->encodeJSON());
            }else{
                common_auth();
                if($existing_node && strtolower($node_data->{'type'}) != "ra" && strtolower($node_data->{'type'}) != "ca" && strtolower($node_data->{'type'}) != "ya" && strtolower($node_data->{'type'}) != "l" && strtolower($node_data->{'type'}) != "ta" && strtolower($node_data->{'type'}) != "ma" && strtolower($node_data->{'type'}) != "pa"){
                    $return = preg_replace( "/\"(\d+)\"/", '$1', $existing_node->encodeJSON());
                }else{
                    $node = DB_DataObject::factory('nodes');
                    assert($node);
                    $node->text = $node_text;
                    $node->type = strtoupper($node_data->{'type'});
                    $node->timestamp = date("Y-m-d H:i:s", time());
                    $nodeId = $node->insert();
                    $return = "";
                    $return .= $node->encodeJSON();
                }
            }
            common_template('clean', 'return', $return);
        }elseif($this->arg('to')){
            $node = Nodes::staticGet($this->arg('to'));
            $nodes_to = $node->getNodesIn();

            $return = '{"nodes":[';
            $JSON = array();
            while($nodes_to->fetch()){
                $JSON[] = $nodes_to->encodeJSON();
            }
            $return = $return . implode(',', $JSON);
            $return = $return . ']}';

            common_template('clean', '', $return);
        }elseif($this->arg('from')){
            $node = Nodes::staticGet($this->arg('from'));
            $nodes_to = $node->getNodesOut();

            $return = '{"nodes":[';
            $JSON = array();
            while($nodes_to->fetch()){
                $JSON[] = $nodes_to->encodeJSON();
            }
            $return = $return . implode(',', $JSON);
            $return = $return . ']}';

            common_template('clean', '', $return); 
        }else{
            $id = $this->arg('id');
            $node = Nodes::staticGet($id);

            if (!$node) {
                $this->no_such_node();
            }

            $content_types = array('application/json', 'application/rdf+xml', 'text/html');
            $reqtype = http_negotiate_content_type($content_types);

            if($reqtype == 'application/rdf+xml'){
                $return = $this->generate_rdf($node);
                common_template('rdf', '', $return);
            }else{
                $return = "";
                $return .= $node->encodeJSON();
                common_template('clean', '', $return);
            }
        }
    }

    function generate_rdf($node) {
        #TODO: Put header in RDF template file!
        $rdf_out = '<?xml version="1.0"?>'."\n\n";
        $rdf_out.= '<!DOCTYPE rdf:RDF ['."\n";
        $rdf_out.= '    <!ENTITY http "http://" >'."\n";
        $rdf_out.= '    <!ENTITY www "http://www.ArgOWL.org#" >'."\n";
        $rdf_out.= '    <!ENTITY owl "http://www.w3.org/2002/07/owl#" >'."\n";
        $rdf_out.= '    <!ENTITY aif "http://www.arg.tech/aif#" >'."\n";
        $rdf_out.= '    <!ENTITY xsd "http://www.w3.org/2001/XMLSchema#" >'."\n";
        $rdf_out.= '    <!ENTITY rdfs "http://www.w3.org/2000/01/rdf-schema#" >'."\n";
        $rdf_out.= '    <!ENTITY rdf "http://www.w3.org/1999/02/22-rdf-syntax-ns#" >'."\n";
        $rdf_out.= ']>'."\n\n";
        $rdf_out.= '<rdf:RDF xmlns="&http;www.w3.org/2002/07/owl#"'."\n";
        $rdf_out.= '     xml:base="&http;www.w3.org/2002/07/owl"'."\n";
        $rdf_out.= '     xmlns:rdfs="&http;www.w3.org/2000/01/rdf-schema#"'."\n";
        $rdf_out.= '     xmlns:http="http://"'."\n";
        $rdf_out.= '     xmlns:www="&http;www.ArgOWL.org#"'."\n";
        $rdf_out.= '     xmlns:owl="&http;www.w3.org/2002/07/owl#"'."\n";
        $rdf_out.= '     xmlns:xsd="&http;www.w3.org/2001/XMLSchema#"'."\n";
        $rdf_out.= '     xmlns:rdf="&http;www.w3.org/1999/02/22-rdf-syntax-ns#"'."\n";
        $rdf_out.= '     xmlns:aif="&http;www.arg.tech/aif#">'."\n";
        $rdf_out.= '    <Ontology rdf:about="&http;www.arg.tech/aif">'."\n";
        $rdf_out.= '        <rdfs:comment rdf:datatype="&http;www.w3.org/2001/XMLSchema#string">A number of argumentation schemes are taken from Bita Banihashemi and Iyad Rahwan&apos;s previous version of the AIF ontology.</rdfs:comment>'."\n";
        $rdf_out.= '        <www:createdBy rdf:datatype="&http;www.w3.org/2001/XMLSchema#string">Floris Bex</www:createdBy>'."\n";
        $rdf_out.= '        <rdfs:comment rdf:datatype="&http;www.w3.org/2001/XMLSchema#string">The AIF Ontology. </rdfs:comment>'."\n";
        $rdf_out.= '        <versionInfo>version 1.0</versionInfo>'."\n";
        $rdf_out.= '    </Ontology>'."\n";

        $rdf_out .= $this->show_node($node);
        
        $rdf_out.='</rdf:RDF>';
        return $rdf_out;
    }

    function show_node($node) {
        $node_out = "\n";
        $aif_uri_root = 'www.arg.tech/aif#';
        $node_uri = 'www.aifdb.org/nodes/' . $node->nodeID;
        $node_out .= '    <!-- http://' . $node_uri . ' -->'."\n";
        $node_out .= '    <NamedIndividual rdf:about="&http;' . $node_uri . '">'."\n";
        $node_out .= '        <rdf:type rdf:resource="&http;'.$aif_uri_root.$node->type.'-node"/>'."\n";
        if($node->type == 'I' || $node->type =='L'){
            $node_out .= '        <aif:claimText>'.$node->text.'</aif:claimText>'."\n";
        }elseif($node->type == 'RA' || $node->type == 'CA'){
            $premise = $node->getNodesIn();
            while($premise->fetch()){
                if($premise->type == "I"){
                    $node_out .= '        <aif:Premise rdf:resource="&http;www.aifdb.org/nodes/'.$premise->nodeID.'"/>'."\n";
                }
            }
            $conclusion = $node->getNodesOut();
            while($conclusion->fetch()){
                if($conclusion->type == "I"){
                    $node_out .= '        <aif:Conclusion rdf:resource="&http;www.aifdb.org/nodes/'.$conclusion->nodeID.'"/>'."\n";
                }
            }
        }elseif($node->type == 'TA'){
            $start= $node->getNodesIn();
            while($start->fetch()){
                if($start->type == "L"){
                    $node_out .= '        <aif:StartLocution rdf:resource="&http;www.aifdb.org/nodes/'.$start->nodeID.'"/>'."\n";
                }
            }
            $end = $node->getNodesOut();
            while($end->fetch()){
                if($end->type == "L"){
                    $node_out .= '        <aif:EndLocution rdf:resource="&http;www.aifdb.org/nodes/'.$end->nodeID.'"/>'."\n";
                }
            }
        }elseif($node->type == 'YA'){
            $loc = $node->getNodesIn();
            while($loc->fetch()){
                if($loc->type == "L"){
                    $node_out .= '        <aif:Locution rdf:resource="&http;www.aifdb.org/nodes/'.$loc->nodeID.'"/>'."\n";
                }elseif($loc->type == "TA"){
                    $node_out .= '        <aif:Anchor rdf:resource="&http;www.aifdb.org/nodes/'.$loc->nodeID.'"/>'."\n";
                }
            }
            $ic = $node->getNodesOut();
            while($ic->fetch()){
                $node_out .= '        <aif:IllocutionaryContent rdf:resource="&http;www.aifdb.org/nodes/'.$ic->nodeID.'"/>'."\n";
            }
        }

        $node_out .= '    </NamedIndividual>'."\n";

        return $node_out;
    }

    function no_such_node() {
        common_user_error('No such node.');
        exit;
    }
}
