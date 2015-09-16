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
				$("input[name='cmd[unvote_all]']").attr('disabled', 'disabled');
				var submit_name = button.attr('name');

				// send vote
				if (submit_name == 'cmd[send_votes]') {

					// POST each vote
					var post_votes = function (votes) {

						$.post(url, {option_id: option_id, votes: votes, type: 'vote_multi'})
							.done(function (data) {

								// remove all but one input field. child 1 = hidden input; child 2 = first input field
								$("#vote_multi_line_input").find('*').not(":nth-child(1)").not(":nth-child(2)").remove();
								// set buttons
								button.attr('disabled', false);
								$("input[name='cmd[unvote_all]']").attr('disabled', false).show();
								$('.btn.btn-default.btn-sm').attr('class', 'btn btn-default btn-sm');

								$('.free-input-form').replaceWith(data);

								//var is_first = true;
								//for (var key in data) {
								//	var vote = data[key];
								//
								//	if (is_first == true) {
								//
								//		var inputField = $("input[name^='vote']");
								//
								//		var name = inputField.attr('name');
								//		var regexName = new RegExp("\\[(\\d*)\\]");
								//		var newName = name.replace(regexName, ('[' + vote['id'] + ']'));
								//		inputField.attr('name', newName);
								//
								//		var mliId = inputField.attr('id');
								//		var regexId = new RegExp("\\__\\d*\\__");
								//		var newMliId = mliId.replace(regexId, '__' + vote['id'] + '__');
								//		inputField.attr('id', newMliId);
								//
								//		inputField.attr('value', vote['free_input']);
								//
								//		is_first = false;
								//	} else {
								//		//$('#vote_multi_line_input').append(
								//		//	'<div class="multi_input_line">'
								//		//	+ '<div class="input">'
								//		//	+ '<input class="form-control" id="vote_multi_line_input__' + vote['id'] + '503____free_input__'
								//		//	+ 'maxlength="200" name="vote_multi_line_input[' + vote['id'] + '[free_input]" value="' + vote['free_input'] + '" type="text">'
								//		//	+ '</div>'
								//		//	+ '<div class="multi_icons_wrapper">'
								//		//	+ '<a href="#" class="btn btn-default multi_icon add_button"><span class="sr-only"></span><span class="glyphicon glyphicon-plus"></span></a>'
								//		//	+ '<a href="#" class="btn btn-default multi_icon remove_button"><span class="sr-only"></span><span class="glyphicon glyphicon-minus"></span></a>'
								//		//	+ '</div>'
								//		//	+ '</div>'
								//		//);
								//		$("#vote_multi_line_input").multi_line_input({"free_input": []}, data);
								//	}
								//}


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
					$.post(url, {option_id: option_id, type: 'delete_all'})
						.done(function () {
							// remove all but one input field. child 1 = hidden input; child 2 = first input field
							$("#vote_multi_line_input").find('*').not(":nth-child(1)").not(":nth-child(2)").remove();

							// first input field
							var inputField = $("input[name^='vote']");

							// reset name
							var name = inputField.attr('name');
							var regexName = new RegExp("\\[(\\d*)\\]");
							var newName = name.replace(regexName, ('[' + 0 + ']'));
							inputField.attr('name', newName);

							// reset id
							var mliId = inputField.attr('id');
							var regexId = new RegExp("\\__\\d*\\__");
							var newMliId = mliId.replace(regexId, '__' + 0 + '__');
							inputField.attr('id', newMliId);

							// reset value
							inputField.attr('value', '');

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