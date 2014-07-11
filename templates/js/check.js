$(document).ready(function () {
	setInterval(function () {
		var gui_classes = $('#gui_class').val();
		$.ajax({
			type: 'GET',
			url: 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/checkCtrl.php?classes=' + gui_classes,
			data: '',
			contentType: 'application/json;',
			success: function (data) {
				if (data.status == true) {
					$('#gui_class').removeClass('ilctrl_failure');
					$('#gui_class').addClass('ilctrl_ok');
				} else {
					$('#gui_class').removeClass('ilctrl_ok');
					$('#gui_class').addClass('ilctrl_failure');
				}

			},
			error: function (xhr, ajaxOptions, thrownError) {
				console.log('error...', xhr);
			},
			complete: function () {
			}
		});
	}, 1000);
});