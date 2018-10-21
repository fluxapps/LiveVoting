/**
 * Class xlvoNumberRange
 * @type {{}}
 */
var xlvoNumberRange = {
	init: function (json) {
		var config = json;
		var replacer = new RegExp('amp;', 'g');
		config.base_url = config.base_url.replace(replacer, '');
		this.config = config;
		this.ready = true;
		this.percentageSign = '';

	},
	config: {},
	base_url: '',
	run: function () {
		var slider = $("#slider").bootstrapSlider();

		var step = parseInt(slider.attr("data-slider-step"));

		this.percentageSign = $('#percentage')[0].value === "1" ? ' %' : '';

		var numberDisplay = $('#number-display');
		var oldText = numberDisplay.text();

		numberDisplay.text(oldText.concat(this.percentageSign));

		slider = slider.bootstrapSlider();

		slider.change(
			function (changedValues) {
				$('#number-display').text(String(changedValues.value.newValue).concat(this.percentageSign));
			}.bind(this));

		//left button click event to move the slider to the left
		var buttonMoveSliderLeft = document.querySelector("#btn-slider-left");
		buttonMoveSliderLeft.onclick = function () {

			var sliderValue = slider.bootstrapSlider("getValue");
			if (slider.bootstrapSlider("getAttribute", "min") < sliderValue)
				slider.bootstrapSlider("setValue", sliderValue - step, false, true);

		}.bind(slider);

		//right button click event to move the slider to the right
		var buttonMoveSliderRight = document.querySelector("#btn-slider-right");
		buttonMoveSliderRight.onclick = function () {

			var sliderValue = slider.bootstrapSlider("getValue");
			if (slider.bootstrapSlider("getAttribute", "max") > sliderValue)
				slider.bootstrapSlider("setValue", sliderValue + step, false, true);

		}.bind(slider);
	},
	/**
	 * @param button_id
	 * @param button_data
	 */
	handleButtonPress: function (button_id, button_data) {

	}
};
