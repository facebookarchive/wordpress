<style type="text/css">
<?php
	require (TEMPLATEPATH . '/include/settings-color.php');
	for ($i = 0; $i < count($controls); $i++) {
		switch ($controls[$i]['type']) {
			case 'background-color':
?>
	<?php echo $controls[$i]['selector'] ?> {
		background-color: #<?php echo t_get_coption($controls[$i]['name']) ?><?php if (isset($controls[$i]['important']) && $controls[$i]['important']) { echo '!important'; } ?>;
	}
<?php
				break;
			case 'background-image':
?>
	<?php echo $controls[$i]['selector'] ?> {
		background-image: url('<?php echo $controls[$i]['path'].t_get_coption($controls[$i]['name']) ?>')<?php if (isset($controls[$i]['important']) && $controls[$i]['important']) { echo '!important'; } ?>;
	}
<?php
				break;
			case 'color':
?>
	<?php echo $controls[$i]['selector'] ?> {
		color: #<?php echo t_get_coption($controls[$i]['name']) ?><?php if (isset($controls[$i]['important']) && $controls[$i]['important']) { echo '!important'; } ?>;
	}
<?php
				break;
			case 'color:hover':
?>
	<?php echo $controls[$i]['selector'] ?> {
		color: #<?php echo t_get_coption($controls[$i]['name']) ?><?php if (isset($controls[$i]['important']) && $controls[$i]['important']) { echo '!important'; } ?>;
	}
<?php
				break;
			case 'font-size':
?>
	<?php echo $controls[$i]['selector'] ?> {
		font-size: <?php echo t_get_coption($controls[$i]['name']) ?>px<?php if (isset($controls[$i]['important']) && $controls[$i]['important']) { echo '!important'; } ?>;
	}
<?php
				break;
		}
	}
?>
</style>

