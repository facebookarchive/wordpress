/*!
 * Nokia Mobile Web Templates v0.5
 * http://forumnokia.com
 *
 * Copyright (c) 2009 Forum Nokia
 *
 */



/*
 * Slideshow(string:id, int:index, boolean:wrap)
 * usage: mySlideshow = new Slideshow("coming-soon", 0, true);
 *
 */

function Slideshow(_id, _index, _wrap) {
	var slideshow = document.getElementById(_id);
	var index, wrap, preview, caption, link;
	var images = new Array();
	var context = this;
	
	// if no index was set assume we're starting at the beginning
	(_index)? index = _index : index = 0;
	
	// does the slideshow wrap around at the beginning and end?
	(_wrap)? wrap = _wrap : wrap = false;
	
	// if the slideshow id isn't found do nothing (false)...
	(slideshow)?init():false;
	
	function init() {
		// find all <img>'s along with the <a class="preview>
		// and <img /> elements within the slideshow
		var _images = slideshow.getElementsByTagName("img");
		for (var i=0; i < _images.length; i++) {
			var isPreview = _images[i].parentNode.className.search(/preview/)+1;			
			if (isPreview) {
				// set the preview image reference
				preview = _images[i];
				link = _images[i].parentNode;
			} else {
				// push all other images into the images array
				images.push(_images[i]);
			}
		}
		// find the preview <p> element and reference it for caption
		var _span = slideshow.getElementsByTagName("span");
		for (var i=0; i < _span.length; i++) {
			var isPreview = _images[i].parentNode.className.search(/preview/)+1;			
			if (isPreview) {
				caption = _span[i];
			}
		}
		// find and enable the previous and next controls
		var _ul = slideshow.getElementsByTagName("ul");
		for (var i=0; i < _ul.length; i++) {
			var isControls = _ul[i].className.search(/controls/)+1;	
			if (isControls) {
				var _a = _ul[i].getElementsByTagName("a");
				for (var j=0; j < _a.length; j++) {
					var isPrevious = _a[j].className.search(/previous/)+1;
					var isNext = _a[j].className.search(/next/)+1;
					// wire up previous button
					if (isPrevious) {
						_a[j].onclick = function() {
							context.previous();
						}
					}
					// wire up next button
					if (isNext) {
						_a[j].onclick = function() {
							context.next();
						}
					}
				}
			}
		}
		// kick things off with an initial update
		update();
	}

	function update() {
		// tweak index if at start or end based on wrap property
		(index<0)?((wrap)?index=images.length-1:index=0):false;
		(index>images.length-1)?((wrap)?index=0:index=images.length-1):false;
		// update the view
		preview.setAttribute("src", images[index].getAttribute("src"));
		caption.innerHTML = images[index].getAttribute("alt");
		link.setAttribute("href", images[index].parentNode.getAttribute("href"));
	}
	
	this.previous = function () {
		// select the previous image by index and update the view
	    index--;update();
	}
	
	this.next = function () {
		// select the next image by index and update the view
		index++;update();
	}
}

/*
 * AccordionList(string:id, callback:function)
 * usage: myAccordianList = new AccordianList(id, callback);
 * - id 'foo' can also be an array such as ids['foo','bar']
 * - callback (optional) function is triggered when a <dt> within the list is clicked
 *   and passes a reference to itself to the defined callback function.
 */
 
function AccordionList(_id, _callback) {
	var id = new Array();
	var callback;
	(!_isArray(_id))?id.push(_id):id=_id;
	(typeof _callback=="function")?callback=_callback:callback=function(){};
	
	for (var x=0;x<id.length;x++) {
		var dl = document.getElementById(id[x]);
		var dt = dl.getElementsByTagName("dt");
		for (var j=0; j < dt.length; j++) {
			var state = dt[j].getAttribute("class");
			// no classes defined, add class attribute with value 'collapsed'
			if (state == null) {
				dt[j].setAttribute("class", "collapsed");
				state = dt[j].getAttribute("class");
			}
			var expanded = state.search(/expanded/)+1;
			
			// find corresponding dd element
			var dd = dt[j];
			do dd = dd.nextSibling;
				while (dd && dd.nodeType != 1);
			(expanded)? dd.style['display'] = "block" : dd.style['display'] = "none" ;
			
			dt[j].onclick = function() {
				var dd = this;
				var state = this.getAttribute("class");
				var expanded = state.search(/expanded/)+1;
				var toggle;
				(expanded) ? toggle = state.replace(/expanded/, "collapsed") : toggle = state.replace(/collapsed/, "expanded") ;
				this.setAttribute("class", toggle);
				
				do dd = dd.nextSibling;
					while (dd && dd.nodeType != 1);
				(dd.style['display'] == "none")? dd.style['display'] = "block" : dd.style['display'] = "none" ;
				callback(this);
			}
		}	
	}
}

/*
 * toggleSwitch()
 * usage: mySwitch = new toggleSwitch(id, function);
 * id can also be an array such as ids['foo','bar'…]
 * 
 */
 
