/**
 *
 * @type {{addCategory: xlvoFreeInputCategorize.addCategory}}
 */
var xlvoFreeInputCategorize = {

	drake: null,

	init: function() {
		console.log(document.querySelector("#answers"));
		this.drake = dragula([document.querySelector("#answers")])
			.on('drag', function(el) {
				xlvoFreeInputCategorize.recalculatePlayerHeight();
			}).on('drop', function(el) {
				xlvoFreeInputCategorize.recalculatePlayerHeight();
			});
		console.log('xlvoFreeInputCategorize initialized');
	},

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
		this.drake.containers.push($('div#categories fieldset').last()[0]);

		// flush input
		$('#category_input').val('');

		// recalculate height of player
		this.recalculatePlayerHeight();
	},

	addAnswer: function() {
		// append answer
		$('div#bars').append(
			'<div>' +
				'<div id="vote_id_" class="col-md-4 xlvo-vote-free-input">' +
					'<div class="well well-sm">' +
						'<span>' + $('#answer_input').val() + '</span>' +
					'</div>' +
				'</div>' +
			'</div>'
		);

		// flush input
		$('#answer_input').val('');

		// recalculate height of player
		this.recalculatePlayerHeight();
	},

	recalculatePlayerHeight: function () {
		var node = $('#xlvo-display-player').children();
		$('#xlvo-display-player').css('height', node.css('height'));
	}
};

// xlvoFreeInputCategorize.init();
