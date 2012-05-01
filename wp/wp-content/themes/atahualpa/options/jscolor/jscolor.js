/**
 * JavaScript Color Picker
 *
 * @author    Honza Odvarko, http://odvarko.cz
 * @copyright Honza Odvarko
 * @license   http://www.gnu.org/copyleft/gpl.html  GNU General Public License
 * @version   1.0.8
 * @link      http://jscolor.com
 */


jscolor_register() // register jscolor_init() at page load


function jscolor_register() {
	if(typeof window.onload == 'function') {
		var f = window.onload
		window.onload = function() {
			if(f)/* IE7 */ f()
			jscolor_init()
		}
	} else {
		window.onload = jscolor_init
	}
}


function jscolor_init() {

	// bind <input class="..."> elements
	var bindClass = 'color'

	// set field's background according selected color?
	var reflectOnBackground = true

	// prepend field's color code with #
	var leadingHash = false

	// allow an empty value in the field instead of setting it to #000000
	var allowEmpty = false

	// spectrum's width and height
	// var HVSize = [ 720, 404 ] // huge
	var HVSize = [ 540, 303 ] // large
	//var HVSize = [ 360, 202 ] // normal
	//var HVSize = [ 180, 101 ] // small


	var padding = 10
	var borderWidth = 1
	var HVCrossSize = [ 15, 15 ]
	var SSize = 66
	var SArrowSize = [ 7, 11 ]
	var SSampleSize = 4
	var ClientSliderSize = 18

	var instanceId = 0
	var instance
	var elements = {}

	var dir = function() {
		var base = location.href

		var e = document.getElementsByTagName('base')
		for(var i=0; i<e.length; i++) {
			if(e[i].href) base = e[i].href
		}

		var e = document.getElementsByTagName('script')
		for(var i=0; i<e.length; i++) {
			if(e[i].src) {
				var src = new URI(e[i].src)
				if(/\/jscolor\.js$/.test(src.path)) {
					var srcAbs = src.toAbsolute(base).toString()
					delete srcAbs.query
					delete srcAbs.fragment
					return srcAbs.replace(/[^\/]+$/, '') // remove filename from path
				}
			}
		}
		return false
	}()


	function createDialog() {

		// dialog
		elements.dialog = document.createElement('div')
		setStyle(elements.dialog, {
			'zIndex' : '100',
			'clear' : 'both',
			'position' : 'absolute',
			'width' : HVSize[0]+SSize+3*padding+'px',
			'height' : HVSize[1]+2*padding+'px',
			'border' : borderWidth+'px solid ThreeDHighlight',
			'borderRightColor' : 'ThreeDShadow', 'borderBottomColor' : 'ThreeDShadow',
			'background' : "url('"+dir+"hv"+HVSize[0]+'x'+HVSize[1]+".png') "+padding+"px "+padding+"px no-repeat ThreeDFace"
		})
		elements.dialog.onmousedown = function() {
			instance.preserve = true
		}
		elements.dialog.onmousemove = function(e) {
			if(instance.holdHV) setHV(e)
			if(instance.holdS) setS(e)
		}
		elements.dialog.onmouseup = elements.dialog.onmouseout = function() {
			if(instance.holdHV || instance.holdS) {
				instance.holdHV = instance.holdS = false
				if(typeof instance.input.onchange == 'function') instance.input.onchange()
			}
			instance.input.focus()
		}

		// hue/value spectrum
		elements.hv = document.createElement('div')
		setStyle(elements.hv, {
			'position' : 'absolute',
			'left' : '0',
			'top' : '0',
			'width' : HVSize[0]+2*padding+'px',
			'height' : HVSize[1]+2*padding+'px',
			'background' : "url('"+dir+"cross.gif') no-repeat",
			'cursor' : 'crosshair'
		})
		var setHV = function(e) {
			var p = getMousePos(e)
			var relX = p[0]<instance.posHV[0] ? 0 : (p[0]-instance.posHV[0]>HVSize[0]-1 ? HVSize[0]-1 : p[0]-instance.posHV[0])
			var relY = p[1]<instance.posHV[1] ? 0 : (p[1]-instance.posHV[1]>HVSize[1]-1 ? HVSize[1]-1 : p[1]-instance.posHV[1])
			instance.color.setHSV(6/HVSize[0]*relX, null, 1-1/(HVSize[1]-1)*relY)
			updateDialogPointers()
			updateDialogSaturation()
			updateInput(instance.input, instance.color, null)
		}
		elements.hv.onmousedown = function(e) { instance.holdHV = true; setHV(e) }
		elements.dialog.appendChild(elements.hv)

		// saturation gradient
		elements.grad = document.createElement('div')
		setStyle(elements.grad, {
			'position' : 'absolute',
			'left' : HVSize[0]+SArrowSize[0]+2*padding+'px',
			'top' : padding+'px',
			'width' : SSize-SArrowSize[0]+'px'
		})
		// saturation gradient's samples
		for(var i=0; i+SSampleSize<=HVSize[1]; i+=SSampleSize) {
			var g = document.createElement('div')
			g.style.height = SSampleSize+'px'
			g.style.fontSize = '1px'
			g.style.lineHeight = '0'
			elements.grad.appendChild(g)
		}
		elements.dialog.appendChild(elements.grad)

		// saturation slider
		elements.s = document.createElement('div')
		setStyle(elements.s, {
			'position' : 'absolute',
			'left' : HVSize[0]+2*padding+'px',
			'top' : '0',
			'width' : SSize+padding+'px',
			'height' : HVSize[1]+2*padding+'px',
			'background' : "url('"+dir+"s.gif') no-repeat"
		})
		// IE 5 fix
		try {
			elements.s.style.cursor = 'pointer'
		} catch(eOldIE) {
			elements.s.style.cursor = 'hand'
		}
		var setS = function(e) {
			var p = getMousePos(e)
			var relY = p[1]<instance.posS[1] ? 0 : (p[1]-instance.posS[1]>HVSize[1]-1 ? HVSize[1]-1 : p[1]-instance.posS[1])
			instance.color.setHSV(null, 1-1/(HVSize[1]-1)*relY, null)
			updateDialogPointers()
			updateInput(instance.input, instance.color, null)
		}
		elements.s.onmousedown = function(e) { instance.holdS = true; setS(e) }
		elements.dialog.appendChild(elements.s)
	}


	function showDialog(input) {
		var is = [ input.offsetWidth, input.offsetHeight ]
		var ip = getElementPos(input)
		var sp = getScrollPos()
		var ws = getWindowSize()
		var ds = [
			HVSize[0]+SSize+3*padding+2*borderWidth,
			HVSize[1]+2*padding+2*borderWidth
		]
		var dp = [
			-sp[0]+ip[0]+ds[0] > ws[0]-ClientSliderSize ? (-sp[0]+ip[0]+is[0]/2 > ws[0]/2 ? ip[0]+is[0]-ds[0] : ip[0]) : ip[0],
			-sp[1]+ip[1]+is[1]+ds[1] > ws[1]-ClientSliderSize ? (-sp[1]+ip[1]+is[1]/2 > ws[1]/2 ? ip[1]-ds[1] : ip[1]+is[1]) : ip[1]+is[1]
		]

		instanceId++
		instance = {
			input : input,
			color : new color(input.value),
			preserve : false,
			holdHV : false,
			holdS : false,
			posHV : [ dp[0]+borderWidth+padding, dp[1]+borderWidth+padding ],
			posS : [ dp[0]+borderWidth+HVSize[0]+2*padding, dp[1]+borderWidth+padding ]
		}

		updateDialogPointers()
		updateDialogSaturation()

		elements.dialog.style.left = dp[0]+'px'
		elements.dialog.style.top = dp[1]+'px'
		document.getElementsByTagName('body')[0].appendChild(elements.dialog)
	}


	function hideDialog() {
		var b = document.getElementsByTagName('body')[0]
		b.removeChild(elements.dialog)

		instance = null
	}


	function updateDialogPointers() {
		// update hue/value cross
		var x = Math.round(instance.color.hue/6*HVSize[0])
		var y = Math.round((1-instance.color.value)*(HVSize[1]-1))
		elements.hv.style.backgroundPosition =
			(padding-Math.floor(HVCrossSize[0]/2)+x)+'px '+
			(padding-Math.floor(HVCrossSize[1]/2)+y)+'px'

		// update saturation arrow
		var y = Math.round((1-instance.color.saturation)*HVSize[1])
		elements.s.style.backgroundPosition = '0 '+(padding-Math.floor(SArrowSize[1]/2)+y)+'px'
	}


	function updateDialogSaturation() {
		// update saturation gradient
		var r, g, b, s, c = [ instance.color.value, 0, 0 ]
		var i = Math.floor(instance.color.hue)
		var f = i%2 ? instance.color.hue-i : 1-(instance.color.hue-i)
		switch(i) {
			case 6:
			case 0: r=0;g=1;b=2; break
			case 1: r=1;g=0;b=2; break
			case 2: r=2;g=0;b=1; break
			case 3: r=2;g=1;b=0; break
			case 4: r=1;g=2;b=0; break
			case 5: r=0;g=2;b=1; break
		}
		var gr = elements.grad.childNodes
		for(var i=0; i<gr.length; i++) {
			s = 1 - 1/(gr.length-1)*i
			c[1] = c[0] * (1 - s*f)
			c[2] = c[0] * (1 - s)
			gr[i].style.backgroundColor = 'rgb('+(c[r]*100)+'%,'+(c[g]*100)+'%,'+(c[b]*100)+'%)'
		}
	}


	function bindInputs() {
		var onfocus = function() {
			if(instance && instance.preserve) {
				instance.preserve = false
			} else {
				showDialog(this)
			}
		}
		var onblur = function() {
			if(instance && instance.preserve) return

			var This = this
			var Id = instanceId
			setTimeout(function() {
				if(instance && instance.preserve) return

				if(instance && instanceId == Id) hideDialog() // if dialog hasn't been already shown by another instance
				updateInput(This, new color(This.value), This.value)
			}, 0)
		}
		var setcolor = function(str) {
			var c = new color(str)
			updateInput(this, c, str)
			if(instance && instance.input == this) {
				instance.color = c
				updateDialogPointers()
				updateDialogSaturation()
			}
		}

		var e = document.getElementsByTagName('input')
		var matchClass = new RegExp('\\s'+bindClass+'\\s')

		for(var i=0; i<e.length; i++) {
			if(e[i].type == 'text' && matchClass.test(' '+e[i].className+' ')) {

				e[i].originalStyle = {
					'color' : e[i].style.color,
					'backgroundColor' : e[i].style.backgroundColor
				}
				e[i].setAttribute('autocomplete', 'off')
				e[i].onfocus = onfocus
				e[i].onblur = onblur
				e[i].setcolor = setcolor

				updateInput(e[i], new color(e[i].value), e[i].value)
			}
		}
	}


	function updateInput(e, color, realValue) {
		if(allowEmpty && realValue != null && !/^\s*#?([0-9A-F]{3}([0-9A-F]{3})?)\s*$/i.test(realValue)) {
			e.value = ''
			if(reflectOnBackground) {
				e.style.backgroundColor = e.originalStyle.backgroundColor
				e.style.color = e.originalStyle.color
			}
		} else {
			e.value = (leadingHash?'#':'')+color
			if(reflectOnBackground) {
				e.style.backgroundColor = '#'+color
				e.style.color =
					0.212671 * color.red +
					0.715160 * color.green +
					0.072169 * color.blue
					< 0.5 ? '#FFF' : '#000'
			}
		}
	}


	function setStyle(e, properties) {
		for(var p in properties) eval('e.style.'+p+' = properties[p]')
	}


	function getElementPos(e) {
		var x=0, y=0
		if(e.offsetParent) {
			do {
				x += e.offsetLeft
				y += e.offsetTop
			} while(e = e.offsetParent)
		}
		return [ x, y ]
	}


	function getMousePos(e) {
		if(!e) var e = window.event
		var x=0, y=0
		if(typeof e.pageX == 'number') {
			x = e.pageX
			y = e.pageY
		} else if(typeof e.clientX == 'number') {
			x = e.clientX+document.documentElement.scrollLeft+document.body.scrollLeft
			y = e.clientY+document.documentElement.scrollTop+document.body.scrollTop
		}
		return [ x, y ]
	}


	function getScrollPos() {
		var x=0, y=0
		if(typeof window.pageYOffset == 'number') {
			x = window.pageXOffset
			y = window.pageYOffset
		} else if(document.body && (document.body.scrollLeft || document.body.scrollTop)) {
			x = document.body.scrollLeft
			y = document.body.scrollTop
		} else if(document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop)) {
			x = document.documentElement.scrollLeft
			y = document.documentElement.scrollTop
		}
		return [ x, y ]
	}


	function getWindowSize() {
		var w=0, h=0
		if(typeof window.innerWidth == 'number') {
			w = window.innerWidth
			h = window.innerHeight
		} else if(document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
			w = document.documentElement.clientWidth
			h = document.documentElement.clientHeight
		} else if(document.body && (document.body.clientWidth || document.body.clientHeight)) {
			w = document.body.clientWidth
			h = document.body.clientHeight
		}
		return [ w, h ]
	}


	function color(hex) {

		this.hue        = 0 // 0-6
		this.saturation = 0 // 0-1
		this.value      = 0 // 0-1

		this.red   = 0 // 0-1
		this.green = 0 // 0-1
		this.blue  = 0 // 0-1

		this.setRGB = function(r, g, b) { // null = don't change
			var hsv = RGB_HSV(
				r==null ? this.red : (this.red=r),
				g==null ? this.green : (this.green=g),
				b==null ? this.blue : (this.blue=b)
			)
			if(hsv[0] != null) {
				this.hue = hsv[0]
			}
			this.saturation = hsv[1]
			this.value = hsv[2]
		}

		this.setHSV = function(h, s, v) { // null = don't change
			var rgb = HSV_RGB(
				h==null ? this.hue : (this.hue=h),
				s==null ? this.saturation : (this.saturation=s),
				v==null ? this.value : (this.value=v)
			)
			this.red   = rgb[0]
			this.green = rgb[1]
			this.blue  = rgb[2]
		}

		function RGB_HSV(r, g, b) {
			var n = Math.min(Math.min(r,g),b)
			var v = Math.max(Math.max(r,g),b)
			var m = v - n
			if(m == 0) return [ null, 0, v ]
			var h = r==n ? 3+(b-g)/m : (g==n ? 5+(r-b)/m : 1+(g-r)/m)
			return [ h==6?0:h, m/v, v ]
		}

		function HSV_RGB(h, s, v) {
			if(h == null) return [ v, v, v ]
			var i = Math.floor(h)
			var f = i%2 ? h-i : 1-(h-i)
			var m = v * (1 - s)
			var n = v * (1 - s*f)
			switch(i) {
				case 6:
				case 0: return [ v, n, m ]
				case 1: return [ n, v, m ]
				case 2: return [ m, v, n ]
				case 3: return [ m, n, v ]
				case 4: return [ n, m, v ]
				case 5: return [ v, m, n ]
			}
		}

		this.setString = function(hex) {
			var m = hex.match(/^\s*#?([0-9A-F]{3}([0-9A-F]{3})?)\s*$/i)
			if(m) {
				if(m[1].length==6) { // 6x hex
					this.setRGB(
						parseInt(m[1].substr(0,2),16)/255,
						parseInt(m[1].substr(2,2),16)/255,
						parseInt(m[1].substr(4,2),16)/255
					)
				} else { // 3x hex
					this.setRGB(
						parseInt(m[1].charAt(0)+m[1].charAt(0),16)/255,
						parseInt(m[1].charAt(1)+m[1].charAt(1),16)/255,
						parseInt(m[1].charAt(2)+m[1].charAt(2),16)/255
					)
				}
			} else {
				this.setRGB(0,0,0)
				return false
			}
		}

		this.toString = function() {
			var r = Math.round(this.red * 255).toString(16)
			var g = Math.round(this.green * 255).toString(16)
			var b = Math.round(this.blue * 255).toString(16)
			return (
				(r.length==1 ? '0'+r : r)+
				(g.length==1 ? '0'+g : g)+
				(b.length==1 ? '0'+b : b)
			).toUpperCase()
		}

		if(hex) {
			this.setString(hex)
		}

	}


	function URI(uri) { // See RFC3986

		this.scheme    = null
		this.authority = null
		this.path      = ''
		this.query     = null
		this.fragment  = null

		this.parse = function(uri) {
			var m = uri.match(/^(([A-Za-z][0-9A-Za-z+.-]*)(:))?((\/\/)([^\/?#]*))?([^?#]*)((\?)([^#]*))?((#)(.*))?/)
			this.scheme    = m[3] ? m[2] : null
			this.authority = m[5] ? m[6] : null
			this.path      = m[7]
			this.query     = m[9] ? m[10] : null
			this.fragment  = m[12] ? m[13] : null
			return this
		}

		this.toString = function() {
			var result = ''
			if(this.scheme    != null) result = result +      this.scheme + ':'
			if(this.authority != null) result = result +'//'+ this.authority
			if(this.path      != null) result = result +      this.path
			if(this.query     != null) result = result + '?'+ this.query
			if(this.fragment  != null) result = result + '#'+ this.fragment
			return result
		}

		this.toAbsolute = function(base) {
			var base = new URI(base)
			var r = this
			var t = new URI

			if(base.scheme == null) return false

			if(r.scheme != null && r.scheme.toLowerCase() == base.scheme.toLowerCase()) {
				r.scheme = null
			}

			if(r.scheme != null) {
				t.scheme    = r.scheme
				t.authority = r.authority
				t.path      = removeDotSegments(r.path)
				t.query     = r.query
			} else {
				if(r.authority != null) {
					t.authority = r.authority
					t.path      = removeDotSegments(r.path)
					t.query     = r.query
				} else {
					if(r.path == '') {
						t.path = base.path
						if(r.query != null) {
							t.query = r.query
						} else {
							t.query = base.query
						}
					} else {
						if(r.path.substr(0,1) == '/') {
							t.path = removeDotSegments(r.path)
						} else {
							if(base.authority != null && base.path == '') {
								t.path = '/'+r.path
							} else {
								t.path = base.path.replace(/[^\/]+$/,'')+r.path
							}
							t.path = removeDotSegments(t.path)
						}
						t.query = r.query
					}
					t.authority = base.authority
				}
				t.scheme = base.scheme
			}
			t.fragment = r.fragment

			return t
		}

		function removeDotSegments(path) {
			var out = ''
			while(path) {
				if(path.substr(0,3)=='../' || path.substr(0,2)=='./') {
					path = path.replace(/^\.+/,'').substr(1)
				} else if(path.substr(0,3)=='/./' || path=='/.') {
					path = '/'+path.substr(3)
				} else if(path.substr(0,4)=='/../' || path=='/..') {
					path = '/'+path.substr(4)
					out = out.replace(/\/?[^\/]*$/, '')
				} else if(path=='.' || path=='..') {
					path = ''
				} else {
					var rm = path.match(/^\/?[^\/]*/)[0]
					path = path.substr(rm.length)
					out = out + rm
				}
			}
			return out
		}

		if(uri) {
			this.parse(uri)
		}

	}

	// init
	createDialog()
	bindInputs()

}
