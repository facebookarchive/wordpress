<?php 
function ih_input( $var, $type, $description = "", $value = "", $selected="", $onchange="" ) {
	echo "\n"; 	
	switch( $type ){	
	    case "text":
			if (eregi ("color", $var)) { 
			echo "<input class=\"picker-input\" id=\"$var\" name=\"$var\" type=\"text\" size=\"7\" maxlength=\"7\" value=\"$value\" onchange=\"get_option('$var')\" />";
			echo "<div class='picker' id='myRainbow_".$var."_input'><div class='overlay'></div></div>\n";
			} else {
	 		echo "<input name=\"$var\" id=\"$var\" type=\"$type\" style=\"width: 80%\" class=\"code\" value=\"".stripslashes($value)."\" onchange=\"$onchange\"/>";					
			}
	 		echo "<p style=\"font-size:0.9em; color:#999; margin:0;\">$description</p>";			
			break;

		case "smalltext":
	 		echo "<input name=\"$var\" id=\"$var\" type=\"$type\" style=\"width: 43%\" class=\"code\" value=\"$value\" onchange=\"$onchange\"/>";
	 		echo "<br /><span style=\"font-size:0.9em; color:#999; margin:0; padding-left:5px; \">$description</span><br />";			
			break;
			
		case "submit":		
	 		echo "<p class=\"submit\" align=\"right\"><input name=\"$var\" type=\"$type\" value=\"$value\" /></p>";
			break;

		case "option":		
			if( $selected == $value ) { $extra = "selected=\"true\""; }
			echo "<option value=\"$value\" $extra >$description</option>";		
		    break;
		    
  		case "radio":  		
			if( $selected == $value ) { $extra = "checked=\"true\""; }  			
  			echo "<label><input name=\"$var\" id=\"$var\" type=\"$type\" value=\"$value\" $extra /> $description</label><br/>";  			
  			break;
  			
		case "checkbox":		
			if( $selected == $value ) { $extra = "checked=\"true\""; }
  			echo "<label><input name=\"$var\" id=\"$var\" type=\"$type\" value=\"$value\" $extra /> $description</label><br/>";
  			break;

		case "textarea":		
		    echo "<textarea name=\"$var\" id=\"$var\" style=\"width: 80%; height: 10em;\" class=\"code\">".stripslashes($value)."</textarea>";		
		    echo "<p style=\"font-size:0.9em; color:#999; margin:0;\">$description</p>";			
		    break;
	}
}

function nat_upload($id, $val, $name) {	
	$uploader = '';
  $uploader .= '<div class="form-uploader"><input class="upl-inp" name="'. $id .'" id="'. $id .'_upload" type="text" value="'. $val .'" />';	
	$uploader .= '<div class="upload_button_div"><span class="button image_upload_button" id="'.$id.'">'.$name.'</span>';
	
	if(!empty($val)) {$hide = '';} else { $hide = 'hide';}
	
	$uploader .= '<span class="button image_reset_button '. $hide.'" id="reset_'. $id .'" title="' . $id . '">Remove</span>';
	$uploader .='</div>' . "\n";
  $uploader .= '<div class="clear"></div>' . "\n";
	if(!empty($val)){
    	$uploader .= '<img class="custom-logo-image" id="image_'.$id.'" src="'.$val.'" alt="" />';
		}
	$uploader .= '<div class="clear"></div> </div>' . "\n"; 

echo $uploader;    
}

function nat_sort ($var, $arrValues, $arrSelected, $label, $description) {
 $sorting = '';
 $sorting .= '<select id="'.$var.'" class="multiselect" multiple="multiple" name="'.$var.'">';
  if (is_array($arrSelected)) {
    foreach( $arrSelected as $arr ) {		
      $sorting .= "<option value=\"" . $arr . "\" selected=\"true\">" . get_the_title($arr) . "</option>\n";
    }
  }  	
  foreach( $arrValues as $arr ) {		      	
      if( is_array($arrSelected) && in_array( $arr[ 0 ], $arrSelected ) ) { continue; }
      $sorting .= "<option value=\"" . $arr[ 0 ] . "\">" . $arr[ 1 ] . "</option>\n";
    }
    	        
  $sorting .= '</select><div id="switcher"></div>';
  $sorting .= '<div class="description inline">'.$description.'</div>';
  echo $sorting;
}


function ih_select( $var, $arrValues, $selected, $label, $change = "" ) {
	if( $label != "" ) {
		echo "<label for=\"$var\">$label</label>";
	}	
	if ($change != "") { 
	echo "<select name=\"$var\" onchange=\"$change\" id=\"$var\">\n";
	} else {
	echo "<select name=\"$var\" id=\"$var\">\n";	}
	foreach( $arrValues as $arr ) {		
		$extra = "";	
		if( $selected == $arr[ 0 ] ) { $extra = " selected=\"true\""; }
		echo "<option value=\"" . $arr[ 0 ] . "\"$extra>" . $arr[ 1 ] . "</option>\n";
	}	
	echo "</select>";	
}

function ih_mselect( $var, $arrValues, $arrSelected, $label, $description ) {
	if( $label != "" ) {
		echo "<label for=\"$var\">$label</label>";
	}	
	echo "<select multiple=\"true\" size=\"7\" name=\"$var\" id=\"$var\" style=\"height:150px;\">\n";	
	foreach( $arrValues as $arr ) {		
		$extra = "";	
		if (is_array($arrSelected)) {
      if( in_array( $arr[ 0 ], $arrSelected ) ) { $extra = " selected=\"true\""; }
    } else {
      if( $arr[ 0 ] == 'no' )  { $extra = " selected=\"true\""; }
    }
		echo "<option value=\"" . $arr[ 0 ] . "\"$extra>" . $arr[ 1 ] . "</option>\n";
	}	
	echo "</select>";	
	echo "<p style=\"font-size:0.9em; color:#999; margin:0;\">$description</p>";	
}

function nat_th( $title ) {
  echo '<div class="form-item">';
	echo "<h4>$title</h4>";
}

function nat_th_array( $title ) {
  echo '<div class="form-item array">';
	echo "<h4>$title</h4>";
}

function nat_endth() {
  echo '<div class="clear"></div>';
	echo "</div>";
}

function nat_hit( $hit ) {
	echo "<div class=\"description\">$hit</div>";
}

function nat_labl( $hit, $var ) {
	echo "<p><label for=\"$var\">$hit</label></p>";
}
?>