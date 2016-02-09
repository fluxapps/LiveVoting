/**
 * Class xlvoPlayer
 * @type {{}}
 */
var xlvoPlayer = {
    init: function (json) {
        var config = JSON.parse(json);
        var replacer = new RegExp('amp;', 'g');
        config.base_url = config.base_url.replace(replacer, '');
        this.config = config;
        this.ready = true;
    },
    delay: 1000,
    forced_update_interval: 3,
    config: {
        base_url: '',
        voter_count_element_id: '',
        lng: {
            player_voters_online: 'Online'
        },
        status_running: -1
    },
    player: {
        status: -1,
        active_voting_id: -1,
        show_results: false,
        frozen: true,
        last_update: 0,
        counter: 0,
        is_first: true,
        is_last: false,
        attendees: 0
    },
    run: function () {
        this.registerElements();
        this.getPlayerData();
    },
    handleFullScreen: function () {
        this.btn_close_fullscreen.parent().hide();
        var jq_target = $('div.ilTabsContentOuter');
        var target = jq_target[0];
        var self = this;
        this.btn_start_fullscreen.click(function () {
            if (screenfull.enabled) {
                screenfull.request(target);
            }
        });
        this.btn_close_fullscreen.click(function () {
            if (screenfull.enabled) {
                screenfull.exit(target);
            }
        });

        if (screenfull.enabled) {
            document.addEventListener(screenfull.raw.fullscreenchange, function () {
                if (!screenfull.isFullscreen) {
                    jq_target.removeClass('xlvo-fullscreen');
                    self.btn_start_fullscreen.parent().show();
                    self.btn_close_fullscreen.parent().hide();
                } else {
                    jq_target.addClass('xlvo-fullscreen');
                    self.btn_start_fullscreen.parent().hide();
                    self.btn_close_fullscreen.parent().show();
                }
            });
        }
    }, registerElements: function () {
        this.btn_freeze = $('#btn-freeze');
        this.btn_previous = $('#btn-previous');
        this.btn_next = $('#btn-next');
        this.btn_unfreeze = $('#btn-unfreeze');
        this.btn_unfreeze.parent().hide();
        this.btn_reset = $('#btn-reset');
        this.btn_reset.parent().attr('disabled', true);
        this.btn_hide_results = $('#btn-hide-results');
        this.btn_show_results = $('#btn-show-results');
        this.btn_show_results.parent().hide();
        this.btn_start_fullscreen = $('#btn-start-fullscreen');
        this.btn_close_fullscreen = $('#btn-close-fullscreen');
        this.div_display_results = $('#xlvo-display-results');

        this.btn_freeze.click(function () {
            xlvoPlayer.callPlayer('freeze');
            return false;
        });

        this.btn_unfreeze.click(function () {
            xlvoPlayer.callPlayer('unfreeze');
            return false;
        });

        this.btn_hide_results.click(function () {
            xlvoPlayer.callPlayer('hide');
            return false;
        });

        this.btn_show_results.click(function () {
            xlvoPlayer.callPlayer('show');
            return false;
        });

        this.btn_reset.click(function () {
            xlvoPlayer.callPlayer('reset');
            return false;
        });
        this.btn_next.click(function () {
            xlvoPlayer.callPlayer('next');
            return false;
        });
        this.btn_previous.click(function () {
            xlvoPlayer.callPlayer('previous');
            return false;
        });
        this.handleFullScreen();
    },
    initElements: function () {
        if (this.player.frozen) {
            this.btn_freeze.parent().hide();
            this.btn_unfreeze.parent().show();
            this.btn_reset.removeAttr('disabled');
        } else {
            this.btn_unfreeze.parent().hide();
            this.btn_freeze.parent().show();
            this.btn_reset.attr('disabled', 'disabled');
        }
        if (this.player.show_results) {
            this.btn_hide_results.parent().show();
            this.btn_show_results.parent().hide();
            this.div_display_results.show();
        } else {
            this.btn_hide_results.parent().hide();
            this.btn_show_results.parent().show();
            this.div_display_results.hide();
        }
        if (this.player.is_last) {
            this.btn_next.attr('disabled', 'disabled');
            this.btn_previous.removeAttr('disabled');
        }
        if (this.player.is_first) {
            this.btn_previous.attr('disabled', 'disabled');
            this.btn_next.removeAttr('disabled');
        }
        if (!this.player.is_last && !this.player.is_first) {
            this.btn_next.removeAttr('disabled');
            this.btn_previous.removeAttr('disabled');
        }
        if (this.player.is_last && this.player.is_first) {
            this.btn_next.attr('disabled', 'disabled');
            this.btn_previous.attr('disabled', 'disabled');
        }
        var attendees = document.getElementById('xlvo-attendees');
        attendees.innerHTML = (this.player.attendees + ' Online');
    },
    getPlayerData: function () {
        $.get(xlvoPlayer.config.base_url, {cmd: 'getPlayerData'}).done(function (data) {
            console.log(data);
            xlvoPlayer.counter++;
            if ((xlvoPlayer.counter > xlvoPlayer.forced_update_interval) || (data.player.last_update != xlvoPlayer.player.last_update) || (data.player.show_results != xlvoPlayer.player.show_results) || (data.player.status != xlvoPlayer.player.status) || (data.player.active_voting_id != xlvoPlayer.player.active_voting_id)) {
                $('#xlvo-display-player').html(data.player_html);
                xlvoPlayer.counter = 0;
            }

            xlvoPlayer.player = data.player;
            xlvoPlayer.initElements();
            setTimeout(xlvoPlayer.getPlayerData, xlvoPlayer.delay);
        });
    },
    /**
     * @param cmd
     * @param success
     * @param fail
     * @param voting_id
     */
    callPlayer: function (cmd, success, fail, voting_id) {
        var success = success ? success : function () {
        }, fail = fail ? fail : function () {
        }, voting_id = voting_id ? voting_id : null;

        $.post(xlvoPlayer.config.base_url + '&cmd=apiCall', {call: cmd, xvi: voting_id}).done(function (data) {
            if (data) {
                success();
            } else {
                fail();
            }
        });
    },
    /**
     *
     * @param id
     */
    open: function (id) {
        this.callPlayer('open', false, false, id);
        return false;
    },
    /**
     * unused
     */
    updateAttendees: function () {
        $.get(this.base_url, {cmd: "getAttendees"})
            .done(function (data) {
                $('#xlvo-attendees').html(data + ' Online');
            });
        setTimeout(xlvoPlayer.updateAttendees, 1500);
    }
};
