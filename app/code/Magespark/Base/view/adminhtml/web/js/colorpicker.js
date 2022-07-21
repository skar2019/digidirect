require([
	'jquery',
	'MageSpark_Base/js/src/colorpicker'
], function ($) {
	'use strict';
	$('.colorSelector').each(function() {
		const ele_id = '#'+$(this).attr('id');
		const ele_val = $(this).val();
		$(ele_id).css('backgroundColor', '#' + ele_val);
		$(ele_id).ColorPicker({
			color: ele_val,
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$(ele_id).css('backgroundColor', '#' + hex);
				$(ele_id).val(hex);
			}
		});
	});
});