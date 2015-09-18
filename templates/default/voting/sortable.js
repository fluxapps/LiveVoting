$(document).ready(function () {
	var fixHelper = function (e, ui) {
		ui.children().each(function () {
			$(this).width($(this).width());
		});
		return ui;
	};

	$("div.ilTableOuter table tbody").sortable({
		helper: fixHelper,
		items: '.fsxSortable'
	}).disableSelection();
});
