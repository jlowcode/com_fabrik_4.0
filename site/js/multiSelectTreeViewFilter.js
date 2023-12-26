jQuery(document).ready(function () {
    const root_url = jQuery('.root_url')[0].value;
    jQuery.getScript(root_url + "/media/com_fabrik/js/lib/require/require.js").done(function () {
        requirejs.config({
            baseUrl: root_url,
            paths: {
                fab: "media\/com_fabrik\/js\/dist",
                lib: "media\/com_fabrik\/js\/lib",
                element: "plugins\/fabrik_element",
                list: "plugins\/fabrik_list",
                form: "plugins\/fabrik_form",
                cron: "plugins\/fabrik_cron",
                viz: "plugins\/fabrik_visualization",
                admin: "administrator\/components\/com_fabrik\/views",
                adminfields: "administrator\/components\/com_fabrik\/models\/fields",
                jquery: "media\/jui\/js\/jquery",
                jQueryUI: "media\/com_fabrik\/js\/lib\/jquery-ui\/jquery-ui",
                chosen: "media\/jui\/js\/chosen.jquery.min",
                ajaxChosen: "media\/jui\/js\/ajax-chosen.min",
                punycode: "media\/system\/js\/punycode",
                filterTree: 'components/com_fabrik/js/tree.jquery'
            },
            shim: {
                "filterTree": {
                    "deps": ['jquery']
                },
                "fab\/fabrik":
                    { "deps": ["fab\/utils", "jquery", "fab\/mootools-ext", "lib\/Event.mock"] },
                "fab\/autocomplete-bootstrap":
                    { "deps": ["fab\/fabrik"] },
                "jQueryUI":
                    { "deps": ["jquery"] },
                "fab\/list": { "deps": [] }
            },
        })
        requirejs(['fab/fabrik', 'fab/list', 'filterTree', 'jquery'], function (Fabrik, FbList, tree, jQuery) {
            jQuery(document).ready(function () {
                Fabrik.addEvent('fabrik.list.loaded', function (list) {
                    var data = list;
                    [].forEach.call(document.getElementsByClassName('tree-view-filter'), function (element) {
                        let mainDiv = element;
                        let selectedCheckbox = jQuery(mainDiv).find('.selected-checkbox')[0];
                        
                        const join_name = jQuery(mainDiv).find('.join_name')[0].value;
                        const join_val_column = jQuery(mainDiv).find('.join_val_column')[0].value;
                        const join_key_column = jQuery(mainDiv).find('.join_key_column')[0].value;
                        const root_url = jQuery(mainDiv).find('.root_url')[0].value;
                        const tree_parent_id = jQuery(mainDiv).find('.tree_parent_id')[0].value;
                        const name = jQuery(mainDiv).find('.name')[0].value;
                        const element_name = jQuery(mainDiv).find('.element_name')[0].value;
                        const elementTreeId = String('#' + 'tree_simples_' + element_name);
                        const count_filter = jQuery(mainDiv).find('.count_filter')[0];
                        const filter_sortedby = jQuery(mainDiv).find('.filter_sortedby')[0].value;
                        let el = jQuery(mainDiv).find(elementTreeId)[0];
                        let dataCount;
                        if (count_filter) {
                            dataCount = JSON.parse(decodeURIComponent(jQuery(mainDiv).find('.count_filter')[0].value));
                        }

                        let tags = [];

                        var count = 0;

                        var cssId = 'tagsCss';  // you could encode the css path itself to generate id..
                        if (!document.getElementById(cssId)) {
                            var head = document.getElementsByTagName('head')[0];
                            var link = document.createElement('link');
                            link.id = cssId;
                            link.rel = 'stylesheet';
                            link.type = 'text/css';
                            link.href = root_url + 'plugins/fabrik_element/databasejoin/tags.css';
                            link.media = 'all';
                            head.appendChild(link);
                        }
                        var cssId = 'jqtree';  // you could encode the css path itself to generate id..
                        if (!document.getElementById(cssId)) {
                            var head = document.getElementsByTagName('head')[0];
                            var link = document.createElement('link');
                            link.id = cssId;
                            link.rel = 'stylesheet';
                            link.type = 'text/css';
                            link.href = root_url + 'plugins/fabrik_element/databasejoin/jqtree.css';
                            link.media = 'all';
                            head.appendChild(link);
                        }

                        // Build the tree making an AJAX request getting only the root nodes
                        jQuery.ajax({
                            url: root_url + 'plugins/fabrik_element/databasejoin/treeViewSearch.php',
                            data: {
                                value: null,
                                join_name: join_name,
                                join_val_column: join_val_column,
                                join_key_column: join_key_column,
                                tree_parent_id: tree_parent_id,
                                filter_sortedby: filter_sortedby
                            },
                            success: function (result) {
                                res = result;
                                let roots = [];
                                let children = [];
                                res.forEach(node => {
                                    if (node.children) {
                                        node.children = [{}];
                                    }

                                    //Se o usuario selecionou o Order by como 'Count'
                                    if (count_filter) {
                                        dataCount.forEach(element => {
                                            if (element.value == node.id) {
                                                node.counter = element.counter;
                                                return;
                                            }
                                        });
                                    }
                                });

                                //Se o usuario selecionou o Order by 'Count'
                                if (count_filter) {
                                    if (dataCount) {
                                        //Se nos parametros o usuário selecionou como desc (1)
                                        if (filter_sortedby == 1)
                                            res.sort(compareValues('counter', 'desc'));
                                        else
                                            res.sort(compareValues('counter', 'asc'));
                                    }
                                }

                                jQuery(elementTreeId).tree({
                                    data: res,
                                    selectable: false,
                                    onCreateLi: function (node, $li, is_selected) {
                                        if (node.counter) {
                                            $li.find('.jqtree-title')[0].innerHTML = $li.find('.jqtree-title')[0].innerHTML +  " (" + node.counter + ") "
                                            //$li.find('.jqtree-title').after('&nbsp<span>(' + node.counter + ')</span>');
                                        }
                                    }
                                });

                            },
                            dataType: "json"
                        });


                        // Make an AJAX request when open a tree branch
                        jQuery(elementTreeId).on(
                            'tree.open',
                            function (e) {
                                let id = parseInt(e.node.id);
                                jQuery.ajax({
                                    url: root_url + 'plugins/fabrik_element/databasejoin/treeViewSearch.php',
                                    data: {
                                        value: id,
                                        join_name: join_name,
                                        join_val_column: join_val_column,
                                        join_key_column: join_key_column,
                                        tree_parent_id: tree_parent_id,
                                        filter_sortedby: filter_sortedby
                                    },
                                    success: function (result) {
                                        let res = result;
                                        res.forEach(node => {
                                            if (node.children) {
                                                node.children = [{}];
                                            }

                                            //Se o usuario selecionou o Order by como 'Count'
                                            if (count_filter) {
                                                dataCount.forEach(element => {
                                                    if (element.value == node.id) {
                                                        node.counter = element.counter;
                                                        return;
                                                    }
                                                });
                                            }
                                        });

                                        if (count_filter) {
                                            if (dataCount) {
                                                if (filter_sortedby == 1)
                                                    res.sort(compareValues('counter', 'desc'));
                                                else
                                                    res.sort(compareValues('counter', 'asc'));
                                            }
                                        }

                                        jQuery(elementTreeId).tree('loadData', res, e.node);
                                    },
                                    dataType: "json"
                                });

                            }
                        );

                        function compareValues(key, order = 'asc') {
                            return function (a, b) {
                                if (!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
                                    return 0;
                                }

                                const varA = a[key];
                                const varB = b[key];

                                let comparison = 0;
                                if (varA > varB) {
                                    comparison = 1;
                                } else if (varA < varB) {
                                    comparison = -1;
                                }
                                return (
                                    (order == 'desc') ?
                                        (comparison * -1) : comparison
                                );
                            };
                        }

                        function addTag(text, id) {
                            let tag = {
                                id: id,
                                text: text,
                                container: document.createElement('div'),
                                content: document.createElement('span'),
                                input: document.createElement('input'),
                                closeButton: document.createElement('span')
                            };

                            tag.container.classList.add('tag-container');
                            tag.container.classList.add('fabrik_filter');
                            tag.container.setAttribute('type-filter', 'treeview');
                            tag.container.setAttribute("data-filter-name", element_name);
                            tag.container.setAttribute('name', name + "[" + Math.floor(Math.random() * 10) + "]");
                            tag.content.classList.add('tag-content');
                            tag.closeButton.classList.add('tag-close-button');

                            tag.input.value = id;
                            tag.input.setAttribute('type', 'checkbox');
                            tag.input.setAttribute('data-filter-name', element_name);
                            tag.input.setAttribute('class', 'fabrik_filter');
                            tag.input.setAttribute('style', 'display: none');
                            tag.input.setAttribute('name', name + "[" + Math.floor(Math.random() * 10) + "]");

                            selectedCheckbox.appendChild(tag.input);

                            tag.content.textContent = tag.text;
                            tag.closeButton.textContent = 'x';

                            tag.closeButton.addEventListener('click', function (bool2) {
                                removeTag(tags.indexOf(tag), bool2);
                            }, false);

                            tag.container.appendChild(tag.content);
                            tag.container.appendChild(tag.closeButton);

                            tags.push(tag);

                            mainDiv.insertBefore(tag.container, el);

                            var hello = data;

                            hello.watchFilters();
                            $(tag.input).prop("checked", true);
                            list.doFilter();
                        }

                        function alreadyInTagsList(id) {
                            for (let i = 0; i < tags.length; i++) {
                                if (tags[i].id === id) {
                                    return true;
                                }
                            }
                            return false;
                        }

                        function removeTag(index, bool) {
                            if (!bool.isTrusted) {
                                for (var i = tags.length - 1; i >= 0; i--) {
                                    if (tags[i]) {
                                        $(tags[i].input).prop("checked", false);
                                        mainDiv.removeChild(tags[i].container);
                                        selectedCheckbox.removeChild(tags[i].input);
                                        tags.splice(i, 1);
                                    }
                                }
                            } else {
                                let tag = tags[index];
                                tags.splice(index, 1);
                                mainDiv.removeChild(tag.container);
                                $(tag.input).prop("checked", false);
                                list.doFilter();
                                selectedCheckbox.removeChild(tag.input);
                            }
                        }

                        // On click on a node adds it to tags
                        jQuery(elementTreeId).on(
                            'tree.click',
                            function (event) {
                                var node = event.node;
                                //Se não encontrou o elemento na lista então adiciona uma tag
                                if (!alreadyInTagsList(node.id)) {
                                    addTag(node.name, node.id);
                                }
                            }
                        );

                    });
                });
            });
        });
    });

});

