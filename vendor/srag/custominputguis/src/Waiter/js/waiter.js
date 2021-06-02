/**
 * il.waiter
 *
 * GUI-Overlay
 *
 * @type {Object}
 */
il.waiter = {
    /**
     * @type {string}
     */
    type: 'waiter',
    /**
     * @type {number}
     */
    count: 0,
    /**
     * @type {number|null}
     */
    timer: null,

    /**
     * @param {string} type
     */
    init: function (type) {
        this.type = type ? type : this.type;
        if (this.type == 'waiter') {
            $('body').append('<div id="srag_waiter" class="srag_waiter"></div>');
            //console.log('il.waiter: added srag_waiter to body');
        } else {
            $('body').append('<div id="srag_waiter" class="srag_waiter_percentage">' +
                '<div class="progress" >' +
                '<div id="srag_waiter_progress" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">' +
                '<div id="srag_waiter_progress_text" class="progress-bar-text"></div>' +
                '</div></div></div>');
            //console.log('il.waiter: added srag_waiter_percentage to body');
        }
    },

    /**
     *
     */
    show: function () {
        if (this.count == 0) {
            this.timer = setTimeout(function () {
                $('#srag_waiter').show();
            }, 10);

        }
        this.count = this.count + 1;
    },

    /**
     * @param {string} type
     */
    reinit: function (type) {
        var type = type ? type : this.type;
        this.count = 0;

        $('#srag_waiter').attr('id', 'srag_waiter2');
        this.init(type);
        $('#srag_waiter2').remove();
    },

    /**
     *
     */
    hide: function () {
        this.count = this.count - 1;
        if (this.count == 0) {
            window.clearTimeout(this.timer);
            $('#srag_waiter').fadeOut(200);
            this.resetProgress();
        }
    },

    /**
     * @param {number} percent
     */
    setPercentage: function (percent) {
        $('#srag_waiter_progress').css('width', percent + '%').attr('aria-valuenow', percent);
    },

    /**
     * use this method instead of setPercentage to show the amount of bytes loaded (e.g. "10.5MB/100MB")
     *
     * @param {number} loaded
     * @param {number} total
     */
    setBytes: function (loaded, total) {
        const percentage = loaded / total * 100;
        this.setPercentage(percentage);
        let loadedHuman = this.humanFileSize(loaded, true);
        let totalHuman = this.humanFileSize(total, true);
        if (loadedHuman === totalHuman) { /* add decimals  */
            loadedHuman = this.humanFileSize(loaded, true, 3);
            totalHuman = this.humanFileSize(total, true, 3);
        }
        $('#srag_waiter_progress_text').text(loadedHuman + " / " + totalHuman);
    },

    /**
     *
     */
    resetProgress: function () {
        this.setPercentage(0);
        $('#srag_waiter_progress_text').text('');
    },

    /**
     * @param {string} dom_selector_string
     */
    addListener: function (dom_selector_string) {
        var self = this;
        $(document).ready(function () {
            $(dom_selector_string).on("click", function () {

                self.show();
            });
        });
    },

    /**
     * @param {string} dom_selector_string
     */
    addLinkOverlay: function (dom_selector_string) {
        var self = this;
        $(document).ready(function () {
            $(dom_selector_string).on("click", function (e) {
                e.preventDefault();
                //console.log('il.waiter: clicked on registred link');
                self.show();
                var href = $(this).attr('href');
                setTimeout(function () {
                    document.location.href = href;
                }, 1000);
            });
        });
        //console.log('il.waiter: registred LinkOverlay: ' + dom_selector_string);
    },

    /**
     * Format bytes as human-readable text.
     *
     * @param {number} bytes Number of bytes.
     * @param {boolean} si True to use metric (SI) units, aka powers of 1000. False to use
     *           binary (IEC), aka powers of 1024.
     * @param {number} dp Number of decimal places to display.
     *
     * @returns {string} Formatted string.
     */
    humanFileSize: function (bytes, si = false, dp = 1) {
        const thresh = si ? 1000 : 1024;

        if (Math.abs(bytes) < thresh) {
            return bytes + ' B';
        }

        const units = si
            ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
            : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
        let u = -1;
        const r = 10 ** dp;

        do {
            bytes /= thresh;
            ++u;
        } while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);


        return bytes.toFixed(dp) + ' ' + units[u];
    }
};
