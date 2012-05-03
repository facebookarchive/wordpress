function update_preview() {
<?php
	for ($i = 0; $i < count($controls); $i++) {
		switch ($controls[$i]['type']) {
			case 'background-color':
?>
	$(document.body).getElements('<?php echo $controls[$i]['selector-mini'] ?>').each(function(item, index) {
		item.setStyle('background-color', '#' + $('<?php echo $controls[$i]['name'] ?>').value);
	});
<?php
				break;
			case 'background-image':
?>
	$(document.body).getElements('<?php echo $controls[$i]['selector-mini'] ?>').each(function(item, index) {
		item.setStyle('background-image', 'url(<?php echo $path_theme ?>/functions/image_resize.php?fn=<?php echo $controls[$i]['path'] ?>' + $('<?php echo $controls[$i]['name'] ?>').value + ')');
	});
<?php
				break;
			case 'color':
?>
	$(document.body).getElements('<?php echo $controls[$i]['selector-mini'] ?>').each(function(item, index) {
		item.setStyle('color', '#' + $('<?php echo $controls[$i]['name'] ?>').value);
	});
<?php
				break;
			case 'font-size':
?>
	$(document.body).getElements('<?php echo $controls[$i]['selector-mini'] ?>').each(function(item, index) {
		item.setStyle('font-size', ($('<?php echo $controls[$i]['name'] ?>').value / 3) + 'px');
	});
<?php
				break;
		}
	}
?>
}

function kDefaults() {
	if ($('paramspresetStyle').value != 'Custom') {
		var currentStyle = null;
<?php
	for ($i = 0; $i < count($preset_styles); $i++) {
		$preset_style = 'var style'.$i.' = new Array(';
		for ($j = 0; $j < count($preset_styles[$i]); $j++) {
			if ($j == count($preset_styles[$i]) - 1) {
				$preset_style .= "'".$preset_styles[$i][$j]."'";
			}
			else {
				$preset_style .= "'".$preset_styles[$i][$j]."',";
			}
		}
		$preset_style .= ');';
		echo $preset_style."\n";
	}
?>
		$CurrentValue = $('paramspresetStyle').value;
		
		switch ($CurrentValue) {
<?php
	for ($i = 0; $i < count($preset_styles); $i++) {
?>
			case 'style<?php echo $i ?>': curentStyle = style<?php echo $i ?>; break;
<?php
	}
?>
		}
		
<?php
	for ($i = 0; $i < count($controls); $i++) {
?>
		$('<?php echo $controls[$i]['name'] ?>').value = curentStyle[<?php echo $i ?>];
<?php
		if ($controls[$i]['type'] == 'background-color' || $controls[$i]['type'] == 'color' || $controls[$i]['type'] == 'color:hover') {
?>
		$('myRainbow_<?php echo $controls[$i]['name'] ?>_input').getElement('.overlay').style.backgroundColor = '#'+curentStyle[<?php echo $i ?>];
<?php
		}
	}
?>
		update_preview();
	}
}

function get_option(el) {
	$('myRainbow_'+el+'_input').getElement('.overlay').style.backgroundColor = '#' + $(el).value;
	$('paramspresetStyle').selectedIndex = $('paramspresetStyle').getChildren().length - 1;
}

