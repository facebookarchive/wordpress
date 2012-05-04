// JavaScript Document
jQuery(document).ready(function($){
	
	$(".rwd-container").hide();

	$("h3.rwd-toggle").click(function(){
	$(this).toggleClass("active").next().slideToggle("fast");
		return false; //Prevent the browser jump to the link anchor
	});

});
jQuery(document).ready(function ($) {
    setTimeout(function () {
        $(".fade").fadeOut("slow", function () {
            $(".fade").remove();
        });

    }, 2000);
});;