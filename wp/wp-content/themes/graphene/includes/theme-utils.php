<?php
/**
 * Convert a hex decimal color code to its RGB equivalent and vice versa
 */                                                                                                
function graphene_rgb2hex( $c ){
   if ( ! $c ) return false;
   $c = trim( $c );
   $out = false;
  if(preg_match("/^[0-9ABCDEFabcdef\#]+$/i", $c) ){
      $c = str_replace( '#','', $c);
      $l = strlen( $c) == 3 ? 1 : (strlen( $c) == 6 ? 2 : false);

      if( $l){
         unset( $out);
         $out['red'] = hexdec(substr( $c, 0,1*$l) );
         $out['green'] = hexdec(substr( $c, 1*$l,1*$l) );
         $out['blue'] = hexdec(substr( $c, 2*$l,1*$l) );
      }else $out = false;
             
   }elseif (preg_match("/^[0-9]+(,| |.)+[0-9]+(,| |.)+[0-9]+$/i", $c) ){
      $spr = str_replace(array( ',',' ','.' ), ':', $c);
      $e = explode(":", $spr);
      if(count( $e) != 3) return false;
         $out = '#';
         for( $i = 0; $i<3; $i++)
            $e[$i] = dechex( ( $e[$i] <= 0)?0:( ( $e[$i] >= 255)?255:$e[$i]) );
             
         for( $i = 0; $i<3; $i++)
            $out .= ( (strlen( $e[$i]) < 2)?'0':'' ).$e[$i];
                 
         $out = strtoupper( $out);
   }else $out = false;
         
   return $out;
}


/**
 * Perform adding (or subtracting) operation on a hexadecimal colour code
*/
function graphene_hex_addition( $hex, $num ){
	$rgb = graphene_rgb2hex( $hex);
	foreach ( $rgb as $key => $val) {
		$rgb[$key] += $num;
		$rgb[$key] = ( $rgb[$key] < 0) ? 0 : $rgb[$key];
	}
	$hex = graphene_rgb2hex(implode( ',', $rgb) );
	
	return $hex;
}


/**
 * Gets all action hooks available in the Graphene theme.
 * @param boolean $hooksonly
 * @return array 
 */
function graphene_get_action_hooks( $hooksonly = false ) {    

	if ( isset( $_GET['rescan_hooks'] ) && $_GET['rescan_hooks'] == 'true' ){
		delete_transient( 'graphene-action-hooks-list' );
		delete_transient( 'graphene-action-hooks' );
	}
	
	// Get the cached action hooks list, if available
	if ( $hooksonly )
		$hooks = get_transient( 'graphene-action-hooks-list' );
	else
		$hooks = get_transient( 'graphene-action-hooks' );
		
	if ( $hooks ) 
		return $hooks;
	else
		$hooks = array();
	
    // as all the hooks are defined in php files get a list of the themes php files
    $files = @glob( get_template_directory() . "/*.php" );
	$files = array_merge( $files, @glob( get_template_directory() . "/includes/*.php" ) );

    if ( $files !== false ) {
        foreach ( $files as $file ) {

            // read the file and scan it's contents for do_action();
            $content = file( $file );
			$content = implode( '', $content );
			
            if ($content !== false) {
                if (preg_match_all("/do_action\([ ]*'(graphene_[^']*)'[ ]*\)/", $content, $matches) > 0) {
					$matches = array_unique( $matches[1] );
                    if ( $hooksonly ){ $hooks = array_merge( $hooks, $matches ); }
                    else {
						$filename = basename( $file );
						if ( stripos( $filename, 'theme-' ) === 0 ) { $filename = 'includes/' . $filename; }
						$hooks[] = array( 'file' => $filename, 'hooks' => $matches );
					}
                }                                
            }
        }
    }
	
	// Cache the found action hooks as WordPress transients
	if ( $hooksonly )
		set_transient( 'graphene-action-hooks-list', $hooks, 60*60*24 );
	else
		set_transient( 'graphene-action-hooks', $hooks, 60*60*24 );
		
    return $hooks;
}


