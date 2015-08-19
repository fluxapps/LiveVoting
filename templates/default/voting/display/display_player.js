(function ($) {
	$.fn.loadResults = function () {
		$(document).ready(function () {

			var current_voting_id = $('#voting-data').attr('voting');
			var object_id = $('#voting-data').attr('object');
			var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoPlayerEndpoint.php";

			// load voting
			$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_results'})
				.done(function (data) {
					if (data != '') {
						$('.display-player').replaceWith(data);
						$('.display-player').hideAndShowResults();
					}
				}).fail(function (jqXHR) {
					console.log(jqXHR);
				})
				.always(function () {
				});

		});
	}
}(jQuery));

setInterval($('.display-player').loadResults, 5000);

(function ($) {
	$.fn.hideAndShowResults = function () {
		$(document).ready(function () {

			$('#show-results').hide();
			$('#show-results').parent().hide();

			var display = $('#hide-results').css('display');
			if(display == 'none') {
				$('#show-results').show();
				$('#show-results').parent().show();
				$('.display-results').hide();
			}

			$('#hide-results').click(function () {
				$('#show-results').show();
				$('#show-results').parent().show();
				$('#hide-results').hide();
				$('#hide-results').parent().hide();
				$('.display-results').hide();
			});

			$('#show-results').click(function () {
				$('#hide-results').show();
				$('#hide-results').parent().show();
				$('#show-results').hide();
				$('#show-results').parent().hide();
				$('.display-results').show();
			});

		});
	}
}(jQuery));

$('.display-player').hideAndShowResults();