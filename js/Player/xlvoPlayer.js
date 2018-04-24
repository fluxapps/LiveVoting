/**
 * Class xlvoPlayer
 * @type {{}}
 */
var xlvoPlayer = {
    init: function (json) {
        var config = JSON.parse(json),
            replacer = new RegExp('amp;', 'g');
        config.base_url = config.base_url.replace(replacer, '');
        this.config = config;
        this.ready = true;
        xlvoPlayer.log(this.config);

        //set the height for safari
        var node = $('#xlvo-display-player').children();
        $('#xlvo-display-player').css('height', node.css('height'));

        if (xlvoPlayer.config.use_mathjax && !!MathJax) {
            MathJax.Hub.Config(xlvoPlayer.mathjax_config);
        }
    },
    mathjax_config: {
        "HTML-CSS": {scale: 80}
    },
    buttons_handled: false,
    toolbar_loaded: false,
    delay: 1000,
    counter: 1,
    timeout: null,
    request_pending: false,
    forced_update_interval: 10,
    countdown_running: false,
    config: {
        base_url: '',
        voter_count_element_id: '',
        lng: {
            player_voters_online: 'Online',
            voting_confirm_reset: 'Reset?'
        },
        status_running: -1,
        use_mathjax: false,
        debug: false
    },
    player: {
        is_first: true,
        is_last: false,
        status: -1,
        active_voting_id: -1,
        show_results: false,
        frozen: true,
        votes: 0,
        last_update: 0,
        attendees: 0,
        countdown: 0,
        has_countdown: false
    },
    run: function () {
        xlvoPlayer.log('running player');
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

                //set the height for safari
                var node = $('#xlvo-display-player').children();
                $('#xlvo-display-player').css('height', node.css('height'));

            });
        }

    }, registerElements: function () {
        $(document).keydown(function (e) {
            switch (e.which) {
                case xlvoPlayer.config.keyboard.toggle_results:
                    xlvoPlayer.callPlayer('toggle_results');
                    break;
                case xlvoPlayer.config.keyboard.toggle_freeze:
                case 66:
                    xlvoPlayer.callPlayer('toggle_freeze');
                    break;
                case 33:
                case xlvoPlayer.config.keyboard.previous:
                    xlvoPlayer.callPlayer('previous');
                    break;
                case 34:
                case xlvoPlayer.config.keyboard.next:
                    xlvoPlayer.callPlayer('next');
                    break;
                default:
                    return;
            }
            e.preventDefault();
        });

        this.btn_freeze = $('#btn-freeze');
        this.btn_previous = $('#btn-previous');
        this.btn_next = $('#btn-next');
        this.btn_unfreeze = $('#btn-unfreeze');
        this.btn_unfreeze.closest('.btn-group').hide();
        this.btn_reset = $('#btn-reset');
        this.btn_terminate = $('#btn-terminate');
        this.btn_terminate.parent().hide();
        this.btn_reset.parent().attr('disabled', true);
        this.btn_hide_results = $('#btn-hide-results');
        this.btn_show_results = $('#btn-show-results');
        this.btn_toggle_pull = $('#btn-toggle-pull');
        this.btn_show_results.parent().hide();
        this.btn_start_fullscreen = $('#btn-start-fullscreen');
        this.btn_close_fullscreen = $('#btn-close-fullscreen');
        this.div_display_results = $('#xlvo-display-results');
        this.toolbar = $('nav.ilToolbar');
        this.toolbar.prepend('<div id="xlvo_player_loading"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');
        this.toolbar_loader = $('#xlvo_player_loading');
        this.toolbar_loader.show();
        this.btn_freeze.click(function () {
            xlvoPlayer.callPlayer('toggle_freeze');
            return false;
        });

        this.btn_unfreeze.click(function () {
            xlvoPlayer.callPlayer('toggle_freeze');
            return false;
        });

        this.btn_hide_results.click(function () {
            xlvoPlayer.callPlayer('toggle_results');
            return false;
        });

        this.btn_show_results.click(function () {
            xlvoPlayer.callPlayer('toggle_results');
            return false;
        });

        this.btn_reset.click(function () {
            if (window.confirm(xlvoPlayer.config.lng.voting_confirm_reset)) {
                xlvoPlayer.callPlayer('reset');
            }
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
        if (this.btn_toggle_pull) {
            this.btn_toggle_pull.click(function () {
                xlvoPlayer.togglePull();
            });
        }
        this.handleFullScreen();
    },
    initElements: function () {
        if (this.player.frozen) {
            this.btn_freeze.parent().hide();

            this.btn_unfreeze.closest('.btn-group').show();
            if (this.player.votes > 0) {
                this.btn_reset.removeAttr('disabled');
            } else {
                this.btn_reset.attr('disabled', 'disabled');
            }
        } else {
            this.btn_unfreeze.closest('.btn-group').hide();
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
        if (this.player.attendees > 0) {
            var attendees = document.getElementById('xlvo-attendees');
            attendees.innerHTML = (this.player.attendees + ' Online');
        }


    },
    startRequest: function () {
        xlvoPlayer.request_pending = true;
    },
    endRequest: function () {
        xlvoPlayer.request_pending = false;
    },
    isRequestPending: function () {
        return xlvoPlayer.request_pending;
    },
    clearTimeout: function () {
        if (xlvoPlayer.timeout) {
            xlvoPlayer.log('clear timeout');
            clearTimeout(xlvoPlayer.timeout);
        }
    },
    getPlayerData: function () {
        if (xlvoPlayer.isRequestPending()) {
            xlvoPlayer.log('Pause getPlayerData due to running POST');
            return;
        }
        xlvoPlayer.startRequest();
        $.get(xlvoPlayer.config.base_url, {cmd: 'getPlayerData'}).done(function (data) {
            xlvoPlayer.counter++;
            if ((xlvoPlayer.counter > xlvoPlayer.forced_update_interval) // Forced update of HTML
                || (data.player.last_update !== xlvoPlayer.player.last_update) // Player is out of sync
                || (data.player.show_results !== xlvoPlayer.player.show_results) // Show Results has changed
                || (data.player.status !== xlvoPlayer.player.status) // player status has changed
                || (data.player.active_voting_id !== xlvoPlayer.player.active_voting_id) //Voting has changed
                || xlvoPlayer.player.has_countdown) // countdown is running
            {

                var playerHtml = data.player_html;

                //create new jquery node
                var node = $(playerHtml);

                //get list of old childs
                var oldNode = $('#xlvo-display-player').children();

                //append new child
                $('#xlvo-display-player').append(node);

                //set height because some browser ignore the height of the absolute content of the player
                $('#xlvo-display-player').css('height', node.css('height'));

                //fade out old child and remove child afterwards
                oldNode.fadeOut(200, function () {
                    oldNode.remove();
                }.bind(oldNode));


                if (xlvoPlayer.config.use_mathjax && !!MathJax) {
                    xlvoPlayer.log('kick mathjax');
                    MathJax.Hub.Config(xlvoPlayer.mathjax_config);
                    MathJax.Hub.Queue(
                        ["Typeset", MathJax.Hub, 'xlvo-display-player']
                    );
                }

                xlvoPlayer.counter = 0;
                xlvoPlayer.buttons_handled = false;
            }
            xlvoPlayer.player = data.player;
            xlvoPlayer.handleQuestionButtons(data.buttons_html);
            xlvoPlayer.initElements();
            xlvoPlayer.timeout = setTimeout(xlvoPlayer.getPlayerData, xlvoPlayer.delay);
            xlvoPlayer.endRequest();
        });
    },

    handleSwitch: function () {
        xlvoPlayer.buttons_handled = false;
        xlvoPlayer.counter = 99;
        xlvoPlayer.endRequest();
    },

    /**
     * @param cmd
     * @param success
     * @param fail
     * @param voting_id
     */
    callPlayer: function (cmd, input_data) {
        if (xlvoPlayer.isRequestPending()) {
            xlvoPlayer.log('There is already a request');
        }

        xlvoPlayer.startRequest();
        xlvoPlayer.toolbar_loader.show();
        var success = success ? success : function () {
        }, fail = fail ? fail : function () {
        }, voting_id = voting_id ? voting_id : null;

        var input_data = input_data ? input_data : {};
        var post_data = $.extend({call: cmd}, input_data);
        $.post(xlvoPlayer.config.base_url + '&cmd=apiCall', post_data).done(function (data) {

            // xlvoPlayer.endRequest();
            xlvoPlayer.handleSwitch();
            xlvoPlayer.getPlayerData();
        });
    },
    /**
     * calls a custom button instance
     * @param button_id
     * @param data
     */
    callButton: function (button_id, data) {
        if (xlvoPlayer.isRequestPending()) {
            return;
        }
        xlvoPlayer.startRequest();
        xlvoPlayer.toolbar_loader.show();
        this.log('call Button: ' + button_id);
        $.post(xlvoPlayer.config.base_url + '&cmd=apiCall', {
            call: 'button',
            button_id: button_id,
            button_data: data
        }).done(function (data) {

        }).fail(function () {

        }).always(function () {
            xlvoPlayer.handleSwitch();
            xlvoPlayer.getPlayerData();
            xlvoPlayer.endRequest();
        });
    },
    /**
     * opens a question directly
     * @param id
     */
    open: function (id) {
        this.callPlayer('open', {xvi: id});
        return false;
    },
    /**
     * gets the current amount of attendees
     */
    updateAttendees: function () {
        if (xlvoPlayer.isRequestPending()) {
            return;
        }
        xlvoPlayer.startRequest();
        $.get(this.base_url, {cmd: "getAttendees"})
            .done(function (data) {
                $('#xlvo-attendees').html(data + ' Online');
                xlvoPlayer.timeout = setTimeout(xlvoPlayer.updateAttendees, 1000);
                xlvoPlayer.endRequest();
            });
    },
    /**
     * Handles some special functionality on startscreen
     */
    handleStartButton: function () {
        var btn = $('.xlvo-preview');
        btn.disableSelection();
        btn.click(function (evt) {
            xlvoPlayer.clearTimeout();
            if (evt.shiftKey) {
                window.location.href = btn.attr('href') + '&preview=1&key=1';
                return false;
            }
            return true;
        });
    },
    /**
     * @param html
     */
    handleQuestionButtons: function (html) {
        if (xlvoPlayer.buttons_handled) {
            this.log('buttons already handled for this question');
            return;
        }

        var custom_toolbar_dom = $('<div/>').html(html).contents(),
            custom_toolbar_inner = custom_toolbar_dom.find('ul.nav'),
            costom_buttons_count = custom_toolbar_inner.find('.btn').length,
            toolbar_inner = this.toolbar.find('ul.nav').last(),
            dynamic_sep = toolbar_inner.find('li#dynamic_sep');

        if (costom_buttons_count < 1 || !html || html === '') {
            if (dynamic_sep.length > 0) {
                dynamic_sep.nextAll().remove();
                dynamic_sep.remove();
            }
            xlvoPlayer.log('there are no custom buttons');
            xlvoPlayer.log(html);
            xlvoPlayer.buttons_handled = true;
            xlvoPlayer.toolbar_loader.hide();
            return;
        }
        xlvoPlayer.log('there are custom buttons!');

        custom_toolbar_inner.find('.btn').each(function () {
            $(this).addClass('xlvo_custom_button');
        });

        if (dynamic_sep.length > 0) {
            xlvoPlayer.log('removing everything after separator');
            dynamic_sep.nextAll().remove();
        } else {
            xlvoPlayer.log('appending separator');
            toolbar_inner.append("<li id='dynamic_sep' class='ilToolbarSeparator hidden-xs'></li>");
        }

        toolbar_inner.append(custom_toolbar_inner.html());
        toolbar_inner.find('.xlvo_custom_button').on("click", function () {
            xlvoPlayer.callButton($(this).attr('id'), true);

        });

        xlvoPlayer.toolbar_loader.hide();
        xlvoPlayer.buttons_handled = true;
    },
    /**
     * Startes the counter
     * @param seconds
     */
    countdown: function (seconds) {
        xlvoPlayer.countdown_running = true;
        xlvoPlayer.log('Countdown started: ' + seconds);
        xlvoPlayer.callPlayer('countdown', {seconds: seconds});
    },
    togglePull: function () {
        if (xlvoPlayer.timeout) {
            alert('Pull stopped');
            xlvoPlayer.clearTimeout();
            xlvoPlayer.timeout = false;
        } else {
            alert('Pull started');
            xlvoPlayer.getPlayerData();
        }
    },
    /**
     * @param data
     */
    log: function (data) {
        if (this.config.debug) {
            console.log(data);
        }
    },
    debug: function () {
        this.config.debug = true;
    },
    stop: function () {
        this.config.debug = false;
    }
};
