<?php
    header("content-type:text/xml");
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<!DOCTYPE ARG SYSTEM "argument.dtd">
<ARG>
<?php echo $template_body; ?>
</ARG>
