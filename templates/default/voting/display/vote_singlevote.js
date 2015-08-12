(function ($) {
	$.fn.singleVote = function () {
		$(document).ready(function () {

			$(document).keypress(function(event){
				if (event.keyCode == 10 || event.keyCode == 13)
					event.preventDefault();

			});

			$(".vote_form").each(function (index, element) {
				$(this).submit(function (event) {

					var option_id = $(this).find("input[name='option_id']").val();
					var vote_id = $(this).find("input[name='vote_id']").val();
					var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/singleVote/class.xlvoSingleVoteSubmitEndpoint.php";

					$.post(url, {option_id: option_id, vote_id: vote_id})
						.done(function (data) {

							$('.btn-default').attr('class', 'btn btn-default btn-lg btn-block');
							$("input.vote_form_input_vote").attr('value', 0).attr('id', 'xlvo_vote_id_0');

							for (var key in data) {
								var vote = data[key];
								console.log(vote['id'])
								if (vote['status'] == 1) {
									$("button[data-id='" + vote['option_id'] + "']").attr('class', 'btn btn-default btn-lg btn-block active');
									$("input[option-id='" + vote['option_id'] + "']").attr('value', vote['id']).attr('id', 'xlvo_vote_id_' + vote['id']);
								}
							}
							//alert("Data Loaded: " + data);
						}).fail(function () {
							alert("error");
						})
						.always(function () {
							//alert("finished");
						});

					return false;
				});
			});
		});
	}
}(jQuery));

$(".vote_form").singleVote();
