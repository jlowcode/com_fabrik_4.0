define(['fab/fabrik', 'https://cdnjs.cloudflare.com/ajax/libs/rxjs/4.1.0/rx.all.min.js'], function () {

    
    var BootstrapTree = {
        options: {
            children       : {},
            childrenLoading: {},
        }
    };
    
    
    /**
     * Returns the id of the current list using regex
     * @throws "List id not found"
     * @returns int
     */
    BootstrapTree.getListId = function () {
        var form = jQuery('form.fabrikForm')
        if(form) {
            const id    = form.attr("id");
            const re    = /(\D+)_(\d+)_(\D+)_(\d+)/gm;
            var   match = re.exec(id);
            if(match) {
                return match[2]
            } else {
                throw "List id not found";
            }
        }
        throw "List id not found";
    };

    /**
     * Get the parent element from the server
     * @returns Observable
     */
    BootstrapTree.getParentElement = function () {
        const url = BootstrapTree.options.liveSite +
        "index.php?option=com_fabrik&"+
        "format=raw&task=plugin.userAjax&"+
        "method=getParentElementLayoutTree&"+
        "list_id="+BootstrapTree.options.listId;

        return Rx.Observable.fromPromise(
            jQuery.get({
                url     : url,
                dataType: 'text'
            })
        ).map(parent_element => {
            return JSON.parse(parent_element);
        });
    };

    BootstrapTree.processChild = function (child, id) {
        var   jChild  = jQuery(child);
        var   firstTd = jChild.find('td:first-child')
        const lastId  = jChild.attr("id");
        jChild.attr("id",lastId+"_child");
        jChild.attr("data-parent", id);
        return jChild;
    }

    /**
     * Get the root elements
     * @returns Observable
     */
    BootstrapTree.getListName = function () {
        const url = BootstrapTree.options.liveSite +
        "index.php?option=com_fabrik&"+
        "format=raw&task=plugin.userAjax&"+
        "method=getListName&"+
        "list_id="+BootstrapTree.options.listId;

        return Rx.Observable.fromPromise(
            jQuery.get({
                url     : url,
                dataType: 'text'
            })
        ).map(parent_element => {
            return JSON.parse(parent_element);
        });
    };

    /**
     * Get the root elements
     * @returns Observable
     */
    BootstrapTree.getElementsWithoutParent = function (listName, parentElementName) {
        const url = BootstrapTree.options.liveSite +
        "index.php?option=com_fabrik&view=list&"+
        "listid="+BootstrapTree.options.listId+
        "&resetfilters=1&"+
        listName + "___" + parentElementName + "_raw[condition]=IS%20NULL";
        
        return Rx.Observable.fromPromise(
            jQuery.get({
                url     : url,
                dataType: 'text'
            })
        ).map(elements => {
            // Parse HTML data
            var page = jQuery.parseHTML(elements)
            // Pega todos os tr filhos
            var children = jQuery(page).find('tbody.fabrik_groupdata > tr').not(".groupDataMsg");
            return children;
        });        
    };

    // Returns all ids from table
    BootstrapTree.buildElementsList = function (tableBody) {
        // var list = [];
        var dict = {}
        tableBody.each(function(index, tr) { 
            var  match                  = /(.*)_(.*)_(.*)_(.*)_(.*)_(.*)_(.*)/g.exec(jQuery(tr).attr('id'));
            var  id                     = match[7];
            dict[jQuery(tr).attr('id')] = {
                obj: jQuery(tr),
                id : id
            }
        });
        return dict;
    };

    BootstrapTree.processParent = function (parent, treeIconId, level = 0) {
        const paddingValue = level*15;
        var   firtsTd      = parent.find('td:first-child');
        firtsTd.css({
            'display'     : 'flex',
            'align-items' : 'center',
            'padding-left': paddingValue + "px"
        });

        var plusIconClosed = jQuery('<div class="button_parent_class" id="'+treeIconId+'">►</div>').css({
            // 'margin': '5px',
            'padding-right': '5px',
            'cursor'       : 'pointer',
        });
        
        firtsTd.prepend(plusIconClosed);

        return plusIconClosed;
    };

    BootstrapTree.getElementChildren = function (elementId) {
        const url = BootstrapTree.options.liveSite +
        "index.php?option=com_fabrik&view=list&"+
        "listid="+BootstrapTree.options.listId+
        "&resetfilters=1&"+
        BootstrapTree.options.db_table_name + "___" + 
        BootstrapTree.options.parent_element_name + "_raw="+elementId+
        "&layout=bootstrap";
        
        return Rx.Observable.fromPromise(
            jQuery.get({
                url     : url,
                dataType: 'text'
            })
        ).map(elements => {
            // Parse HTML data
            var page = jQuery.parseHTML(elements)
            // Pega todos os tr da tabela da pagina carregada por ajax
            var fullListTableBody = jQuery(page).find('tbody.fabrik_groupdata > tr').not(".groupDataMsg");
            return fullListTableBody;
        });   
    };

    BootstrapTree.toggleNode = function (parent_id, childreen = null) {
        if(childreen) {
            // If has children array show
            childreen.forEach(function (child) {
                var jChild = jQuery(child);
                if(jChild.is(":visible")){
                } else {
                    thisIcon.innerText = "▼";
                    jChild.show();
                }
            })
        } else {
            // Else hide
            BootstrapTree.hideChildren(parent_id);
        }
    }

    // Oculta todos os filhos do parent
    BootstrapTree.hideChildren = function (parent_id) {
        var children = jQuery('tr[data-parent="'+parent_id+'"]');
        if(children.length > 0) {
            children.each(function () {
                BootstrapTree.hideChildren(jQuery(this).attr("id"));
            });
        }
        children.hide();
        var childrenIcons = children.find(".button_parent_class");
        childrenIcons.each(function( index ) {
            this.innerText = "►";
        });
    }

    // Processa nós da raiz
    BootstrapTree.processRoot = function (table) {
        var rootElements = BootstrapTree.options.root_elements;
        var i            = 0;
        table.each(function(index, tr) {
            const id = jQuery(tr).attr('id');
            // Se for um elemento da raiz da arvore
            if(rootElements.hasOwnProperty(id)) {
                
                var idNum = rootElements[id].id;
                
                var                                   icon        = BootstrapTree.processParent(jQuery(tr), 'button_parent'+i);
                var                                   thisElement = jQuery(tr);
                BootstrapTree.options.childrenLoading[id]         = false;
                icon.on('click', function () {
                    var   thisIcon           = this;
                    const loadingIcon        = jQuery('<img src="'+BootstrapTree.options.liveSite+'/media/com_fabrik/images/ajax-loader.gif">');
                          thisIcon.innerText = "";
                    thisIcon.append(loadingIcon[0]);
                    // Verify if has already catched the children
                    if(BootstrapTree.options.children.hasOwnProperty(id)) {
                        var c = BootstrapTree.options.children[id];
                        if(jQuery(c).is(":visible")) {
                            thisIcon.innerText = "►";
                            BootstrapTree.hideChildren(id);
                        } else {
                            thisIcon.innerText = "▼";
                            BootstrapTree.options.children[id].forEach(function (child) {
                                var jChild = jQuery(child);
                                jChild.show();
                            })
                        }
                    } else {
                        if(BootstrapTree.options.childrenLoading[id])
                            return;
                        BootstrapTree.options.childrenLoading[id] = true;
                        BootstrapTree.getElementChildren(idNum).subscribe(result => {
                            thisIcon.innerText = "▼";
                            if(result.length == 0) {
                                this.remove();
                                return;
                            } 

                            var processedChildren = [];

                            result.each(function( index ) {
                                var child = BootstrapTree.processChild(this, thisElement.attr("id"));
                                processedChildren.append(
                                    jQuery(child));
                                thisElement.after(child);
                            });
                            
                            BootstrapTree.options.children[id] = processedChildren;
                            BootstrapTree.process(result, 1);
                        });
                    }
                });
                i++;
            } else {
                // Se n for da raiz oculta
                jQuery(tr).hide();
            }
        });
    };

    // Processa outros nós
    BootstrapTree.process = function (table, level) {
        table.each(function(index, tr) {
            const id    = jQuery(tr).attr('id');
            var   match = /(.*)_(.*)_(.*)_(.*)_(.*)_(.*)_(.*)_child/g.exec(id);
            var   idNum = match[7];
            // var idNum = rootElements[id].id;
                
            var                                   icon        = BootstrapTree.processParent(jQuery(tr), 'button_parent'+i, level);
            var                                   thisElement = jQuery(tr);
            BootstrapTree.options.childrenLoading[id]         = false;
            icon.on('click', function () {
                var thisIcon = this;
                // Verify if has already catched the children
                if(BootstrapTree.options.children.hasOwnProperty(id)) {
                    // BootstrapTree.options.children[id].forEach(function (child) {
                    //     var jChild = jQuery(child);
                    //     if(jChild.is(":visible")){
                    //         thisIcon.innerText = "►";
                    //         BootstrapTree.hideChildren(id);
                    //         jChild.hide();
                    //     } else {
                    //         thisIcon.innerText = "▼";
                    //         jChild.show();
                    //     }
                    // })
                    var c = BootstrapTree.options.children[id];
                    if(jQuery(c).is(":visible")) {
                        thisIcon.innerText = "►";
                        BootstrapTree.hideChildren(id);
                    } else {
                        thisIcon.innerText = "▼";
                        BootstrapTree.options.children[id].forEach(function (child) {
                            var jChild = jQuery(child);
                            jChild.show();
                        })
                    }
                } else {
                    if(BootstrapTree.options.childrenLoading[id])
                        return;
                    BootstrapTree.options.childrenLoading[id]            = true;
                                                          this.innerText = "▼";
                    BootstrapTree.getElementChildren(idNum).subscribe(result => {
                        if(result.length == 0) {
                            this.remove();
                            return;
                        } 

                        var processedChildren = [];

                        result.each(function( index ) {
                            var child = BootstrapTree.processChild(this, thisElement.attr("id"));
                            processedChildren.append(
                                jQuery(child));
                            thisElement.after(child);
                        });
                        
                        BootstrapTree.options.children[id] = processedChildren;
                        BootstrapTree.process(result, level+1);
                    });
                }
            });
            i++;
        });
    };

    BootstrapTree.buildAjaxEvents = function () {
        // Pega todos os elementos da página que está navegando
        var tableBody = jQuery('tbody.fabrik_groupdata > tr').not(".groupDataMsg");
        // Constroi lista de elementos 
        // var thisPageElements = buildElementsList(tableBody);

        var rootElements = BootstrapTree.options.root_elements;
        var i            = 0;

        // Para cada elemento da pagina carregada
        tableBody.each(function(index, tr) {

            const id = jQuery(tr).attr('id');
            // Se for um elemento da raiz da arvore
            if(rootElements.hasOwnProperty(id)) {
                var                                   idNum       = rootElements[id].id;
                var                                   icon        = BootstrapTree.processParent(jQuery(tr), 'button_parent'+i);
                var                                   thisElement = jQuery(tr);
                BootstrapTree.options.childrenLoading[id]         = false;
                icon.on('click', function () {
                    var thisIcon = this;
                    // Verify if has already catched the children
                    if(BootstrapTree.options.children.hasOwnProperty(id)) {
                        var c = BootstrapTree.options.children[id];
                        if(jQuery(c).is(":visible")) {
                            thisIcon.innerText = "►";
                            BootstrapTree.hideChildren(id);
                        } else {
                            thisIcon.innerText = "▼";
                            BootstrapTree.options.children[id].forEach(function (child) {
                                var jChild = jQuery(child);
                                jChild.show();
                            })
                        }
                        // BootstrapTree.options.children[id].forEach(function (child) {
                        //     var jChild = jQuery(child);
                        //     if(jChild.is(":visible")){
                        //         thisIcon.innerText = "►";
                        //         BootstrapTree.hideChildren(id);
                        //         jChild.hide();
                        //     } else {
                        //         thisIcon.innerText = "▼";
                        //         jChild.show();
                        //     }
                        // })
                    } else {
                        if(BootstrapTree.options.childrenLoading[id])
                            return;
                        BootstrapTree.options.childrenLoading[id]            = true;
                                                              this.innerText = "▼";
                        BootstrapTree.getElementChildren(idNum).subscribe(result => {

                            var processedChildren = [];

                            result.each(function( index ) {
                                var child = BootstrapTree.processChild(this, thisElement.attr("id"));
                                processedChildren.append(
                                    jQuery(child));
                                thisElement.after(child);
                            });
                            
                            BootstrapTree.options.children[id] = processedChildren;
                        });
                    }
                });
                i++;
            } else {
                // Se n for da raiz oculta
                jQuery(tr).hide();
            }
        });
    };
    
    /**
     * Initializes the plugin
     */
    BootstrapTree.init = async function () {

        this.options = {
            children       : {},
            childrenLoading: {},
        };

        try {
            BootstrapTree.options.listId         = BootstrapTree.getListId();
            BootstrapTree.options.liveSite       = './';
            BootstrapTree.options.parentElement$ = BootstrapTree.getParentElement();

            // Verify if parent element was selected
            var parentElementValue = await BootstrapTree.options.parentElement$.take(1).toPromise();
            if(parentElementValue.error) {
                // If the parent was not selected stop the bootstrap tree
                return;
            }

            BootstrapTree.options.listName$      = BootstrapTree.getListName();
            
            // Switch map of the observable to include parentElement data
            BootstrapTree.options.initData$ = BootstrapTree.options.listName$.switchMap(list_name => {
                let listNameStr = list_name.db_table_name;
                let liveSite = list_name.fabrik_live_site;
                if(listNameStr) {
                    return BootstrapTree.options.parentElement$.map(parent_element => {
                        let mergedData               = parent_element;
                            mergedData.db_table_name = listNameStr;
                            mergedData.liveSite = liveSite;
                        return mergedData;
                    });
                }
                
            });

            // Subscribe to get init data
            BootstrapTree.options.initData$.subscribe(init_data => {
                // If has an error throw an exception
                if(init_data.error || !Boolean(init_data.parent_element_id)) {
                    throw "Parent element not found";
                }
                BootstrapTree.options.db_table_name       = init_data.db_table_name;
                BootstrapTree.options.parent_element_id   = init_data.parent_element_id;
                BootstrapTree.options.parent_element_name = init_data.parent_element_name;
                BootstrapTree.options.liveSite = init_data.liveSite;
                console.log('live site updated to: ');
                console.log(BootstrapTree.options.liveSite);

                BootstrapTree.getElementsWithoutParent(
                    BootstrapTree.options.db_table_name, 
                    BootstrapTree.options.parent_element_name)
                    .subscribe(res => {
                        BootstrapTree.options.root_elements = BootstrapTree.buildElementsList(res);

                        // Pega todos os elementos da página que está navegando
                        var tableBody = jQuery('tbody.fabrik_groupdata > tr').not(".groupDataMsg");
                        BootstrapTree.processRoot(tableBody);
                    });

            });
            
        } catch (e) {
            console.log('Error on init bootstrap tree: ');
            console.log(e);
        }
        
    };
    
    return BootstrapTree;
});