/**
 * il.waiter
 *
 * GUI-Overlay
 *
 * @type {Object}
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
il.waiter = {
	/**
	 * @type {string}
	 */
	type: 'waiter',
	/**
	 * @type {number}
	 */
	count: 0,
	/**
	 * @type {number|null}
	 */
	timer: null,

	/**
	 * @param {string} type
	 */
	init: function (type) {
		this.type = type ? type : this.type;
		if (this.type == 'waiter') {
			$('body').append('<div id="srag_waiter" class="srag_waiter"></div>');
			//console.log('il.waiter: added srag_waiter to body');
		} else {
			$('body').append('<div id="srag_waiter" class="srag_waiter_percentage">' +
				'<div class="progress" >' +
				'<div id="srag_waiter_progress" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">' +
				'</div></div></div>');
			//console.log('il.waiter: added srag_waiter_percentage to body');
		}
	},

	/**
	 *
	 */
	show: function () {
		if (this.count == 0) {
			this.timer = setTimeout(function () {
				$('#srag_waiter').show();
			}, 10);

		}
		this.count = this.count + 1;
	},

	/**
	 * @param {string} type
	 */
	reinit: function (type) {
		var type = type ? type : this.type;
		this.count = 0;

		$('#srag_waiter').attr('id', 'srag_waiter2');
		this.init(type);
		$('#srag_waiter2').remove();
	},

	/**
	 *
	 */
	hide: function () {
		this.count = this.count - 1;
		if (this.count == 0) {
			window.clearTimeout(this.timer);
			$('#srag_waiter').fadeOut(200);
		}
	},

	/**
	 * @param {number} percent
	 */
	setPercentage: function (percent) {
		$('#srag_waiter_progress').css('width', percent + '%').attr('aria-valuenow', percent);
	},

	/**
	 * @param {string} dom_selector_string
	 */
	addListener: function (dom_selector_string) {
		var self = this;
		$(document).ready(function () {
			$(dom_selector_string).on("click", function () {

				self.show();
			});
		});
	},

	/**
	 *
	 * @param {string} dom_selector_string
	 */
	addLinkOverlay: function (dom_selector_string) {
		var self = this;
		$(document).ready(function () {
			$(dom_selector_string).on("click", function (e) {
				e.preventDefault();
				//console.log('il.waiter: clicked on registred link');
				self.show();
				var href = $(this).attr('href');
				setTimeout(function () {
					document.location.href = href;
				}, 1000);
			});
		});
		//console.log('il.waiter: registred LinkOverlay: ' + dom_selector_string);
	}
};
