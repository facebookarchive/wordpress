<?php
	// First div - for drag & drop
	// Second div is root div for preview
?>
<div style="width: 400px; height: 216px; border: 3px solid #DFDFDF; -moz-border-radius-bottomleft:4px;
-moz-border-radius-bottomright:4px;
-moz-border-radius-topleft:4px;
-moz-border-radius-topright:4px;  background: #f3f3f3;">
	<div style="width: 400px; height:216px; line-height: normal; padding: 0px;" id="background-image-div">
    
    	<div style=" width:400px; height:216px; background-image:url('<?php echo get_template_directory_uri(); ?>/include/images/preview.png'); background-repeat:no-repeat; position:absolute;"></div>
    <div id="test-text" style="display:none;"><a href="#">link</a> text</div>
	</div>
</div>