window.addEvent('domready', function() {
	update_preview();
	
	var previewWindow = $('<?php echo $root_block ?>');
	var previewParent = previewWindow.getParent();
	var slide = new Fx.Slide(previewParent, {'wait': false}).show();
	previewParent.over = false;
	previewParent.leave = false;
	previewParent.drop = false;
	
	previewWindow.setStyle('cursor', 'move').makeDraggable({
		'droppables': $$(previewParent)
	});
	previewWindow.addEvent('mousedown', function() {
		this.setStyle('position', 'absolute');
	});
	previewParent.addEvents({
		'over': function(drag, obj) {
			if (previewParent.over) return;
			obj.droppables[0].setStyles({
				'border': '3px solid #d3d3d3',
				'background-color': '#f3f3f3'
			});
			slide.show();
			previewParent.over = true;
			previewParent.leave = false;
			previewParent.drop = false;					
		},
		'leave': function(drag, obj) {
			if (previewParent.leave) return;
			obj.droppables[0].setStyles({
				'border': '3px solid #DFDFDF',
				'background-color': '#f3f3f3'
			});
			//slide.hide();
			previewParent.over = false;
			previewParent.leave = true;
			previewParent.drop = false;
		},
		'drop': function(drag, obj) {
			if (previewParent.drop) return;
			previewWindow.setStyle('position', '');
			obj.droppables[0].setStyles({
				'border': '3px solid #DFDFDF',
				'background-color': '#f3f3f3'
			});
			previewParent.over = false;
			previewParent.leave = false;
			previewParent.drop = true;
		}
	});

	// Update settings for preview
<?php
	for ($i = 0; $i < count($controls); $i++) {
		switch ($controls[$i]['type']) {
			case 'background-color':
?>
	$('<?php echo $controls[$i]['name'] ?>').addEvent('change', function() {
		$(document.body).getElements('<?php echo $controls[$i]['selector-mini'] ?>').each(function(item, index) {
			item.setStyle('background-color', '#' + $('<?php echo $controls[$i]['name'] ?>').value);
		});
	});
<?php
				break;
			case 'background-image':
?>
	$('<?php echo $controls[$i]['name'] ?>').addEvent('change', function() {
		$(document.body).getElements('<?php echo $controls[$i]['selector-mini'] ?>').each(function(item, index) {
			item.setStyle('background-image', 'url(<?php echo $path_theme ?>/functions/image_resize.php?fn=<?php echo $controls[$i]['path'] ?>' + $('<?php echo $controls[$i]['name'] ?>').value + ')');
		});
		$('paramspresetStyle').selectedIndex = $('paramspresetStyle').getChildren().length - 1;
	});
<?php
				break;
			case 'color':
?>
	$('<?php echo $controls[$i]['name'] ?>').addEvent('change', function() {
		$(document.body).getElements('<?php echo $controls[$i]['selector-mini'] ?>').each(function(item, index) {
			item.setStyle('color', '#' + $('<?php echo $controls[$i]['name'] ?>').value);
		});
	});
<?php
				break;
			case 'color:hover':
?>
	$(document.body).getElements('<?php echo $controls[$i]['selector-mini'] ?>').each(function(item, index) {
		item.addEvent('mouseover', function() {
			item.setStyle('color', '#' + $('<?php echo $controls[$i]['name'] ?>').value);
		});
		item.addEvent('mouseout', function() {
			item.setStyle('color', '#' + $('<?php echo $controls[$i]['color-control'] ?>').value);
		});
	});
<?php
				break;
			case 'font-size':
?>
	$('<?php echo $controls[$i]['name'] ?>').addEvent('change', function() {
		$(document.body).getElements('<?php echo $controls[$i]['selector-mini'] ?>').each(function(item, index) {
			item.setStyle('font-size', ($('<?php echo $controls[$i]['name'] ?>').value / 3) + 'px');
		});
		$('paramspresetStyle').selectedIndex = $('paramspresetStyle').getChildren().length - 1;
	});
<?php
				break;
		}
	}
?>

	// Inputs
<?php
	for ($i = 0; $i < count($controls); $i++) {
		if ($controls[$i]['type'] == 'background-color' || $controls[$i]['type'] == 'color' || $controls[$i]['type'] == 'color:hover') {
?>
	var <?php echo $controls[$i]['name'] ?>_input = $('<?php echo $controls[$i]['name'] ?>');
	var r_<?php echo $controls[$i]['name'] ?> = new MooRainbow('myRainbow_<?php echo $controls[$i]['name'] ?>_input', {
		id: 'myRainbow_<?php echo $controls[$i]['name'] ?>',
		imgPath: '<?php echo $path_rainbow_images ?>',
		startColor: $('<?php echo $controls[$i]['name'] ?>').getValue().hexToRgb(true),
		onChange: function(color) {
			$('paramspresetStyle').selectedIndex = $('paramspresetStyle').getChildren().length - 1;
			<?php echo $controls[$i]['name'] ?>_input.getNext().getFirst().setStyle('background-color', color.hex);
			<?php echo $controls[$i]['name'] ?>_input.value = color.hex.substr(1,6);
<?php
			switch ($controls[$i]['type']) {
				case 'background-color':
?>
			$(document.body).getElements('<?php echo $controls[$i]['selector-mini'] ?>').each(function(item, index) {
				item.setStyle('background-color', '#' + $('<?php echo $controls[$i]['name'] ?>').value);
			});
<?php
					break;
				case 'color':
?>
			$(document.body).getElements('<?php echo $controls[$i]['selector-mini'] ?>').each(function(item, index) {
				item.setStyle('color', '#' + $('<?php echo $controls[$i]['name'] ?>').value);
			});
<?php
					break;
			}
?>
		}
	});
	r_<?php echo $controls[$i]['name'] ?>.okButton.setStyle('outline', 'none');
	$('myRainbow_<?php echo $controls[$i]['name'] ?>_input').addEvent('click', function() {
		r_<?php echo $controls[$i]['name'] ?>.okButton.focus();
		r_<?php echo $controls[$i]['name'] ?>.manualSet($('<?php echo $controls[$i]['name'] ?>').getValue().hexToRgb(true));
		r_<?php echo $controls[$i]['name'] ?>.startColor = $('<?php echo $controls[$i]['name'] ?>').getValue().hexToRgb(true);
	});
	<?php echo $controls[$i]['name'] ?>_input.addEvent('keyup', function(e) {
		e = new Event(e);
		if ((this.value.length == 4 || this.value.length == 7) && this.value[0] == '#') {
			var rgb = new Color(this.value);
			var hex = this.value;
			var hsb = rgb.rgbToHsb();
			var color = {
				'hex': hex,
				'rgb': rgb,
				'hsb': hsb
			}
			r_<?php echo $controls[$i]['name'] ?>.fireEvent('onChange', color);
			r_<?php echo $controls[$i]['name'] ?>.manualSet(color.rgb);
		};
	});
	<?php echo $controls[$i]['name'] ?>_input.getNext().getFirst().setStyle('background-color', r_<?php echo $controls[$i]['name'] ?>.sets.hex);
	
	
<?php
		}
	}
?>
});