sfHover = function() {
	if (!document.getElementsByTagName) return false;
	var sfEls = document.getElementById("nav-ie").getElementsByTagName("li");
	// if you only have one main menu - delete the line below //
	//var sfEls1 = document.getElementById("secnav").getElementsByTagName("li");
	//
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfhover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
		}
	}
	// if you only have one main menu - delete the "for" loop below //
//	for (var i=0; i<sfEls1.length; i++) {
//		sfEls1[i].onmouseover=function() {
//			this.className+=" sfhover1";
//		}
//		sfEls1[i].onmouseout=function() {
//			this.className=this.className.replace(new RegExp(" sfhover1\\b"), "");
//		}
//	}
	//
}
if (window.attachEvent) window.attachEvent("onload", sfHover);