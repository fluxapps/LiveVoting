(function ($) {
	$.fn.loadVoting = function () {
		$(document).ready(function () {

			var current_voting_id = $('#voting-data').attr('voting');
			var object_id = $('#voting-data').attr('object');
			var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoPlayerEndpoint.php";

			// load voting
			$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_voting'})
				.done(function (data) {
					if (data != '') {
						$('.display-voter').replaceWith(data);
					}
				}).fail(function (jqXHR) {
					console.log(jqXHR);
				})
				.always(function () {
				});
		});
	}
}(jQuery));

setInterval($('.display-voter').loadVoting, 2000);