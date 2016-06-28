/**
 * Class xlvoVoter
 * @type {{}}
 */
var xlvoVoterConfig = {
    base_url: '',
    obj_id: '',
    player_id: '',
    cmd_voting_data: ''
};
var xlvoVoter = {
    init: function (json) {
        var config = JSON.parse(json);
        var replacer = new RegExp('amp;', 'g');
        config.base_url = config.base_url.replace(replacer, '');
        this.config = config;
        this.ready = true;
    },
    config: xlvoVoterConfig,
    status: -1,
    debug: false,
    active_voting_id: -1,
    run: function () {
        this.getVotingData();
    },
    getVotingData: function () {
        $.get(xlvoVoter.config.base_url, {cmd: xlvoVoter.config.cmd_voting_data})
            .done(function (data) {
                xlvoVoter.log(data);
                var voting_has_changed = (xlvoVoter.active_voting_id != data.active_voting_id);
                //xlvoVoter.log('voting: ' + voting_has_changed);
                var status_has_changed = (xlvoVoter.status != data.status);
                //xlvoVoter.log('status: ' + voting_has_changed);
                if (status_has_changed || voting_has_changed) {
                    xlvoVoter.replaceHTML();
                }
                xlvoVoter.active_voting_id = data.active_voting_id;
                xlvoVoter.status = data.status;
                setTimeout(xlvoVoter.getVotingData, 1000);
            }).fail(function () {
            setTimeout(xlvoVoter.getVotingData, 1000);
        });
    },
    replaceHTML: function () {
        xlvoVoter.log('replace');
        $.get(xlvoVoter.config.base_url, {cmd: 'getHTML'}).done(function (data) {
            $(xlvoVoter.config.player_id).html(data);
        });
    },
    /**
     * @param data
     */
    log: function (data) {
        if (xlvoVoter.debug) {
            console.log(data);
        }
    }
};