/**
 * Class xlvoFreeOrder
 * @type {{}}
 */
var xlvoFreeOrder = {
	init: function (json) {
		var config = JSON.parse(json);
		var replacer = new RegExp('amp;', 'g');
		config.base_url = config.base_url.replace(replacer, '');
		this.config = config;
		this.ready = true;

		setTimeout(function () {
			alert();
			$('input[name^="vote_multi_line_input["][name$="][free_input]"]').each(function (i, input) {
				alert();
				$(input).on("keyup", function (e) {
					if (e.keyCode === 13) {alert();
						e.preventDefault();

						input.form.submit();
					}
				});
			});
		}, 500);
	},
	config: {},
	base_url: '',
	run: function () {
	},
	/**
	 * @param button_id
	 * @param button_data
	 */
	handleButtonPress: function (button_id, button_data) {
	}
};
