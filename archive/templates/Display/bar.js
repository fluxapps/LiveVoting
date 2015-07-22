/**
 * xlvoBar
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */

(function ($, window, document, undefined) {

	var pluginName = "xlvoBar",
		defaults = {
			ajaxLink: ""
		};

	function Plugin(element, options) {
		this.element = element;
		this.options = $.extend({}, defaults, options);
		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}

	Plugin.prototype = {
		init: function () {
			// Alles einrichten
			alert('o');
		}
	};

	$.fn[pluginName] = function (options) {
		return this.each(function () {
			if (!$.data(this, "plugin_" + pluginName)) {
				$.data(this, "plugin_" + pluginName,
					new Plugin(this, options));
			}
		});
	};

})(jQuery, window, document);