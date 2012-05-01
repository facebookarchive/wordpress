/* <![CDATA[ */

var jqu = jQuery.noConflict();

jqu( function () {

	/* Fancybox */
	jqu( "a[href$='.jpg'], a[href$='.jpeg'], a[href$='.png'], a[href$='.gif']" ).attr( 'rel', 'lightbox' );
	jqu( "a[rel^='lightbox']" ).fancybox( { titleShow: false, overlayOpacity: .8, overlayColor: '#000' } );	
	
	/* FitVids */
	jqu( ".entry-content" ).fitVids();
	
} );

/* ]]> */