function ToggleSwitch(_id, _callback) {
	var id = new Array();
	var callback;
	(!_isArray(_id))?id.push(_id):id=_id;
	(typeof _callback=="function")?callback=_callback:callback=function(){};
	
	for (var x=0;x<id.length;x++) {
		var toggle = document.getElementById(id[x]);
		toggle.style['display'] = "none";	
		// now let's build the toggle switch dynamically...	
		var ol = document.createElement("ol");
		// set the class based on the state of the toggle (checkbox)
		var toggleClass = "toggle-switch ";
		(toggle.checked)?toggleClass += "on":toggleClass += "off";
		ol.setAttribute("class", toggleClass);
		// create the <li class="label-on"> element
		var lion = document.createElement("li");
		lion.setAttribute("class", "label-on");
		// create the <li class="label-off"> element
		var lioff = document.createElement("li");
		lioff.setAttribute("class", "label-off");
		// create the 'on' <a> element
		var aon = document.createElement("a");
		aon.setAttribute("href", "#on");
		aon.appendChild(document.createTextNode("on"));
		// create the 'off' <a> element
		var aoff = document.createElement("a");
		aoff.setAttribute("href", "#off");
		aoff.appendChild(document.createTextNode("off"));
		// assemble all of the various elements
		lioff.appendChild(aoff);
		lion.appendChild(aon);
		ol.appendChild(lion);
		ol.appendChild(lioff);
		// clone and add the original (and hidden) checkbox to the toggle swithc
		ol.appendChild(toggle.cloneNode(true));
		// add the click event
		ol.onclick = function() {
			var state = this.getAttribute("class");
			var on = state.search(/on/)+1;
			var toggle;
			var checkbox = this.getElementsByTagName("input");
			if (on) {
				toggle = state.replace(/on/, "off");
				checkbox[0].removeAttribute("checked");
			} else {
				toggle = state.replace(/off/, "on");
				checkbox[0].setAttribute("checked", "true");
			}
			this.setAttribute("class", toggle);
			callback(this);
		}
		// replace the original 'toggle' element with the new one
		toggle.parentNode.replaceChild(ol, toggle);		
	}
}

/*
 * styleTweaker()
 * usage: myStyleTweaker = new styleTweaker();
 * id can also be an array such as ids['foo','bar'…]
 * 
 */
 
function StyleTweaker() {
	this.ua = navigator.userAgent;
	this.tweaks = new Object();
}

StyleTweaker.prototype.add = function(_string, _stylesheet) {
	this.tweaks[_string] = _stylesheet;
}

StyleTweaker.prototype.remove = function(_term) {
	for (var _string in this.tweaks) {
		var exists = false;
		(_string == _term)?exists=true:false;
		(this.tweaks[_string])?exists=true:false;
		(exists)?delete this.tweaks[_string]:false;
	}
}

StyleTweaker.prototype.tweak = function() {
	for (var _string in this.tweaks) {
		if (this.ua.match(_string)) {
			loadStylesheet(this.tweaks[_string]);
		}
	}
}

StyleTweaker.prototype.untweak = function() {
	for (var _string in this.tweaks) {
		if (this.ua.match(_string)) {
			removeStylesheet(this.tweaks[_string]);
		}
	}
}

/*
 * _isArray()
 * usage: _isArray(object);
 * 
 */
function _isArray(x){
	return ((typeof x == "object") && (x.constructor == Array));
}

/*
 * addEvent()
 * usage: addEvent(event, function);
 * note: only targets window events!
 * 
 */

function addEvent(_event, _function) {
	var _current_event = window[_event];
	if (typeof window[_event] != 'function') {
		window[_event] = _function;
	} else {
		window[_event] = function() {
			_current_event();
			_function();
		}
	}
}

/*
 * include(file)
 * usage: include(filename.js);
 * 
 */

function include(filename) {
	var head = document.getElementsByTagName("head")[0];
	var script = document.createElement("script");
	script.setAttribute("type", "text/javascript");
	script.setAttribute("src", filename);
	head.appendChild(script);
}

/*
 * loadStylesheet(file)
 * usage: loadStylesheet(filename.css);
 * 
 */
 
function loadStylesheet(filename) {
	var head = document.getElementsByTagName('head')[0];
	var link = document.createElement("link");
	link.setAttribute("rel", "stylesheet");
	link.setAttribute("type", "text/css");
	link.setAttribute("href", filename);
	head.appendChild(link);
}

/*
 * removeStylesheet(file)
 * usage: removeStylesheet(filename.css);
 * 
 */
 
function removeStylesheet(filename) {
	var stylesheets=document.getElementsByTagName("link");
	for (var i=stylesheets.length; i>=0; i--) { 
		if (stylesheets[i] && stylesheets[i].getAttribute("href")!=null && stylesheets[i].getAttribute("href").indexOf(filename)!=-1) {
			stylesheets[i].parentNode.removeChild(stylesheets[i]); 
		}
	}
}