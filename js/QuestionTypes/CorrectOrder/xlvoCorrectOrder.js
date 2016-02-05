/**
 * Class xlvoCorrectOrder
 * @type {{}}
 */
var xlvoCorrectOrder = {
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
    },
    base_url: '',
    run: function () {
        this.addSortable();

        var $formXlvoSortable = $('#form_xlvo_sortable');

        $formXlvoSortable.on('submit', function () {
            alert();
            $formXlvoSortable.find('.lvo_bar_movable_item').each(function(item){
                alert($(this));
            });
            return false;
        });

    }
    ,
    addSortable: function () {
        $('#lvo_bar_movable').sortable({
            placeholder: "list-group-item list-group-item-danger xlvolist-group-fix"
        });
        $("#lvo_bar_movable").disableSelection();
    }
};
