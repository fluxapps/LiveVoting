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
				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_waiting_screen'})
					.done(function (data) {
						$('.display-voter').replaceWith(data);
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
					});
			};

			var loadNotStartedScreen = function () {
				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_not_running_screen'})
					.done(function (data) {
						$('.display-voter').replaceWith(data);
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
					});
			};

			var loadNotAvailableScreen = function () {
				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_not_available_screen'})
					.done(function (data) {
						$('.display-voter').replaceWith(data);
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
					});
			};

			var loadAccessScreen = function () {
				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_access_screen'})
					.done(function (data) {
						$('.display-voter').replaceWith(data);
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
					});
			};

			var loadEndOfVotingScreen = function () {
				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_end_of_voting_screen'})
					.done(function (data) {
						$('.display-voter').replaceWith(data);
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
					});
			};

			var callVotingFunction = function () {
				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'get_voting_data'})
					.done(function (data) {
						var isFrozen = +data.voIsFrozen;
						var isReset = +data.voIsReset;
						var status = +data.voStatus;
						var isAvailable = +data.voIsAvailable;
						var hasAccess = +data.voHasAccess;

						if (hasAccess == 0) {
							loadAccessScreen();
						} else if (status == 0) {
							// status 0 = stopped
							loadNotStartedScreen();
						} else if (isAvailable == 0) {
							loadNotAvailableScreen();
						} else if (status == 2) {
							// status 2 = end of voting
							loadEndOfVotingScreen();
						} else if (isFrozen) {
							loadWaitingScreen();
						} else if (isReset) {
							// set votingId to 0 to reload current voting
							$('#voting-data').attr('voting', 0);
							loadVotingScreen();
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

// For freeInput voting type only.
// Initializes delete buttons for freeInput form after page replacement.
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

setInterval($('.display-voter').loadVoting, 2000);