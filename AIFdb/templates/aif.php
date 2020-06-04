<?php
    header("content-type:text/xml");
    echo "<?xml-stylesheet href=\"" . common_local_path('ovaview/aif.xsl') . "\" type=\"text/xsl\"?>\n";
?>
<rdf:RDF xmlns:j.0="http://www.owl-ontologies.com/araucaria4#" xmlns:j.1="http://protege.stanford.edu/aif#" xmlns:owl="http://www.w3.org/2002/07/owl#" xmlns:daml="http://www.daml.org/2001/03/daml+oil#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">

    <?php echo $template_body; ?>

</rdf:RDF>
