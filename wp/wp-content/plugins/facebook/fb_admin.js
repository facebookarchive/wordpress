function toggleOptions(parentOption, childOptions) {
			console.log(parentOption);
	console.log(childOptions.length);
	var display = '';
	
	if (document.getElementById(parentOption).checked == false) {
		display = 'none';
	}
	for (var i = 0; i < childOptions.length; i++) {
		console.log(childOptions[i]);
		console.log(document.getElementById(childOptions[i]));
		document.getElementById(childOptions[i]).style.display = display;
	}
}