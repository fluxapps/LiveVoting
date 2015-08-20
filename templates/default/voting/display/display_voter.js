(function ($) {
	$.fn.loadVoting = function () {
		$(document).ready(function () {

			//var checkFrozen = function () {
			//	$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'check_frozen'})
			//		.done(function (data) {
			//			isFrozen = +data;
			//			return isFrozen;
			//		}).fail(function (jqXHR) {
			//			console.log(jqXHR);
			//		})
			//		.always(function () {
			//		});
			//};

			var current_voting_id = $('#voting-data').attr('voting');
			var object_id = $('#voting-data').attr('object');
			var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoPlayerEndpoint.php";

			//var isFrozen = checkFrozen();

			// load voting
			$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_voting'})
				.done(function (data) {
					if (data != '') {
						$('.display-voter').replaceWith(data);
						$('.display-voter').initFreeInputDeleteButtons();
					}
					//if (isFrozen == 1) {
					//	console.log('now');
					//	$('.display-voter').hide();
					//}
				}).fail(function (jqXHR) {
					console.log(jqXHR);
				})
				.always(function () {
				});


		});
	}
}(jQuery));

// frozen: waiting screen
// end / start: voting not available
// reset: load voting again

setInterval($('.display-voter').loadVoting, 2000);