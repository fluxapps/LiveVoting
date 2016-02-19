/**
 * Class xlvoModal
 * @type {{}}
 */
var xlvoModal = {
    init: function (json) {
        this.config = JSON.parse(json);

        $('#' + this.config.id).on('show.bs.modal', function () {
            $('.modal-content').css('height', $(window).height() * 0.95);
            $('.modal-content img').css('height', $('.modal-content').height() - 120);
        });
    }
};
