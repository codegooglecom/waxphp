function numPad_append(target, val) {
	if ($(target)) {
		$(target).value += val;
	}
}
function numPad_clear(target) {
	if ($(target)) {
		$(target).value = '';
	}
}
function numPad_submit(target) {
	if ($(target)) {
		$(target).submit();
	}	
}