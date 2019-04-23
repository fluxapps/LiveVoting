/**
 *
 * @type {{addCategory: xlvoFreeInputCategorize.addCategory}}
 */
var xlvoFreeInputCategorize = {

	/**
	 * dragula object
	 */
	drake: null,

	/**
	 *  init dragula and event listeners
	 */
	init: function() {
		console.log('initialize xlvoFreeInputCategorize');
		this.drake = dragula([document.querySelector("#bars")], {
			moves: function (el) {
				return !$(el).is("legend");
			}
		})
		.on('drag', function(el) {
			xlvoFreeInputCategorize.recalculatePlayerHeight();
		}).on('drop', function(el) {
			xlvoFreeInputCategorize.recalculatePlayerHeight();
		});

		$('#category_input').on("keypress", function(e) {
			if (e.which == 13) {
				xlvoFreeInputCategorize.addCategory();
			}
		});

		$('#category_button').on("click", xlvoFreeInputCategorize.addCategory);

		$('#answer_input').on("keypress", function(e) {
			if (e.which == 13) {
				xlvoFreeInputCategorize.addAnswer();
			}
		});

		$('#answer_button').on("click", xlvoFreeInputCategorize.addAnswer);
	},

	/**
	 *
	 */
	addCategory: function() {
		// append category
		categories = $('div#categories').append(
			'<div class="col-md-4">' +
				'<fieldset class="well xlvo-category category_dropzone">' +
						'<legend>' + $('#category_input').val() + '</legend>' +
				'</fieldset>' +
			'</div>'
		);

		// add new container to dragula
		console.log($('div#categories fieldset').last()[0]);
		xlvoFreeInputCategorize.drake.containers.push($('div#categories fieldset').last()[0]);

		// flush input
		$('#category_input').val('');

		// recalculate height of player
		xlvoFreeInputCategorize.recalculatePlayerHeight();
	},

	/**
	 *
	 */
	addAnswer: function() {
		// append answer
		$('div#bars').append(
			'<div class="col-md-4">' +
				'<div id="vote_id_" class="xlvo-vote-free-input">' +
					'<div class="well well-sm">' +
						'<span>' + $('#answer_input').val() + '</span>' +
					'</div>' +
				'</div>' +
			'</div>'
		);

		// flush input
		$('#answer_input').val('');

		// recalculate height of player
		xlvoFreeInputCategorize.recalculatePlayerHeight();
	},

	/**
	 * called in addCategory and addAnswer
	 */
	recalculatePlayerHeight: function () {
		var node = $('#xlvo-display-player').children();
		$('#xlvo-display-player').css('height', node.css('height'));
	}
};