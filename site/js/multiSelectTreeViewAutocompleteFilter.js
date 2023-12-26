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
                    var dataList = list;
                    [].forEach.call(document.getElementsByClassName('autocomplete-treeview-filter'), function (element) {
                        let mainDiv = element;
                        element.classList.add("autocomplete");
                        let selectedCheckbox = jQuery(mainDiv).find('.selected-checkbox')[0];
                        
                        const join_name = jQuery(mainDiv).find('.join_name')[0].value;
                        const join_val_column = jQuery(mainDiv).find('.join_val_column')[0].value;
                        const join_key_column = jQuery(mainDiv).find('.join_key_column')[0].value;
                        const root_url = jQuery(mainDiv).find('.root_url')[0].value;
                        const tree_parent_id = jQuery(mainDiv).find('.tree_parent_id')[0].value;
                        const name = jQuery(mainDiv).find('.name')[0].value;
                        const element_name = jQuery(mainDiv).find('.element_name')[0].value;
                        let mainInput = document.getElementById(String('autocomplete-treeview-filter-input-' + element_name));
                        const count_filter = jQuery(mainDiv).find('.count_filter')[0];
                        const filter_sortedby = jQuery(mainDiv).find('.filter_sortedby')[0].value;
                        const elementTreeId = String('#' + 'tree_' + element_name);
                        const default_filter = jQuery(mainDiv).find('.default_filter')[0];
                        let dataCount;
                        if (count_filter) {
                            dataCount = JSON.parse(decodeURIComponent(jQuery(mainDiv).find('.count_filter')[0].value));
                        }

                        let defautValues;
                        if (default_filter) {
                            defautValues = JSON.parse(decodeURIComponent(default_filter.value));
                        }

                        let dataObj = {
                            value: null,
                            join_name: join_name,
                            join_val_column: join_val_column,
                            join_key_column: join_key_column,
                            tree_parent_id: tree_parent_id,
                            filter_sortedby: filter_sortedby
                        }

                        let tags = [];

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

                        var autoTree = 'autocompletetreeview';  // you could encode the css path itself to generate id..
                        if (!document.getElementById(autoTree)) {
                            var head = document.getElementsByTagName('head')[0];
                            var link = document.createElement('link');
                            link.id = autoTree;
                            link.rel = 'stylesheet';
                            link.type = 'text/css';
                            link.href = root_url + 'plugins/fabrik_element/databasejoin/autocompletetreeview.css';
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

                                $(elementTreeId).tree({
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
                        $(elementTreeId).on(
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

                                        $(elementTreeId).tree('loadData', res, e.node);
                                    },
                                    dataType: "json"
                                });

                            }
                        );

                        // On click on a node adds it to tags
                        $(elementTreeId).on(
                            'tree.click',
                            function (event) {
                                var node = event.node;
                                var found = false;
                                //Se não encontrou o elemento na lista então adiciona uma tag
                                if (!alreadyInTagsList(node.name)) {
                                    addTag(node.name, node.id);
                                }
                            }
                        );

                        function alreadyInTagsList(text) {
                            for (let i = 0; i < tags.length; i++) {
                                if (tags[i].text === text) {
                                    return true;
                                }
                            }
                            return false;
                        }

                        function closeAllLists(elmnt) {
                            /*close all autocomplete lists in the document,
                            except the one passed as an argument:*/
                            var x = document.getElementsByName(join_name + "-autocomplete-filter-list");
                            for (var i = 0; i < x.length; i++) {
                                if (elmnt != x[i] && elmnt != mainInput) {
                                    x[i].parentNode.removeChild(x[i]);
                                }
                            }
                        }

                        function addActive(x) {
                            /*a function to classify an item as "active":*/
                            if (!x) return false;
                            /*start by removing the "active" class on all items:*/
                            removeActive(x);
                            if (currentFocus >= x.length) currentFocus = 0;
                            if (currentFocus < 0) currentFocus = (x.length - 1);
                            /*add class "autocomplete-active":*/
                            x[currentFocus].classList.add(this.id + "autocomplete-active");
                        }

                        function removeActive(x) {
                            /*a function to remove the "active" class from all autocomplete items:*/
                            for (var i = 0; i < x.length; i++) {
                                x[i].classList.remove(this.id + "autocomplete-active");
                            }
                        }

                        /*execute a function when someone clicks in the document:*/
                        document.addEventListener("click", function (e) {
                            closeAllLists(e.target);
                            if(mainInput.value){
                                mainInput.value = '';
                            }
                        });

                        /*execute a function presses a key on the keyboard:*/
                        mainInput.addEventListener("keydown", function (e) {
                            var x = document.getElementById(join_name + "-autocomplete-filter-list");
                            if (x) x = x.getElementsByTagName("div");
                            if (e.keyCode == 40) {
                                /*If the arrow DOWN key is pressed,
                                increase the currentFocus variable:*/
                                currentFocus++;
                                /*and and make the current item more visible:*/
                                addActive(x);
                            } else if (e.keyCode == 38) { //up
                                /*If the arrow UP key is pressed,
                                decrease the currentFocus variable:*/
                                currentFocus--;
                                /*and and make the current item more visible:*/
                                addActive(x);
                            } else if (e.keyCode == 13) {
                                /*If the ENTER key is pressed, prevent the form from being submitted,*/
                                e.preventDefault();
                                if (currentFocus > -1) {
                                    /*and simulate a click on the "active" item:*/
                                    if (x) x[currentFocus].click();
                                }
                            }
                        });

                        mainInput.addEventListener("input", function (e) {
                            var a, b, i, val = this.value;
                            /*close any already open lists of autocompleted values*/
                            closeAllLists();
                            if (!val) { return false; }
                            currentFocus = -1;
                            /*create a DIV element that will contain the items (values):*/
                            a = document.createElement("DIV");
                            a.setAttribute("id", join_name + "autocomplete-filter-list");
                            a.setAttribute("name", join_name + "-autocomplete-filter-list");
                            a.setAttribute("class", "autocomplete-items");
                            //a.setAttribute("style", "display: inline-block;");
                            /*append the DIV element as a child of the autocomplete container:*/
                            this.parentNode.insertBefore(a, this.parentNode.getElementById('tree'));
                            //this.parentNode.appendChild(a);
                            if (val.length < 2)
                                return;

                            $.ajax({
                                url: root_url + 'plugins/fabrik_element/databasejoin/autocompleteSearch.php',
                                data: {
                                    value: val,
                                    join_name: join_name,
                                    join_val_column: join_val_column,
                                    join_key_column: join_key_column
                                },
                                success: function (data) {
                                    data.forEach(result => {
                                        b = document.createElement("DIV");
                                        /*make the matching letters bold:*/
                                        b.innerHTML = "<strong>" + result.text.substr(0, val.length) + "</strong>";
                                        b.innerHTML += result.text.substr(val.length);
                                        /*insert a input field that will hold the current array item's value:*/
                                        b.innerHTML += "<input type='hidden' value='" + result.text + "'>";
                                        /*execute a function when someone clicks on the item value (DIV element):*/
                                        b.addEventListener("click", function (e) {
                                            if (!alreadyInTagsList(result.text)) {
                                                addTag(result.text, result.value);
                                                closeAllLists();
                                                mainInput.value = '';
                                            }
                                        });

                                        a.appendChild(b);
                                    });
                                },
                                dataType: "json"
                            });
                        })

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
                            //tag.container.classList.add('inputbox');
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

                            mainDiv.insertBefore(tag.container, mainInput);

                            var hello = dataList;

                            hello.watchFilters();
                            $(tag.input).prop("checked", true);
                            list.doFilter();
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

                    });
                });
            });
        });
    });

});

