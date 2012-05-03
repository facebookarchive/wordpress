var $jn = jQuery.noConflict();
$jn(document).ready(function(){ 
	$jn('div.toggle').click(function () {
	$jn(this).next('div.toggle_content').toggle(250);
	});
	
	$jn('div.toggle').toggle(
	function () { $jn(this).css('background-position', 'left -68px'); },
	function () { $jn(this).css('background-position', 'left 12px'); }
	);
});