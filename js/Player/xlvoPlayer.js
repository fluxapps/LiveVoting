/**
 * Class xlvoPlayer
 * @type {{}}
 */
var xlvoPlayer = {
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
    voter_count_element_id: '',
    lng: {
        player_voters_online: 'Online'
    },
    run: function () {

    },
    updateVoterCounter: function () {
        $.get(this.base_url, {cmd: "getVoterCounterData"})
            .done(function (data) {
                $('#' + xlvoPlayer.voter_count_element_id).html(data + ' ' + xlvoPlayer.lng['player_voters_online']);
            });
        setTimeout(xlvoPlayer.updateVoterCounter, 1500);
    }
};
