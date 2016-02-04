var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/Voter/xlvo_voter_ajax.php";

(function ($) {
	$.fn.loadResults = function () {
		$(document).ready(function () {

			var current_voting_id = $('#voting-data').attr('Voting');
			var object_id = $('#voting-data').attr('object');

			// load Voting
			$.get(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_results'})
				.done(function (data) {
					console.log(data);
					if (data.length > 10) {
						$('#display-player').replaceWith(data);
						$('#display-player').hideAndShowResults();
					}
				}).fail(function (jqXHR) {
					console.log(jqXHR);
				}).always(function () {
				});

		});
	}
}(jQuery));

(function ($) {
	$.fn.loadPlayerInfo = function () {
		$(document).ready(function () {

			var current_voting_id = $('#voting-data').attr('Voting');
			var object_id = $('#voting-data').attr('object');

			// load Voting
			$.get(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'load_player_info'})
				.done(function (data) {
					//console.log(data);
				}).fail(function (jqXHR) {
					console.log(jqXHR);
				}).always(function () {
				});

		});
	}
}(jQuery));

(function ($) {
	$.fn.hideAndShowResults = function () {
		$(document).ready(function () {

			var showResults = $('#btn-show-results');
			var hideResults = $('#btn-hide-results');

			if (hideResults.css('display') == 'none') {
				showResults.show();
				showResults.parent().show();
				$('.display-results').hide();
			} else {
				hideResults.show();
				hideResults.parent().show();
				$('.display-results').show();
			}

			hideResults.click(function () {
				showResults.show();
				showResults.parent().show();
				hideResults.hide();
				hideResults.parent().hide();
				$('.display-results').hide();
			});

			showResults.click(function () {
				hideResults.show();
				hideResults.parent().show();
				showResults.hide();
				showResults.parent().hide();
				$('.display-results').show();
			});

		});
	}
}(jQuery));

(function ($) {
	$.fn.initToolbarButtons = function () {
		$(document).ready(function () {

			var btnNext = $('#btn-next');
			//btnNext.html(btnNext.text() + '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>');
			var btnPrevious = $('#btn-previous');
			//btnPrevious.html('<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>' + btnPrevious.text());
			var btnTerminate = $('#btn-terminate');
			//btnTerminate.html('<span class="glyphicon glyphicon-stop" aria-hidden="true"></span>' + btnTerminate.text());
			var btnReset = $('#btn-reset');
			//btnReset.html('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>' + btnReset.text());
			var btnBackToVoting = $('#btn-back-to-Voting');
			//btnBackToVoting.html('<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>' + btnBackToVoting.text());
			var btnStartVoting = $('#btn-start-Voting');
			//btnStartVoting.html('<span class="glyphicon glyphicon-play" aria-hidden="true"></span>' + btnStartVoting.text());


			var btnShowResults = $('#btn-show-results');
			//btnShowResults.html('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>' + btnShowResults.text());
			var btnHideResults = $('#btn-hide-results');
			//btnHideResults.html('<span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>' + btnHideResults.text());
			btnHideResults.hide();
			btnHideResults.parent().hide();

			var isFrozen = $('#voting-data').attr('frozen');
			var btnFreeze = $('#btn-freeze');
			var btnUnfreeze = $('#btn-unfreeze');
			if (isFrozen == true) {
				btnFreeze.hide();
				btnFreeze.parent().hide();
				//btnUnfreeze.html('<span class="glyphicon glyphicon-play" aria-hidden="true"></span>' + btnUnfreeze.text());
				btnReset.attr('disabled', false);
			} else {
				btnUnfreeze.hide();
				btnUnfreeze.parent().hide();
				//btnFreeze.html('<span class="glyphicon glyphicon-pause" aria-hidden="true"></span>' + btnFreeze.text());
				btnReset.attr('disabled', 'disabled');
			}

		});
	}
}(jQuery));

(function ($) {
	$.fn.freezeVoting = function () {
		$(document).ready(function () {

			var btnFreeze = $('#btn-freeze');
			var btnUnfreeze = $('#btn-unfreeze');
			var btnReset = $('#btn-reset');

			btnFreeze.click(function () {

				var current_voting_id = $('#voting-data').attr('Voting');
				var object_id = $('#voting-data').attr('object');

				// freeze
				var post_data = {voting_id_current: current_voting_id, object_id: object_id, type_player: 'freeze_voting'};

				$.post(url, post_data)
					.done(function (data) {
						if (data.length < 10) {
							btnFreeze.hide();
							btnFreeze.parent().hide();
							btnUnfreeze.show();
							btnUnfreeze.parent().show();
							btnUnfreeze.html('<span class="glyphicon glyphicon-play" aria-hidden="true"></span>' + btnUnfreeze.text());
							btnReset.attr('disabled', false);
						} else {
							$('#display-player').replaceWith(data);
						}
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
					});
			});
		});
	}
}(jQuery));

(function ($) {
	$.fn.unfreezeVoting = function () {
		$(document).ready(function () {

			var btnFreeze = $('#btn-freeze');
			var btnUnfreeze = $('#btn-unfreeze');
			var btnReset = $('#btn-reset');

			btnUnfreeze.click(function () {

				var current_voting_id = $('#voting-data').attr('Voting');

				var object_id = $('#voting-data').attr('object');

				// unfreeze
				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'unfreeze_voting'})
					.done(function (data) {
						if (data.length < 10) {
							btnFreeze.show();
							btnFreeze.parent().show();
							btnFreeze.html('<span class="glyphicon glyphicon-pause" aria-hidden="true"></span>' + btnFreeze.text());
							btnUnfreeze.hide();
							btnUnfreeze.parent().hide();
							btnReset.attr('disabled', 'disabled');
						} else {
							$('#display-player').replaceWith(data);
						}
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
					});
			});
		});
	}
}(jQuery));

(function ($) {
	$.fn.resetVoting = function () {
		$(document).ready(function () {

			var btnReset = $('#btn-reset');

			btnReset.click(function () {

				var current_voting_id = $('#voting-data').attr('Voting');
				var object_id = $('#voting-data').attr('object');

				$('.ilToolbar').find('.btn.btn-default').attr('class', 'btn btn-default disabled');

				// reset votes of current Voting
				$.post(url, {voting_id_current: current_voting_id, object_id: object_id, type_player: 'reset_voting'})
					.done(function (data) {
						if (data.length < 10) {
							$('#display-player').loadResults();
						} else {
							$('#display-player').replaceWith(data);
						}

					}).fail(function (jqXHR) {
						console.log(jqXHR);
					}).always(function () {
						$('.ilToolbar').find('.btn.btn-default').attr('class', 'btn btn-default');
						$('#btn-unfreeze').css('background-color', '#557B2E').css('background-color', '#4F732B');
					});
			});
		});
	}
}(jQuery));

var displayPlayer = $('#display-player');

setInterval(displayPlayer.loadResults, 1000);
setInterval(displayPlayer.loadPlayerInfo, 2000);
displayPlayer.initToolbarButtons();
displayPlayer.hideAndShowResults();
displayPlayer.freezeVoting();
displayPlayer.unfreezeVoting();
displayPlayer.resetVoting();