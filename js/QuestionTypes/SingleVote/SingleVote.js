/**
 * Class xlvoSingleVote
 * @type {{}}
 */
var xlvoSingleVote = {
    init: function (json) {
        var config = JSON.parse(json);
        var replacer = new RegExp('amp;', 'g');
        config.base_url = config.base_url.replace(replacer, '');
        this.config = config;
        this.ready = true;
    },
    config: {},
    base_url: '',
    run: function () {
    },
    /**
     * @param button_id
     * @param button_data
     */
    handleButtonPress: function (button_id, button_data) {

    }
};
