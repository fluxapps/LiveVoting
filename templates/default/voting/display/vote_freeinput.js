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
				var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/freeInput/class.xlvoFreeInputSubmitEndpoint.php";

				// get name of submit button
				var button = $(this).find("input[type=submit][clicked=true]");
				button.attr('clicked', 'false');
				var submit_name = button.attr('name');

				// send vote
				if (submit_name == 'cmd[send_vote]') {
					$.post(url, {free_input: free_input, option_id: option_id, vote_id: vote_id, type: 'vote'})
						.done(function (data) {
							console.log(data);
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
						}).fail(function (jqXHR) {
							console.log(jqXHR);
						}).always(function () {
						});
				}
				// delete vote
				if (submit_name == 'cmd[send_unvote]') {
					$.post(url, {free_input: free_input, option_id: option_id, vote_id: vote_id, type: 'unvote'})
						.done(function (data) {
							console.log(data);
							// set button style to default
							$('.btn-default').attr('class', 'btn btn-default btn-sm');
							// hide delete button
							$("input[name='cmd[send_unvote]']").hide();
							// reset input textfield
							$("#free_input").attr('value', "");
							$("#vote_id").attr('value', 0);
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
				var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/freeInput/class.xlvoFreeInputSubmitEndpoint.php";

				// get name of submit button
				var button = $(this).find("input[type=submit][clicked=true]");
				button.attr('clicked', 'false');
				button.attr('disabled', 'disabled');
				var submit_name = button.attr('name');

				// send vote
				if (submit_name == 'cmd[send_votes]') {

					// POST each vote
					var post_votes = function () {

						$("input[name^='vote']").each(function (i) {
							var free_input = $(this).val();
							$.post(url, {free_input: free_input, option_id: option_id, type: 'vote'})
								.done(function (data) {
									console.log('done');
								}).fail(function (jqXHR) {
									console.log(jqXHR);
								}).always(function () {
								});
						});
					};

					// delete all existing votes
					var delete_votes = function () {
						$.post(url, {option_id: option_id, type: 'delete_all'})
							.done(function (data) {
								// save the new votes
								post_votes();
								button.attr('disabled', false);
							}).fail(function (jqXHR) {
								console.log(jqXHR);
							}).always(function () {
							});
					};

					// call function
					delete_votes();

					// set buttons
					$("input[name='cmd[unvote_all]']").show();
					$('.btn.btn-default.btn-sm').attr('class', 'btn btn-default btn-sm');
				}
				if (submit_name == 'cmd[unvote_all]') {
					$.post(url, {option_id: option_id, type: 'delete_all'})
						.done(function (data) {
							// remove all but one input field. child 1 = hidden input; child 2 = first input field
							$("#vote_multi_line_input").find('*').not(":nth-child(1)").not(":nth-child(2)").remove();
							// reset value in first input field
							$("#vote_multi_line_input").find("input[name^='vote']").val("");
							// set buttons
							$("input[name='cmd[unvote_all]']").hide();
							$('.btn.btn-default.btn-sm').attr('class', 'btn btn-default btn-sm');
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