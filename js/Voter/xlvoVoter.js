/**
 * Class xlvoVoter
 * @type {{}}
 */
var xlvoVoter = {
    init: function (json) {
        
        var config = JSON.parse(json);
        var replacer = new RegExp('amp;', 'g');
        config.base_url = config.base_url.replace(replacer, '');
        this.config = config;
        this.ready = true;
        if (xlvoVoter.config.use_mathjax && !!MathJax) {
            MathJax.Hub.Config({
                "HTML-CSS": {scale: 80}
            });
        }
    },
    config: {
        base_url: '', // Base-URL for API-Calls
        cmd_voting_data: '', // loadVotingData
        lng: {
            player_seconds: 's'
        },
        debug: false,
        delay:1000
    },
    player: {
        frozen: true,
        active_voting_id: 0,
        status: -1,
        countdown: 0,
        has_countdown: false,
        countdown_classname: ''
    },
    delay: 1000,
    counter: 0,
    forced_update: 300,
    timeout: null,
    run: function () {
        this.loadVotingData();
        this.initElements();
    },
    initElements: function () {
        this.countdown_element = $('#xlvo_countdown');
        this.player_element = $('#xlvo_voter_player');
    },
    loadVotingData: function () {
        $.get(xlvoVoter.config.base_url, {cmd: 'getVotingData'})
            .done(function (data) {
                xlvoVoter.log(data);

                //kill timer if running
                if(xlvoVoter.interval)
                {
                    clearInterval(xlvoVoter.interval);
                    xlvoVoter.interval = null;
                }

                var voting_has_changed = (xlvoVoter.player.active_voting_id !== data.active_voting_id), // Voting has changed
                    status_has_changed = (xlvoVoter.player.status !== data.status), // Status of player has changed
                    forced_update = (xlvoVoter.counter > xlvoVoter.forced_update), // forced update
                    frozen_changed = (xlvoVoter.player.frozen !== data.frozen); // frozen status has changed

                xlvoVoter.player = data;
                if (status_has_changed || voting_has_changed || forced_update || frozen_changed) {
                    xlvoVoter.replaceHTML(xlvoVoter.handleCountdown);
                } else {
                    xlvoVoter.handleCountdown();
                }
                xlvoVoter.timeout = setTimeout(xlvoVoter.loadVotingData, xlvoVoter.config.delay);
                xlvoVoter.counter++;
            }).fail(function () {
            xlvoVoter.timeout = setTimeout(xlvoVoter.loadVotingData, xlvoVoter.config.delay);

        });
    },
    replaceHTML: function (success) {
        xlvoVoter.log('replace');
        success = success ? success : function () {
        };
        $.get(xlvoVoter.config.base_url, {cmd: 'getHTML'}).done(function (data) {
            xlvoVoter.player_element.replaceWith('<div id="xlvo_voter_player">' + data + '</div>');
            if (xlvoVoter.config.use_mathjax && !!MathJax) {
                MathJax.Hub.Queue(
                    ["Typeset", MathJax.Hub, 'xlvo_voter_player']
                );
            }
            xlvoVoter.counter = 0;
            xlvoVoter.player_element = $('#xlvo_voter_player');
            xlvoVoter.countdown_element = $('#xlvo_countdown');
            success();
        });
    },
    handleCountdown: function () {
        if (xlvoVoter.player.has_countdown) {
            xlvoVoter.log('has countdown: ' + (xlvoVoter.player.has_countdown ? 'yes, ' + xlvoVoter.player.countdown : 'no'));
            xlvoVoter.countdown_element.removeClass();
            xlvoVoter.countdown_element.text(xlvoVoter.player.countdown.toString() + ' ' + xlvoVoter.config.lng.player_seconds);
            xlvoVoter.countdown_element.show();
            xlvoVoter.countdown_element.addClass('label label-cd-' + xlvoVoter.player.countdown_classname);
            xlvoVoter.interval = setInterval(xlvoVoter.countDown, 1000);

        } else {
            xlvoVoter.countdown_element.removeClass();
            xlvoVoter.countdown_element.hide();
        }
    },
    countDown: function () {
        if (xlvoVoter.player.has_countdown) {
            xlvoVoter.player.countdown--;
            if(xlvoVoter.player.countdown > 0)
            {
                xlvoVoter.countdown_element.text((xlvoVoter.player.countdown).toString() + ' ' + xlvoVoter.config.lng.player_seconds);
            }
        }
    },

    /**
     * @param data
     */
    log: function (data) {
        if (xlvoVoter.config.debug) {
            console.log(data);
        }
    }
};