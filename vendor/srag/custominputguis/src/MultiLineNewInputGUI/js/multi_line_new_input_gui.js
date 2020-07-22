il.MultiLineNewInputGUI = {
    /**
     * @param {number} counter
     * @param {jQuery} el
     */
    add: function (counter, el) {
        var cloned_el = this.clone_template[counter].clone();

        this.init(counter, cloned_el);

        el.after(cloned_el);

        this.update(counter, el.parent());
    },

    /**
     * @param {number} counter
     */
    addFirstLine: function (counter) {
        this.add_first_line[counter].hide();

        var cloned_el = this.clone_template[counter].clone();

        this.init(counter, cloned_el);

        this.add_first_line[counter].parent().parent().children().eq(1).append(cloned_el);

        this.update(counter, this.add_first_line[counter].parent().parent().children().eq(1));
    },

    /**
     * @type {object}
     */
    add_first_line: {},

    /**
     * @type {object}
     */
    cached_options: [],

    /**
     * @param {number} counter
     * @param {jQuery} el
     * @param {string} type
     * @param {Object} options
     */
    cacheOptions(counter, el, type, options) {
        if (!Array.isArray(this.cached_options[counter])) {
            this.cached_options[counter] = [];
        }

        this.cached_options[counter].push({
            type: type,
            options: options
        });

        el.attr("data-cached_options_id", (this.cached_options[counter].length - 1));
    },

    /**
     * @type {object}
     */
    clone_template: {},

    /**
     * @param {number} counter
     * @param {jQuery} el
     */
    down: function (counter, el) {
        el.insertAfter(el.next());

        this.update(counter, el.parent());
    },

    /**
     * @param {number} counter
     * @param {jQuery} el
     * @param {boolean} add_first_line
     */
    init: function (counter, el, add_first_line) {
        $("span[data-action]", el).each(function (i, action_el) {
            action_el = $(action_el);

            action_el.off();

            action_el.on("click", this[action_el.data("action")].bind(this, counter, el))
        }.bind(this));

        if (!add_first_line) {
            $(".input-group.date:not([data-cached_options_id])", el).each(function (i2, el2) {
                el2 = $(el2);

                if (el2.data("DateTimePicker")) {
                    this.cacheOptions(counter, el2, "datetimepicker", el2.datetimepicker("options"));
                }
            }.bind(this));

            $("select[data-multiselectsearchnewinputgui]:not([data-cached_options_id])", el).each(function (i2, el2) {
                el2 = $(el2);

                const options = JSON.parse(atob(el2.data("multiselectsearchnewinputgui")));

                this.cacheOptions(counter, el2, "select2", options);
            }.bind(this));

            if (!this.clone_template[counter]) {
                this.clone_template[counter] = el.clone();

                $("[name]", this.clone_template[counter]).each(function (i2, el2) {
                    if (el2.type === "checkbox") {
                        el2.checked = false;
                    } else {
                        el2.value = "";
                    }
                });

                $(".alert", this.clone_template[counter]).remove();

                this.clone_template[counter].show();

                $("select[data-multiselectsearchnewinputgui]", this.clone_template[counter]).each(function (i2, el2) {
                    el2 = $(el2);

                    el2.html("");
                }.bind(this));

                if (el.parent().parent().data("remove_first_line")) {
                    this.remove(counter, el);
                }
            }
        } else {
            this.add_first_line[counter] = el;
        }
    },

    /**
     * @param {number} counter
     * @param {jQuery} el
     */
    remove: function (counter, el) {
        var parent = el.parent();

        if (!parent.parent().data("required") || parent.children().length > 1) {
            el.remove();

            this.update(counter, parent);
        }
    },

    /**
     * @param {number} counter
     * @param {jQuery} el
     */
    up: function (counter, el) {
        el.insertBefore(el.prev());

        this.update(counter, el.parent());
    },

    /**
     * @param {number} counter
     * @param {jQuery} el
     */
    update: function (counter, el) {
        $("span[data-action=up]", el).show();
        $("> div:first-of-type span[data-action=up]", el).hide();

        $("span[data-action=down]", el).show();
        $("> div:last-of-type span[data-action=down]", el).hide();

        for (const key of ["aria-controls", "aria-labelledby", "href", "id", "name"]) {
            el.children().each(function (i, el) {
                $("[" + key + "]", el).each(function (i2, el2) {
                    for (const [char_open, char_close] of [["[", "]["], ["__", "__"]]) {
                        el2.attributes[key].value = el2.attributes[key].value.replace(new RegExp(char_open.replace(/./g, "\\$&") + "[0-9]+" + char_close.replace(/./g, "\\$&")), char_open + i + char_close);
                    }
                }.bind(this));
            }.bind(this));
        }

        if (el.parent().data("required")) {
            if (el.children().length < 2) {
                $("span[data-action=remove]", el).hide();
            } else {
                $("span[data-action=remove]", el).show();
            }
        } else {
            $("span[data-action=remove]", el).show();

            if (el.children().length === 0) {
                this.add_first_line[counter].show();
            }
        }

        $("[data-cached_options_id]", el).each(function (i2, el2) {
            el2 = $(el2);

            const options = this.cached_options[counter][el2.attr("data-cached_options_id")];
            if (!options) {
                return;
            }
            switch (options.type) {
                case "datetimepicker":
                    if (el2.data("DateTimePicker")) {
                        el2.datetimepicker("destroy");
                    }

                    el2.prop("id", "");

                    el2.datetimepicker(options.options);
                    break;

                case "select2":
                    if (el2.data("select2")) {
                        el2.select2("destroy");
                    }

                    el2.next(".select2").remove();

                    el2.removeAttr("class");
                    el2.removeAttr("data-select2-id");
                    el2.removeAttr("aria-hidden");
                    el2.removeAttr("tabindex");

                    el2.select2(options.options);
                    break;

                default:
                    break;
            }
        }.bind(this));
    }
};
