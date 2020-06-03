(() => {
    if (il.int_textinput_observer) {
        return;
    }
    il.int_textinput_observer = true;

    const observer = new MutationObserver((events) => {
        /**
         * @type {MutationRecord} event
         */
        for (const event of events) {
            //console.log(event);
            for (const node of event.addedNodes) {
                if (node instanceof Element) { // Some node types like text missing some methods, this can be ignored
                    for (const child_node of [node, ...node.querySelectorAll("*")]) { // Check item self and needs also to check all children because if only insert a new parent node with HTML, the needed node is missing in addedNodes
                        if (checkNode(child_node)) {
                            initNode(child_node);
                        }
                    }
                }
            }
        }
    });

    observer.observe(document.documentElement, {
        childList: true,
        subtree: true
    });

    /**
     * @param {Node} node
     *
     * @returns {boolean}
     */
    function checkNode(node) {
        return (node instanceof HTMLInputElement && node.type === "text" && node.classList.contains("form-control"));
    }

    /**
     * @param {HTMLInputElement} node
     */
    function initNode(node) {
        if (node._init_textinput) {
            return;
        }
        node._init_textinput = true;

        //console.log(node);

        if (node.dataset.autocomplete_url) {
            $.widget("custom.iladvancedautocomplete", $.ui.autocomplete, {
                more: false,
                _renderMenu: (ul, items) => {
                    const instance = $(node).iladvancedautocomplete("instance");

                    for (const item of items) {
                        instance._renderItemData(ul, item);
                    }

                    instance.options.requestUrl.searchParams.delete("fetchall");

                    if (instance.more) {
                        ul.append(`<li class='ui-menu-category ui-menu-more ui-state-disabled'><span>&raquo;${il.textinput_more_txt}</span></li>`);
                        ul.find('li').last().on('click', (e) => {
                            instance.options.requestUrl.searchParams.append("fetchall", "1");
                            instance.close(e);
                            instance.search(null, e);
                            e.preventDefault();
                        });
                    }
                }
            });

            $(node).iladvancedautocomplete({
                requestUrl: new URL(node.dataset.autocomplete_url, location.href),
                source: async (request, response) => {
                    const instance = $(node).iladvancedautocomplete("instance");

                    const url = new URL(instance.options.requestUrl);
                    url.searchParams.append("term", request.term);

                    const data = await (await fetch(url)).json();

                    if (typeof data.items === "undefined") {
                        instance.more = false;
                        response(data);
                    } else {
                        instance.more = data.hasMoreResults;
                        response(data.items);
                    }
                },
                minLength: 0
            });
        }
    }
})();
