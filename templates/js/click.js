var filetypes = /\.(zip|exe|dmg|pdf|doc.*|xls.*|ppt.*|mp3|txt|rar|wma|mov|avi|wmv|flv|wav)$/i;
var baseHref = '';
if (jQuery('base').attr('href') != undefined) {
	baseHref = jQuery('base').attr('href');
}
var hrefRedirect = '';


var clickCheckerLoaded = true;
$(document).ready(function () {
	$('body').prepend('<div id=\'fsx_dimmer\'></div>');

	/**
	 * redirect to URL
	 *
	 * @param url
	 */
	var simpleRedirect = function (url) {
		document.location = url;
	};

	/**
	 * Dim Screen
	 *
	 * @param delay
	 */
	$.fn.clickDimmer = function (delay) {
		$(this).fadeTo(0, 0.6);
		$(this).delay(delay).fadeTo(100);

	}


	/**
	 * Links
	 */
	$('a').each(function () {
		$(this).click(function (event) {
			var no_file = (event.target.href.indexOf('download.html') == -1);
			var no_file_n = (event.target.href.indexOf('_download&') == -1);
			var no_file_n2 = (event.target.href.indexOf('sendFile') == -1);
			var inner_domain = event.target.href.indexOf(document.domain) > 0;
			var no_action = event.target.href.substr(event.target.href.length - 1) != '#';
			var no_new_site = event.target.target != '_blank';

			if (inner_domain && no_action && no_new_site && no_file && no_file_n && no_file_n2) {
				$('#fsx_dimmer').clickDimmer(5000);
			}
			if ($(this).attr('class') == 'ilEditSubmit') {
				$('#fsx_dimmer').clickDimmer(1000);
			}

			setTimeout(function () {
				simpleRedirect(event.target.href);
			}, 0);

		});
	});

	/**
	 * Forms
	 */
	$('form').each(function () {
		$(this).submit(function () {
			if (typeof window.formCheckerLoaded == 'undefined') {
				$('#fsx_dimmer').clickDimmer(5000);

			} else {
				if ($(this).checkIliasForm()) {
					$('#fsx_dimmer').clickDimmer(5000);
				}
			}
		});
	});
});


$(document).keyup(function (e) {
	if (e.keyCode == 27) {
		$('#fsx_dimmer').hide();
	}
});