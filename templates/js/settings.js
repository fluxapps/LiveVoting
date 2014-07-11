$(document).ready(function () {
	var mm_sform = $("#form_ctrl_mm_settings_entry_form");
	mm_sform.submit(function () {
		mm_sform.fadeOut(500);
		var slink = "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/CtrlMainMenu/classes/EntryTypes/Settings/settings.php";
		$.post(slink, mm_sform.serialize()).done(function (data) {
			console.log(data)
			mm_sform.parent().attr('yui-overlay-hidden');
			if(data) {
				location.reload();
			}
		});

		return false;
	});
});