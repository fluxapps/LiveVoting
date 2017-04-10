/**
 * Main LV Function
 * @type {{init: xlvoMain.init}}
 */
var xlvoMain = {
    init: function () {
        var $subtabSubtabEdit = $('#subtab_subtab_edit a');
        $subtabSubtabEdit.disableSelection();
        $subtabSubtabEdit.click(function (evt) {
            if (evt.shiftKey) {
                window.location.href = $subtabSubtabEdit.attr('href') + '&import=1';
                return false;
            }
            return true;
        });
    }
};
