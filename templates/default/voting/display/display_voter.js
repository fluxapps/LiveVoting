(function ($) {
	$.fn.loadVoting = function () {
		$(document).ready(function () {

			var current_voting_id = $('#voting-data').attr('voting');
			var object_id = $('#voting-data').attr('object');
			var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoPlayerEndpoint.php";

			var loadVotingScreen = function () {

				// load voting
				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_voting_screen'})
					.done(function (data) {
						if (data != '') {
							$('.display-voter').replaceWith(data);
							$('.display-voter').initFreeInputDeleteButtons();
						}
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
					});
			};

			var loadWaitingScreen = function () {
				// load waiting screen
				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_waiting_screen'})
					.done(function (data) {
						$('.display-voter').replaceWith(data);
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
					});
			};

			var loadNotAvailableScreen = function () {

			};

			var loadAccessScreen = function () {

			};

			var callVotingFunction = function () {
				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'get_voting_data'})
					.done(function (data) {
						var isFrozen = +data.voIsFrozen;
						var isReset = +data.voIsReset;
						var status = +data.voStatus;
						var isAvailable = +data.voIsAvailable;
						var hasAccess = +data.voHasAccess;

						if (isFrozen) {
							loadWaitingScreen();
						} else if (isReset) {
							// set votingId to 0 to reload current voting
							$('#voting-data').attr('voting', 0);
							loadVotingScreen();
						} else if (status == 0) {
							loadNotAvailableScreen();
						} else if (isAvailable == 0) {
							loadNotAvailableScreen();
						} else if(hasAccess == 0) {
							loadAccessScreen();
						} else {
							loadVotingScreen();
						}
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
					});
			};

			callVotingFunction();

		});
	}
}(jQuery));

// called in display_voter.js after a new voting was loaded
(function ($) {
	$.fn.initFreeInputDeleteButtons = function () {
		$(document).ready(function () {

			// hide delete button if no existing
			var vote_id = $('#vote_id').val();
			if (vote_id == 0) {
				$("input[name='cmd[send_unvote]']").hide();
			}

			// hide delete button if no existing
			if ($("input[name='vote_multi_line_input[0][free_input]']").length) {
				$("input[name='cmd[unvote_all]']").hide();
			}

		})
	}
}(jQuery));

// frozen: waiting screen
// end / start: voting not available
// reset: load voting again
// end of voting
// access page

setInterval($('.display-voter').loadVoting, 2000);