$(document).ready(function () {
    $("input[type=checkbox][data-ajaxcheckbox]").each(function (i, el) {
        const config = JSON.parse(atob(el.dataset.ajaxcheckbox));

        if (!config.ajax_change_link) {
            return;
        }

        $(el).change(function () {
            il.waiter.show();

            $.ajax({
                url: config.ajax_change_link,
                type: "POST",
                data: {
                    checked: el.checked
                }
            }).always(function () {
                il.waiter.hide();
            });
        });
    });
});
