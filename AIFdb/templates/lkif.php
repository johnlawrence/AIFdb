<?php
    header("content-type:text/xml");
    echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>' . "\n";
    echo '<?oxygen RNGSchema="../../schemas/LKIF2.rnc" type="compact"?>' . "\n";
?>
<lkif version="2.0.4">
    <argument-graphs>
        <argument-graph id="NewGraph" title="New Graph">
<?php echo $template_body; ?>
        </argument-graph>
    </argument-graphs>
</lkif>