function graphene_column_mode( $post_id = NULL ){
    global $graphene_settings;
    
    // Check the front-end template
	if ( ! is_admin() && ! $post_id){
		if ( is_page_template( 'template-onecolumn.php' ) )
			return 'one_column';
		elseif ( is_page_template( 'template-twocolumnsleft.php' ) )
			return 'two_col_left';
		elseif ( is_page_template( 'template-twocolumnsright.php' ) )
			return 'two_col_right';
		elseif ( is_page_template( 'template-threecolumnsleft.php' ) )
			return 'three_col_left';
		elseif ( is_page_template( 'template-threecolumnsright.php' ) )
			return 'three_col_right';
		elseif ( is_page_template( 'template-threecolumnscenter.php' ) )
			return 'three_col_center';
	}
		
	/* Check the template in Edit Page screen in admin */
	if ( is_admin() || $post_id ){
		
		if ( ! $post_id ){
			$post_id = ( isset( $_GET['post'] ) ) ? $_GET['post'] : NULL;
		}
		
		$page_template = get_post_meta( $post_id, '_wp_page_template', true );
		
		if ( $page_template != 'default' ){
			if ( strpos( $page_template, 'template-onecolumn' ) === 0 )
				return 'one_column';
			elseif ( strpos( $page_template, 'template-twocolumns' ) === 0 )
				return 'two_col';
			elseif ( strpos( $page_template, 'template-threecolumns' ) === 0 )
				return 'three_col';
		}
	}
    
	// Return the settings for BBPress column mode if viewing a BBPress page
	if ( class_exists( 'bbPress' ) && is_bbpress() )
		return $graphene_settings['bbp_column_mode'];
	
	// Return the settings as defined in the theme options 
    return $graphene_settings['column_mode']; 
}


/**
 * Prints out the content of a variable wrapped in <pre> elements.
 * For development and debugging use
*/
if ( ! function_exists( 'disect_it' ) ) :
function disect_it( $var = NULL, $exit = true, $comment = false){
	if ( $var !== NULL){
		if ( $comment) {echo '<!--';}
		echo '<pre>';
		print_r( $var);
		echo '</pre>';
		if ( $comment) {echo '-->';}
		if ( $exit) {exit();}
	} else {
		echo '<strong>ERROR:</strong> You must pass a variable as argument to the <code>disect_it()</code> function.';	
	}
}
endif;


function graphene_print_only_text( $text ){
    return sprintf( '<p class="printonly">%s</p>', $text );
}


/**
 * Truncate a string by specified length
*/
if ( ! function_exists( 'graphene_substr' ) ) :

function graphene_substr( $string, $start = 0, $length = '', $suffix = '' ){
	
	if ( $length == '' ) return $string;
	
	if ( strlen( $string ) > $length ) {
		$trunc_string = substr( $string, $start, $length ) . $suffix;
	} else {
		$trunc_string = $string;	
	}
	return apply_filters( 'graphene_substr', $trunc_string, $string, $start, $length, $suffix );
}

endif;

/**
 * Truncate a string by specified word count
 *
 * @param string $string The string to be truncated
 * @param int $word_count The number of words to keep
 * @param string $suffix Optional, string to be appended to truncated string
 * @return string $trunc_string The truncated string
 *
 * @package Graphene
 * @since 1.6
*/
if ( ! function_exists( 'graphene_truncate_word' ) ) :

function graphene_truncate_words( $string, $word_count, $suffix = '...' ){
   $string_array = explode( ' ', $string );
   if( count ( $string_array ) > $word_count && $word_count > 0 )
      $trunc_string = implode( ' ', array_slice( $string_array, 0, $word_count ) ) . $suffix;
	  
   return apply_filters( 'graphene_truncate_words', $trunc_string, $string, $word_count, $suffix );
}

endif;


/**
 * Check the currently installed version of WordPress
 *
 * @param string $version The version to check
 * @return bool True is WordPress version is equal to or greater than the passed version, false otherwise
 *
 * @package Graphene
 * @since 1.6
*/
if ( ! function_exists( 'graphene_is_wp_version' ) ) :

function graphene_is_wp_version( $version = '' ) {
	if ( ! $version ) return false;

	global $wp_version;

	if ( version_compare( $wp_version, $version, '<' ) ) {
		return false;
	}
	
	return true;
}

endif;
?>