(function ($) {
	$.fn.freeInputVote = function () {
		$(document).ready(function () {

			// prevent submitting over (mobile)keyboard
			$(document).keypress(function (event) {
				if (event.keyCode == 10 || event.keyCode == 13)
					event.preventDefault();

			});

			$('body').on('click', 'input[type=submit]', function () {
				$(this).attr('clicked', 'true');
			});

			$('#il_center_col').on('submit', '#form_free_input', function (event) {
				event.preventDefault();
				// get values for POST request
				var free_input = $('#free_input').val();
				var option_id = $('#option_id').val();
				var vote_id = $('#vote_id').val();
				var object_id = $('#Voting-data').attr('object');
				var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/FreeInput/class.xlvoFreeInputSubmitEndpoint.php";

				// get name of submit button
				var button = $(this).find("input[type=submit][clicked=true]");
				button.attr('clicked', 'false');
				var submit_name = button.attr('name');

				// send vote
				if (submit_name == 'cmd[send_vote]') {
					$.post(url, {free_input: free_input, option_id: option_id, vote_id: vote_id, object_id: object_id, type: 'vote'})
						.done(function (data) {

							// check if data is javascript object; else display html info_screen with error_msg
							if (typeof data === 'object') {
								// set button style to default
								$('.btn-default').attr('class', 'btn btn-default btn-sm');
								for (var key in data) {
									var vote = data[key];
									if (vote['status'] == 1) {
										// set values
										$("#vote_id").attr('value', vote['id']);
										$("#free_input").attr('value', vote['free_input']);
										// show delete button
										$("input[name='cmd[send_unvote]']").show();
									}
								}
							} else {
								$('.display-voter').replaceWith(data);
							}
						}).fail(function (jqXHR) {
							console.log(jqXHR);
						}).always(function () {
						});

				}
				// delete vote
				if (submit_name == 'cmd[send_unvote]') {
					$.post(url, {free_input: free_input, option_id: option_id, vote_id: vote_id, object_id: object_id, type: 'unvote'})
						.done(function (data) {
							if (data == '') {
								// set button style to default
								$('.btn-default').attr('class', 'btn btn-default btn-sm');
								// hide delete button
								$("input[name='cmd[send_unvote]']").hide();
								// reset input textfield
								$("#free_input").attr('value', "");
								$("#vote_id").attr('value', 0);
							} else {
								$('.display-voter').replaceWith(data);
							}

						}).fail(function (jqXHR) {
							console.log(jqXHR);
						}).always(function () {
						});
				}
				return false;
			});
		});
	}
}(jQuery));

$('#form_free_input').freeInputVote();

(function ($) {
	$.fn.freeInputMultiVote = function () {
		$(document).ready(function () {

			// prevent submitting over (mobile)keyboard
			$(document).keypress(function (event) {
				if (event.keyCode == 10 || event.keyCode == 13)
					event.preventDefault();
			});

			$('body').on('click', 'input[type=submit]', function () {
				$(this).attr('clicked', 'true');
			});

			$('#il_center_col').on('submit', '#form_free_input_multi', function (event) {
				event.preventDefault();

				// get values for POST request
				var option_id = $(".multi_input_line").attr('option_id');
				var object_id = $('#Voting-data').attr('object');
				var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/QuestionTypes/FreeInput/class.xlvoFreeInputSubmitEndpoint.php";

				// get name of submit button
				var button = $(this).find("input[type=submit][clicked=true]");
				button.attr('clicked', 'false');
				button.attr('disabled', 'disabled');
				$("input[name='cmd[unvote_all]']").attr('disabled', 'disabled');
				var submit_name = button.attr('name');

				// send vote
				if (submit_name == 'cmd[send_votes]') {

					// POST each vote
					var post_votes = function (votes) {

						$.post(url, {option_id: option_id, votes: votes, object_id: object_id, type: 'vote_multi'})
							.done(function (data) {

								$('.display-voter').replaceWith(data);

							}).fail(function (jqXHR) {
								console.log(jqXHR);
							}).always(function () {
							});

					};

					var post_free_input_multi = function () {

						var votes = [];

						$("input[name^='vote']").each(function () {
							var free_input = $(this).val();

							var name = $(this).attr('name');
							var voteId = new RegExp("\\[(\\d*)\\]").exec(name)[1];

							var vote = {vote_id: voteId, free_input: free_input};
							votes.push(vote);

						});
						JSON.stringify(votes);
						post_votes(votes);

					};

					post_free_input_multi();

				}
				if (submit_name == 'cmd[unvote_all]') {
					$.post(url, {option_id: option_id, object_id: object_id, type: 'delete_all'})
						.done(function (data) {

							$('.display-voter').replaceWith(data);

						}).fail(function (jqXHR) {
							console.log(jqXHR);
						}).always(function () {
						});
				}

				return false;
			});
		});
	}
}(jQuery));

$('#form_free_input_multi').freeInputMultiVote();