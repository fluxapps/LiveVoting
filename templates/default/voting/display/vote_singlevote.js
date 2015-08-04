$(document).ready(function () {

	$(".vote_form").each(function (index, element) {
		$(this).submit(function (event) {

			var option_id = $(this).find("input[name='option_id']").val();
			var url = "./Customizing/global/plugins/Services/Repository/RepositoryObject/LiveVoting/classes/voting/singleVote/singleVoteEndpoint.php";

			//console.log(option_id);

			$.post(url, {option_id: option_id})
				.done(function (data) {
					if(data == 1) {
						$('#xlvo_bar_' + option_id).attr('class', 'btn btn-default btn-lg btn-block active');
					} else {
						$('#xlvo_bar_' + option_id).attr('class', 'btn btn-default btn-lg btn-block');
					}
					alert("Data Loaded: " + data);
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
