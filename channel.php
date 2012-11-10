<?php
/**
 * Improve the performance of the JavaScript SDK in some browsers with a samedomain proxy
 * Avoids issue with pages containing a social plugin as well as cross-frame communication displaying blank, auto playing audio or video playing twice, and more accurate pageview numbers in server logs.
 *
 * @link https://developers.facebook.com/docs/reference/javascript/#channel JavaScript SDK channel description
 */

if ( ! headers_sent() ) {
	header( 'Content-Type: text/html; charset=utf-8', true );
	// cache me	
	$cache_expire = 60 * 60 * 24 * 365;
	header( 'Pragma: public', true );
	header( 'Cache-Control: max-age=' . $cache_expire, true );
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $cache_expire ) . ' GMT', true );
}
?><!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><title>Facebook channel file</title><script type="text/javascript" src="//connect.facebook.net/en_US/all.js"></script></head><body></body></html>