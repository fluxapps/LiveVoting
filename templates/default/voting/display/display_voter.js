(function ($) {
	$.fn.loadVoting = function () {
		$(document).ready(function () {

			var current_voting_id = $('#voting-data').attr('voting');
			var object_id = $('#voting-data').attr('object');
			var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoPlayerEndpoint.php";

			var loadVotingScreen = function () {
				// load voting
				$.get(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'voting_screen'})
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

			var loadInfoScreen = function (type_player) {
				var type = $('#voting-data').attr('info-type');
				if (type != type_player) {
					$.get(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: type_player})
						.done(function (data) {
							$('.display-voter').replaceWith(data);
						}).fail(function (jqXHR) {
							console.log(jqXHR);
						}).always(function () {
						});
				}
			};

			var loadWaitingScreen = function () {
				loadInfoScreen('waiting_screen');
			};

			var loadNotRunningScreen = function () {
				loadInfoScreen('not_running_screen');
			};

			var loadNotAvailableScreen = function () {
				loadInfoScreen('not_available_screen');
			};

			var loadAccessScreen = function () {
				loadInfoScreen('access_screen');
			};

			var loadStartOfVotingScreen = function () {
				loadInfoScreen('start_of_voting_screen');
			};

			var loadEndOfVotingScreen = function () {
				loadInfoScreen('end_of_voting_screen');
			};

			var callVotingFunction = function () {
				$.get(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'get_voting_data'})
					.done(function (data) {
						var isFrozen = +data.voIsFrozen;
						var status = +data.voStatus;
						var isAvailable = +data.voIsAvailable;
						var hasAccess = +data.voHasAccess;

						if (hasAccess == 0) {
							loadAccessScreen();
						} else if (status == 0) {
							// status 0 = stopped
							loadNotRunningScreen();
						} else if (isAvailable == 0) {
							loadNotAvailableScreen();
						} else if (status == 2) {
							// status 2 = start of voting
							loadStartOfVotingScreen();
						} else if (status == 3) {
							// status 3 = end of voting
							loadEndOfVotingScreen();
						} else if (isFrozen) {
							loadWaitingScreen();
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

setInterval($('.display-voter').loadVoting, 2000);

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

(function ($) {
	$.fn.accessVoting = function () {
		$(document).ready(function () {

			// prevent submitting over (mobile)keyboard
			$(document).keypress(function (event) {
				if (event.keyCode == 10 || event.keyCode == 13)
					event.preventDefault();

			});

			$('#il_center_col').on('submit', '#form_access', function (event) {
				event.preventDefault();

				// get values for POST request
				var pin_input = $('#pin_input').val();
				var object_id = $('#voting-data').attr('object');
				var current_voting_id = $('#voting-data').attr('voting');
				var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/display/class.xlvoPlayerEndpoint.php";

				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'access_voting', pin_input: pin_input})
					.done(function (data) {
						$('.display-voter').replaceWith(data);
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
					});
				return false;
			});
		});
	}
}(jQuery));

$(".display_voter").accessVoting();