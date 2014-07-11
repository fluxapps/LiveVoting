$(document).ready(function () {
	var fixHelper = function (e, ui) {
		ui.children().each(function () {
			$(this).width($(this).width());
		});
		return ui;
	};

	$("table tbody").sortable({
		helper: fixHelper,
		items: '.fsxSortable'
	}).disableSelection();
});