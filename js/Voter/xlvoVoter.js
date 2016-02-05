/**
 * Class xlvoVoter
 * @type {{}}
 */
var xlvoVoter = {
    init: function (json) {
        var self = this;
        var input = JSON.parse(json);
        for (var attrname in input) {
            if (attrname == 'base_url') {
                var replacer = new RegExp('amp;', 'g');
                input[attrname] = input[attrname].replace(replacer, '');
            }
            self[attrname] = input[attrname];
        }
        this.ready = true;
        this.run();
    },
    base_url: '',
    run: function () {

    }
};
