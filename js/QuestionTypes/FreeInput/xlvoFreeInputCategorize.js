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
	 *
	 */
	base_url: '',

	/**
	 *  init dragula and event listeners
	 */
	init: function(base_url) {
		this.base_url = base_url;
		this.initDragula();
		this.initButtons();
		this.initialized = true;
		console.log('xlvoFreeInputCategorize initialized');
	},

	/**
	 *
	 */
	initDragula: function () {
		// the new HTML is added first, then the old one is removed - therefore there are two 'div.bars' atm
		this.drake = dragula([$("div.bars")[1]], {
			moves: function (el) {
				return $(el).is("div");
			}
		})
			.on('drag', function (el) {
				xlvoFreeInputCategorize.recalculatePlayerHeight();
			}).on('drop', function (el) {
				xlvoFreeInputCategorize.recalculatePlayerHeight();
			});
	},

	/**
	 *
	 */
	initButtons: function() {
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
		$('div#categories').append(
			'<div class="col-md-4">' +
				'<button type="button" class="close" aria-label="Close" onClick="xlvoFreeInputCategorize.removeCategory($(this).parent());">' +
					'<span aria-hidden="true">&times;</span>' +
				'</button>' +
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
	 * @param category
	 */
	removeCategory: function(category) {
		console.log($(category).find('div.col-md-4'));
		$(category).find('div.col-md-4').each(function(key, element) {
			console.log(element);
			$('div#bars').append(element);
		});
		category.remove();
	},

	/**
	 *
	 */
	addAnswer: function() {
		// append answer
		$('div#bars').append(
			'<div class="col-md-4">' +
				'<div id="vote_id_" class="xlvo-vote-free-input">' +
					'<button type="button" class="close" aria-label="Close" onClick="$(this).parent().parent().remove();">' +
						'<span aria-hidden="true">&times;</span>' +
					'</button>' +
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