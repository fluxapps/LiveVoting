(function ($) {

	$.fn.extend({
		multi_line_input: function (element_config, options) {

			var settings = $.extend({
				unique_values: false
			}, options);

			var element_config = element_config;
			var element = this;
			var counter = 0;
            var clone_line = $(this).find('.multi_input_line').first();
            var empty_id = "empty";

            var setup_clone_line = function(clone_line) {
                clone_line.hide();
                clone_line.removeClass('multi_input_line');

                clone_line.find("textarea[name^='" + element.attr('id') + "'], input[name^='" + element.attr('id') + "'], select[name^='" + element.attr('id') + "']").each(function () {
                    var name = $(this).attr('name');
                    var id = element.attr('id');
                    var regex = new RegExp('^' + id + '\[[0-9]+\](.*)$', 'g');
                    var matches = regex.exec(name);
                    name = empty_id + '[' + counter + ']' + matches[1];
                    $(this).attr('name', name);
                });
                //counter++;
            };

            setup_clone_line(clone_line);

			var setup_line = function (line, init) {
				var init = init || false;
				var $line = line;

				$(line).find('.add_button').on('click', function (e) {
					var new_line = clone_line.clone();
                    new_line.show();
                    $(new_line).addClass("multi_input_line");

					setup_line(new_line);
					$(element).append(new_line);
					$(element).change();
                    $(document).trigger('multi_line_add_button', [$line, new_line]);
					return false;
				});

				$(line).find('.remove_button').on('click', function (e) {
					$line.remove();
					$(element).change();
                    $(document).trigger('multi_line_remove_button', $line);
					return false;
				});

				if (!init) {
                    console.log($line);
					$line.find("textarea[name^='" + empty_id + "'], input[name^='" + empty_id + "'], select[name^='" + empty_id + "']").each(function () {
                        var name = $(this).attr('name');
						var id = element.attr('id');
                        $(this).val('');
                        var regex = new RegExp('^' + empty_id + '\[[0-9]+\](.*)$', 'g');
                        var matches = regex.exec(name);
                        name = id + '[' + counter + ']' + matches[1];
                        console.log(name);
                        console.log(matches);
						$(this).attr('name', name);
					});
				}
				counter++;
			};

			// hide/show delete icons
			$(element).on('change', function (e) {
				var remove_buttons = $(element).find('.multi_input_line .remove_button');

				if (remove_buttons.length > 1) {
					remove_buttons.show().first().hide();
				} else {
					remove_buttons.hide();
				}
			});

			$(this).find('.multi_input_line').each(function () {
				setup_line($(this), true);
			});
			$(element).change();

			return element;
		}
	});

}(jQuery));