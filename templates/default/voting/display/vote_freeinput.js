(function ($) {
	$.fn.freeInputVote = function () {
		$(document).ready(function () {

			$(document).keypress(function(event){
				if (event.keyCode == 10 || event.keyCode == 13)
					event.preventDefault();

			});

			var vote_id = $('#vote_id').val();
			if (vote_id == 0) {
				$("input[name='cmd[send_unvote]']").hide();
			}

			$('#form_').submit(function (event) {

				var free_input = $('#free_input').val();
				var option_id = $('#option_id').val();
				var vote_id = $('#vote_id').val();
				var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/freeInput/class.xlvoFreeInputSubmitEndpoint.php";

				var submit_name = $(this).find("input[type=submit]:focus").attr('name');

				if (submit_name == 'cmd[send_vote]') {
					$.post(url, {free_input: free_input, option_id: option_id, vote_id: vote_id, type: 'vote'})
						.done(function (data) {
							$('.btn-default').attr('class', 'btn btn-default btn-sm');
							for (var key in data) {
								var vote = data[key];
								if (vote['status'] == 1) {
									$("#vote_id").attr('value', vote['id']);
									$("#free_input").attr('value', vote['free_input']);
									$("input[name='cmd[send_unvote]']").show();
								}
							}
							//alert("Data Loaded: " + data);
						}).fail(function (jqXHR) {
							console.log(jqXHR);
							//alert("error");
						})
						.always(function () {
							//alert("finished");
						});
				}
				if (submit_name == 'cmd[send_unvote]') {
					$.post(url, {free_input: free_input, option_id: option_id, vote_id: vote_id, type: 'unvote'})
						.done(function (data) {
							$('.btn-default').attr('class', 'btn btn-default btn-sm');
							$("input[name='cmd[send_unvote]']").hide();
							$("#free_input").attr('value', "");
							$("#vote_id").attr('value', 0);
							//alert("Data Loaded: " + data);
						}).fail(function (jqXHR) {
							console.log(jqXHR);
							//alert("error");
						})
						.always(function () {
							//alert("finished");
						});
				}

				return false;
			});
		});
	}
}(jQuery));

$('#form_').freeInputVote();

(function ($) {
	$.fn.freeInputMultiVote = function () {
		$(document).ready(function () {

			$(document).keypress(function(event){
				if (event.keyCode == 10 || event.keyCode == 13)
					event.preventDefault();

			});

			if($("input[name='vote_multi_line_input[0][free_input]']").length) {
				$("input[name='cmd[unvote_all]']").hide();
			}

			$('#form_').submit(function () {

				var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/freeInput/class.xlvoFreeInputSubmitEndpoint.php";
				var submit_name = $(this).find("input[type=submit]:focus").attr('name');
				var option_id = $(".multi_input_line").attr('option_id');

				if (submit_name == 'cmd[send_votes]') {

					$.post(url, {option_id: option_id, type: 'delete_all'})
						.done(function (data) {
							//alert("Data Loaded: " + data);
						}).fail(function (jqXHR) {
							console.log(jqXHR);
							//alert("error");
						})
						.always(function () {
							//alert("finished");
						});

					$("input[name^='vote']").each(function (i) {
						var free_input = $(this).val();
						$.post(url, {free_input: free_input, option_id: option_id, type: 'vote'})
							.done(function (data) {
								//alert("Data Loaded: " + data);
							}).fail(function (jqXHR) {
								//alert("error");
								console.log(jqXHR);
							})
							.always(function () {
								//alert("finished");
							});
					});
					// set buttons
					$("input[name='cmd[unvote_all]']").show();
					$('.btn-default').attr('class', 'btn btn-default btn-sm');
				}
				if (submit_name == 'cmd[unvote_all]') {
					$.post(url, {option_id: option_id, type: 'delete_all'})
						.done(function (data) {
							// remove all but one input field. child 1 = hidden input; child 2 = first input field
							$("#vote_multi_line_input").find('*').not(":nth-child(1)").not(":nth-child(2)").remove();
							// reset value in first input field
							$("#vote_multi_line_input").find("input[name^='vote']").val("");
							$("input[name='cmd[unvote_all]']").hide();
							$('.btn-default').attr('class', 'btn btn-default btn-sm');
							//alert("Data Loaded: " + data);
						}).fail(function (jqXHR) {
							console.log(jqXHR);
							alert("error");
						})
						.always(function () {
							//alert("finished");
						});
				}

				return false;
			});
		});
	}
}(jQuery));

$('#form_').freeInputMultiVote();
