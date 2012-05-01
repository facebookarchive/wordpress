<?php
function m_find_in_dir( $root, $pattern, $recursive = true, $case_sensitive = false ) {
    $result = array();
    if( $case_sensitive ) {
        if( false === m_find_in_dir__( $root, $pattern, $recursive, $result )) {
            return false;
        }
    } else {
        if( false === m_find_in_dir_i__( $root, $pattern, $recursive, $result )) {
            return false;
        }
    }
   
    return $result;
}

/**
 * @access private
 */
function m_find_in_dir__( $root, $pattern, $recursive, &$result ) {
    $dh = @opendir( $root );
    if( false === $dh ) {
        return false;
    }
    while( $file = readdir( $dh )) {
        if( "." == $file || ".." == $file ){
            continue;
        }
//		Note: ereg() is depreciated in php 5.3
//      if( false !== @ereg( $pattern, "{$root}/{$file}" )) { 
        if( false !== @preg_match( '/'.$pattern.'/', "{$root}/{$file}" )) {
            $result[] = "{$root}/{$file}";
        }
        if( false !== $recursive && is_dir( "{$root}/{$file}" )) {
            m_find_in_dir__( "{$root}/{$file}", $pattern, $recursive, $result );
        }
    }
    closedir( $dh );
    return true;
}

/**
 * @access private
 */
function m_find_in_dir_i__( $root, $pattern, $recursive, &$result ) {
    $dh = @opendir( $root );
    if( false === $dh ) {
        return false;
    }
    while( $file = readdir( $dh )) {
        if( "." == $file || ".." == $file ){
            continue;
        }
//		Note: ergi() is depreciated in php 5.3
//      if( false !== @eregi( $pattern, "{$root}/{$file}" )) { 
        if( false !== @preg_match( '/'.$pattern.'/i', "{$root}/{$file}" )) {
            $result[] = "{$root}/{$file}";
        }
        if( false !== $recursive && is_dir( "{$root}/{$file}" )) {
            m_find_in_dir__( "{$root}/{$file}", $pattern, $recursive, $result );
        }
    }
    closedir( $dh );
    return true;
}
?>