(function ($) {
	$.fn.singleVote = function () {
		$(document).ready(function () {

			// prevent submitting over (mobile)keyboard
			$(document).keypress(function (event) {
				if (event.keyCode == 10 || event.keyCode == 13)
					event.preventDefault();

			});

			$('#il_center_col').on('submit', '.vote_form', function (event) {
				event.preventDefault();

				// get values for POST request
				var option_id = $(this).find("input[name='option_id']").val();
				var vote_id = $(this).find("input[name='vote_id']").val();
				var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/singleVote/class.xlvoSingleVoteSubmitEndpoint.php";

				$.post(url, {option_id: option_id, vote_id: vote_id})
					.done(function (data) {

						// set all buttons to default
						$('.btn-default').attr('class', 'btn btn-default btn-lg btn-block');
						$("input.vote_form_input_vote").attr('value', 0).attr('id', 'xlvo_vote_id_0');

						for (var key in data) {
							var vote = data[key];
							if (vote['status'] == 1) {
								// set buttons values
								$("button[data-id='" + vote['option_id'] + "']").attr('class', 'btn btn-default btn-lg btn-block active');
								$("input[option-id='" + vote['option_id'] + "']").attr('value', vote['id']).attr('id', 'xlvo_vote_id_' + vote['id']);
							}
						}
					}).fail(function (jqXHR) {
						console.log(jqXHR);
					})
					.always(function () {
					});

				return false;
			});
		});
	}
}(jQuery));

$(".vote_form").singleVote();
