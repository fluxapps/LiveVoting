/**
 * Class xlvoFreeInput
 * @type {{}}
 */
var xlvoFreeInput = {
	init: function (json) {
		var config = json;
		var replacer = new RegExp('amp;', 'g');
		config.base_url = config.base_url.replace(replacer, '');
		this.config = config;
		this.ready = true;

		new MutationObserver(function () { // Detect html changes
			$('input[name^="vote_multi_line_input["][name$="][free_input]"]:not([data-has_auto_submit])').each( // Intentionally not do this for textareas ...
				/**
				 * @param {number} i
				 * @param {HTMLInputElement} input
				 */
				function (i, input) {
					input.dataset.has_auto_submit = "true"; // Only new input fields

					$(input).on("keydown", function (e) {
						if (e.keyCode === 13) {
							e.preventDefault(); // Prevent some browsers auto submit if only one input field
						}
					});

					$(input).on("keyup", function (e) {
						if (e.keyCode === 13) {
							e.preventDefault();

							$('input[type="submit"][name="cmd[submit]"]', input.form).click(); // Find submit button of form and submit it
						}
					});
				});
		}).observe($("#xlvo_voter_player").parent()[0], {
			attributes: false,
			childList: true,
			characterData: false,
			subtree: true
		});
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
