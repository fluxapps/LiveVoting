$(document).ready(function () {
	(function ($) {
		$.fn.ctrlMM = function (options) {
			var settings = $.extend({
				menu_id: 0
			}, options);

			$.get("Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/jsonMenu.php", settings)
				.done(function (data) {
//					console.log(data);
					$('#ctrlmmtest').html(data);
				});
		};
	}(jQuery));
});