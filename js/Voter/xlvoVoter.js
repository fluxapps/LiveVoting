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
    active_voting_id: -1,
    run: function () {
        this.getVotingData();
    },
    getVotingData: function () {
        $.get(xlvoVoter.config.base_url, {cmd: xlvoVoter.config.cmd_voting_data})
            .done(function (data) {
                console.log(data);
                var voting_has_changed = (xlvoVoter.active_voting_id != data.active_voting_id);
                //console.log('voting: ' + voting_has_changed);
                var status_has_changed = (xlvoVoter.status != data.status);
                //console.log('status: ' + voting_has_changed);
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
    riddler: '+',
    replaceHTML: function () {
        console.log('replace');
        $.get(xlvoVoter.config.base_url, {cmd: 'getHTML'}).done(function (data) {
            $(xlvoVoter.config.player_id).html(data).parent();
        });
    }
};

