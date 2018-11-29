/**
 * Class xlvoModal
 * @type {{}}
 */
var xlvoModal = {
	init: function (json) {
		this.config = json;

		var ilmodal = $('#' + this.config.id);
		ilmodal.on('show.bs.modal', function () {
			var modal = $('.modal-content');
			modal.css('overflow', 'hidden');
			if ($('.xlvo-fullscreen').length > 0) {
				modal.css('height', $(window).height() * 0.75);
			} else {
				modal.css('height', $(window).height() * 0.95);
			}
			var new_img_height = modal.height() - 150;
			var img = modal.find('img');

			img.css('height', new_img_height);
			xlvoModal.autoSizeText();
		});

		ilmodal.on('shown.bs.modal', function () {
			xlvoModal.autoSizeText();
		});
	},

	run: function () {

	},

	/**
	 *
	 * @param elements
	 * @returns {Array}
	 */
	autoSizeText: function () {
		var el, elements, _i, _len, _results;
		elements = $('.resize');
		elements.css('display', 'block');
		if (elements.length < 0) {
			return;
		}
		_results = [];
		for (_i = 0, _len = elements.length; _i < _len; _i++) {
			el = elements[_i];
			_results.push((function (el) {
				var resizeText, _results1;
				resizeText = function () {
					var elNewFontSize;
					elNewFontSize = (parseInt($(el).css('font-size').slice(0, -2)) - 1) + 'px';
					return $(el).css('font-size', elNewFontSize);
				};
				_results1 = [];
				while (el.scrollWidth > el.offsetWidth) {
					_results1.push(resizeText());
				}
				return _results1;
			})(el));
		}
		return _results;
	}

};
