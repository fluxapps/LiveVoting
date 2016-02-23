/**
 * Class xlvoModal
 * @type {{}}
 */
var xlvoModal = {
    init: function (json) {
        this.config = JSON.parse(json);


        $('#' + this.config.id).on('show.bs.modal', function () {
            var modal = $('.modal-content');
            modal.css('height', $(window).height() * 0.95);
            //var new_img_height = modal.height() - 120;
            //var img = modal.find('img');
            //if (img.width() >= new_img_height) {
            //    img.css('height', new_img_height);
            //}

        });
        //
        //$('#' + this.config.id).on('shown.bs.modal', function () {
        //    var modal = $('.modal-content');
        //    modal.find('span.label').css('font-size', '');
        //    var modal_width = modal.width(),
        //        text_width = modal.find('span.label').width();
        //
        //    var ratio = Math.round(text_width / 100 * modal_width / 7);
        //
        //    modal.find('span.label').css('font-size', ratio + '%');
        //});
    }
};
