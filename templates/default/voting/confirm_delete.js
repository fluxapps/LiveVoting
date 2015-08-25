(function ($) {
	$.fn.confirmDelete = function () {

		console.log(this);

		this.on('change', this, function (event) {
			console.log('delete confirmed');
			confirm('test');
		});

	}
}(jQuery));

//$('.btn.btn-default.multi_icon.remove_button').confirmDelete();