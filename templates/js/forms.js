var formCheckerLoaded = true;
$(document).ready(function () {
	/**
	 * Checks required Inputs
	 *
	 * @returns {boolean}
	 */
	$.fn.checkIliasForm = function () {
		var return_value = true;
		$(this).find('span.asterisk').each(function () {
			if ($(this).parent().parent().next('.ilFormValue').find('input').val() == '') {
				$(this).parent().parent().next('.ilFormValue').addClass('form_alert');
				return_value = false;
			}
		});
		return return_value;
	}

	/**
	 * Fired on Sendform
	 */
	$('form').submit(function () {
		return $(this).checkIliasForm();
	});

	/**
	 * Deletes Class after input is modified
	 */
	$('div.ilFormValue input').each(function () {
		var elem = $(this);
		elem.data('oldVal', elem.val());
		elem.bind("propertychange keyup input paste", function (event) {
			if (elem.data('oldVal') != elem.val()) {
				elem.data('oldVal', elem.val());
				elem.parent().parent().removeClass('form_alert');
			}
		});
	});


});