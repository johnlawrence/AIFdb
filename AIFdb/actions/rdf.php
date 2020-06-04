<?php

class RdfAction extends Action {

    function handle($args) {
        parent::handle($args);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            $uploaddir = INSTALLDIR.'/tmp/';
            $rdf_file = $uploaddir . basename($_FILES['file']['name']);

            if(move_uploaded_file($_FILES['file']['tmp_name'], $rdf_file)){
                require_once(INSTALLDIR.'/lib/rdfparse.php');
                $data = file_get_contents($rdf_file);

                $rdf = new rdfParser( $data );

                $nodeset = DB_DataObject::factory('nodesets');
                assert($nodeset);
                $nodesetId = $nodeset->insert();

                foreach ($rdf->nodes as $node) {
                    $nodeID = $this->add_node($node);
                    $nodes_added['NA'.$node[id]] = $nodeID;
                    $nodesetmapping = DB_DataObject::factory('nodesetmappings');
                    assert($nodesetmapping);
                    $nodesetmapping->nodeID = $nodeID;
                    $nodesetmapping->nodeSetID = $nodesetId;
                    $nodesetmappingId = $nodesetmapping->insert();
                }

                foreach ($rdf->edges as $edge) {
                    $edgeID = $this->add_edge($nodes_added['NA'.$edge['from']], $nodes_added['NA'.$edge['to']]);
                }

                $return = "Imported as nodeSet " . $nodesetId;
                common_template('clean', '', $return);
                
            }else{
	            common_user_error('Error uploading file');
            }

        }else{
	        $nodeset_id = $this->arg('nodesetid');
            $nodeset = NodeSets::staticGet('nodeSetID', $nodeset_id);

            if (!$nodeset) {
                $this->no_such_nodeset();
                return;
            }

            $output = $this->generate_rdf($nodeset);

	        common_template('rdf', $title, $output);
	    }
    }

    function no_such_nodeset() {
        common_user_error('No such nodeset');
    }

    function generate_rdf($nodeset) {
        $node = $nodeset->getNodes();
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

        while($node->fetch()){
            $rdf_out .= $this->show_node($node);
        }

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
            $node_out .= '        <aif:claimText>'.htmlspecialchars($node->text, ENT_QUOTES).'</aif:claimText>'."\n";
            if($node->type =='I'){
                $in = $node->getNodesIn();
                while($in->fetch()){
                    if($in->type == "YA"){ 
                        # floris symmetric relations
                        $node_out .= '        <aif:IllocutionaryContent rdf:resource="&http;www.aifdb.org/nodes/'.$in->nodeID.'"/>'."\n";
                    }elseif($in->type == "RA" || $in->type == "CA"){
                        # floris symmetric relations
                        $node_out .= '        <aif:Conclusion rdf:resource="&http;www.aifdb.org/nodes/'.$in->nodeID.'"/>'."\n";
                    }
                }
                $out = $node->getNodesOut();
                while($out->fetch()){
                    if($out->type == "RA" || $out->type=="CA"){
                        # floris symmetric relations
                        $node_out .= '        <aif:Premise rdf:resource="&http;www.aifdb.org/nodes/'.$out->nodeID.'"/>'."\n";
                    }
                }
            }elseif($node->type =='L'){
                $a = explode(" says ", htmlspecialchars($node->text, ENT_QUOTES));
                $authorName = $a[0];
                $node_out .= '        <aif:source>'.$authorName.'</aif:source>'."\n";
                $in = $node->getNodesIn();
                while($in->fetch()){
                    if($in->type == "TA"){
                        # floris symmetric relations
                        $node_out .= '        <aif:EndLocution rdf:resource="&http;www.aifdb.org/nodes/'.$in->nodeID.'"/>'."\n";
                    }
                }
                $out = $node->getNodesOut();
                while($out->fetch()){
                    if($out->type == "YA"){
                        # floris symmetric relations
                        $node_out .= '        <aif:Locution rdf:resource="&http;www.aifdb.org/nodes/'.$out->nodeID.'"/>'."\n";
                    }elseif($out->type == "TA"){
                        # floris symmetric relations
                        $node_out .= '        <aif:StartLocution rdf:resource="&http;www.aifdb.org/nodes/'.$out->nodeID.'"/>'."\n";
                    }
                }
            }

        }elseif($node->type == 'RA' || $node->type == 'CA'){
            $premise = $node->getNodesIn();
            while($premise->fetch()){
                if($premise->type == "I"){
                    $node_out .= '        <aif:Premise rdf:resource="&http;www.aifdb.org/nodes/'.$premise->nodeID.'"/>'."\n";
                }elseif($premise->type == "YA"){
                    # floris symmetric relations
                    $node_out .= '        <aif:IllocutionaryContent rdf:resource="&http;www.aifdb.org/nodes/'.$premise->nodeID.'"/>'."\n";
                }
            }
            $conclusion = $node->getNodesOut();
            while($conclusion->fetch()){
                if($conclusion->type == "I"){
                    $node_out .= '        <aif:Conclusion rdf:resource="&http;www.aifdb.org/nodes/'.$conclusion->nodeID.'"/>'."\n";
                }
            }
        }elseif($node->type == 'TA'){
            $node_out .= '        <rdf:type rdf:resource="&http;'.$aif_uri_root.'Transition"/>'."\n";
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

        $node_out .= '        <aif:creationDate>'.$node->timestamp.'</aif:creationDate>'."\n";
        $node_out .= '    </NamedIndividual>'."\n";

        return $node_out;
    }

    function add_node($node_data) {
        $props = array("text", "type"); 
        $node = DB_DataObject::factory('nodes');
        assert($node);
        
        foreach ($node_data as $key => $value){
            if(in_array($key, $props)){
                $node->$key = $value;
            }
        }
        $nodeId = $node->insert(); 

        return $nodeId; 
    }

    function add_edge($from, $to) {
        $edge = DB_DataObject::factory('edges');
        assert($edge);
        $edge->fromID = $from;
        $edge->toID = $to;
        $edgeId = $edge->insert(); 

        return $edgeId;
    }
}
