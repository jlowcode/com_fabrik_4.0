/**
 * Tree-view auto-complete filter
 *
 * @copyright: Copyright (C) 2019-2020  Projeto PITT. - All rights reserved.
 * @license:   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

/*jshint mootools: true */
/*global fconsole:true, Joomla:true,  */

define(['jquery', 'fab/treeview-autocomplete-cdd', 'fab/treeview', 'lib/tree.jquery'], function (jQuery, FabCddAutocomplete, TreeView) {

    var TreeViewAutoComplete = new Class({

        Implements: [Options, Events],

        options: {
            menuclass: 'auto-complete-container dropdown',
            classes: {
                'ul': 'dropdown-menu',
                'li': 'result'
            },
            url: 'index.php',
            max: 10,
            onSelection: Class.empty,
            autoLoadSingleResult: true,
            minTriggerChars: 1,
            debounceDelay: 500,
            storeMatchedResultsOnly: false // Only store a value if selected from picklist
        },

        initialize: function (element, edivSelectedValues, edivTree, options) {
            var self = this;
            this.setOptions(options);
            this.options.dataTree = [];

            this.options.labelelement = typeOf(document.id(element + '-auto-complete')) === 'null' ?
                document.getElement(element + '-auto-complete') : document.id(element + '-auto-complete');
            if (this.options.labelelement) {
                this.options.labelelement.value = '';
            }
            this.options.labeldivSelected = document.getElement(edivSelectedValues);
            this.options.labeldivTree = edivTree;

            //if there is a refresh button in the filter
            var btnAddForm = jQuery(this.options.labeldivTree + '_popupformbtn');
            if(btnAddForm[0]){
                btnAddForm[0].addEventListener('click', function(){
                    var addUrl = window.location.origin + window.location.pathname + '?option=com_fabrik&view=form&tmpl=component&ajax=1&formid=' 
                            + self.options.popUpId + '&noredirect=1';
					window.open(addUrl, "", "top=300,width=800,height=600");
                });
            }

            //if there is a refresh button in the filter
            var btnRefresh = jQuery(this.options.labeldivTree + '_refreshbutton');
            if(btnRefresh[0]){
                btnRefresh[0].addEventListener('click', function(){
                    var tree = jQuery(self.options.labeldivTree).tree('getTree');
                    //fechar todos os nós da árvore primeiro
                    tree.iterate(function(node) {
                        if (node.hasChildren()) {
                            jQuery(self.options.labeldivTree).tree('closeNode', node, true);
                        }
                    });

                    //reloading tree
                    this.ajax = new Request({
                        url: self.options.url + '&method=treeview_options',
                        data: {
                            value: ''
                        },
                        onSuccess: function (e) {
                            let res = self.buildResultTree(JSON.parse(e));
                            jQuery(self.options.labeldivTree).tree('loadData', res);
                            self.addEventsTreeAfterRefreshBtn();
                        }.bind(this),
                        onFailure: function (xhr) {
                            this.ajax = null;
                            fconsole('PITT treeview autocomplete: Ajax failure: Code ' + xhr.status + ': ' + xhr.statusText);
                        }.bind(this)
                    }).send();
                });
            }

            options.url += '&method=autocomplete_options';
            options.labeldivSelected = this.options.labeldivSelected;

            new FabCddAutocomplete(this.options.labelelement, options);
            self.buildTreeRecursive();
        },

        buildTreeRecursive: function () {
            var self = this;

            var arrayChildren = [];
            JSON.parse(self.options.count).forEach(element => {
                if(self.options.rootCategory){
                    if(self.options.rootCategory == element.value || self.options.rootCategory == element.id){
                        element.children = [];
                        self.options.dataTree.push(element);
                    } else {
                        arrayChildren.push(element);
                    }
                } else {
                    if (!element.parent || element.parent == "0") {
                        if (!element.children) {
                            element.children = [{}];
                        }
    
                        self.options.dataTree.push(element);
                    }
                }

            })

            if(arrayChildren.length != 0){
                self.options.dataTree[0].children = arrayChildren;
            }

            function checkInArray(group) {
                ////(array_search(7, $user->groups) || array_search(8, $user->groups))
                return group == 7 || group == 8;
            }

            function handleStop(node, e) {
                if(self.options.dragndropProp){
                    //usa o plugin treeview recursivo
                    if(self.options.dragndropProp == 1){
                        if(self.options.userGroups.find(checkInArray)) {
                            var elemFromPoint = document.elementFromPoint(e.clientX, e.clientY);
                            if(elemFromPoint){
                                var tagA = elemFromPoint.closest("a");
                                if(tagA && tagA.getAttribute("data-rowid")){
                                    var htmlIdRow = tagA.getAttribute("data-rowid");
                                    var nodeId = node.value ? node.value : node.id;
                                    //busca a string do elemento que vai ser alterado no formulario 
                                    jQuery.ajax({
                                        url: self.options.url + '&method=formrow_options',
                                        data: {
                                            value: parseInt(htmlIdRow)
                                        }
                                    }).done(function (strRetorno) {
                                        if(strRetorno){
                                            //busca o formulario que vai ser alterado
                                            jQuery.ajax({
                                                type: "POST",
                                                url : "index.php?option=com_fabrik&view=form&Itemid=135&formid="+self.options.listId+"&rowid="+htmlIdRow+"&listid="+self.options.listId,
                                                context: document.body
                                            }).done(function (ret) {
                                                var formToModify = jQuery(ret).find(".fabrikForm");
                                                if(formToModify[0]){
                                                    //busca agora os elementos fileupload p/ adicionar no formulario
                                                    jQuery.ajax({
                                                        url: self.options.url + '&method=fileuploadelements_options',
                                                        data: {
                                                            value: parseInt(htmlIdRow)
                                                        }
                                                    }).done(function (fileUploadEl) {
                                                        JSON.parse(fileUploadEl).forEach(element => {
                                                            if(formToModify[0].getElementById(element.name)){
                                                                formToModify[0].getElementById(element.name).getParent().appendChild(new Element('input', {'type': 'hidden', 'name': element.name + '[id][' + element.strImg + ']' ,'value': element.value}));
                                                                formToModify[0].getElementById(element.name).getParent().appendChild(new Element('input', {'type': 'hidden', 'name': element.name + '[crop][' + element.strImg + ']' ,'value': element.param}));
                                                            }
                                                        })
        
                                                        var tagToModify = jQuery(formToModify[0]).find("#" + strRetorno);
                                                        if(tagToModify[0]){
                                                            var option = null;
                                                            if(/selected-checkbox-/.test(jQuery(tagToModify[0]).attr('class'))){
                                                                option = new Element('input', { 'type':'checkbox', 'value': nodeId, 'hidden': true, 'style': 'display: none', 'checked': 'checked', 'name': strRetorno + '[]'});
                                                            } else {
                                                                option = new Element('option', { 'value': nodeId, 'selected': 'selected'});
                                                            }
                                                            tagToModify[0].appendChild(option);
                                                            formToModify.css("display", "none");
                                                            formToModify.appendTo(jQuery(document).find('body'));
                                                            formToModify.submit();
                                                        }
                                                        
                                                    });
                                                } else {
                                                    alert("Error(2): Não foi possível atualizar o registro!");
                                                }
                                            });
                                        } else {
                                            //senao é treeview single
                                            //busca o formulario que vai ser alterado
                                            jQuery.ajax({
                                                type: "POST",
                                                url : "index.php?option=com_fabrik&view=form&Itemid=135&formid="+self.options.listId+"&rowid="+htmlIdRow+"&listid="+self.options.listId,
                                                context: document.body
                                            }).done(function (ret) {
                                                var formToModify = jQuery(ret).find(".fabrikForm");
                                                if(formToModify[0]){
                                                    //busca agora os elementos fileupload p/ adicionar no formulario
                                                    jQuery.ajax({
                                                        url: self.options.url + '&method=fileuploadelements_options',
                                                        data: {
                                                            value: parseInt(htmlIdRow)
                                                        }
                                                    }).done(function (fileUploadEl) {
                                                        JSON.parse(fileUploadEl).forEach(element => {
                                                            if(formToModify[0].getElementById(element.name)){
                                                                formToModify[0].getElementById(element.name).getParent().appendChild(new Element('input', {'type': 'hidden', 
                                                                'name': element.name + '[id][' + element.strImg + ']' ,'value': element.value}));
                                                                formToModify[0].getElementById(element.name).getParent().appendChild(new Element('input', {'type': 'hidden', 
                                                                'name': element.name + '[crop][' + element.strImg + ']' ,'value': element.param}));
                                                            }
                                                        })
                                                        var tagToModify = jQuery(formToModify[0]).find(".selected-checkbox-" + self.options.singleName);
                                                        if(tagToModify[0]){
                                                            if(jQuery(tagToModify[0]).find(">:first-child")[0]){
                                                                jQuery(tagToModify[0]).find(">:first-child")[0].remove();
                                                                var input = new Element('input', { 'type':'checkbox', 'value': nodeId, 'hidden': true, 'style': 'display: none', 'checked': 'checked', 'name': self.options.fullName + '[]'});
                                                                tagToModify[0].appendChild(input);
                                                                formToModify.css("display", "none");
                                                                formToModify.appendTo(jQuery(document).find('body'));
                                                                formToModify.submit();
                                                            } else {
                                                                var input = new Element('input', { 'type':'checkbox', 'value': nodeId, 'hidden': true, 'style': 'display: none', 'checked': 'checked', 'name': self.options.fullName + '[]'});
                                                                tagToModify[0].appendChild(input);
                                                                formToModify.css("display", "none");
                                                                formToModify.appendTo(jQuery(document).find('body'));
                                                                formToModify.submit();
                                                            }
                                                    
                                                        } else {
                                                            alert("Error(6): Não foi possível atualizar o registro!");
                                                        }
                                                        
                                                    });
                                                } else {
                                                    alert("Error(1): Não foi possível atualizar o registro!");
                                                }
                                            });
                                        }
                                    })
        
                                } else {
                                    alert("Error(3): Não foi possível atualizar o registro!");
                                }
                                
                            }
                        } else {
                            alert("Error(4): Desculpe, mas você não está autorizado a editar este registro.");
                        }
                    } else {
                        //normal
                        
                        if(self.options.userGroups.find(checkInArray)) {
                            var elemFromPoint = document.elementFromPoint(e.clientX, e.clientY);
                            if(elemFromPoint){
                                var tagA = elemFromPoint.closest("a");
                                if(tagA && tagA.getAttribute("data-rowid")){
                                    var htmlIdRow = tagA.getAttribute("data-rowid");
                                    var nodeId = node.value ? node.value : node.id;

                                    jQuery.ajax({
                                        type: "POST",
                                        url : "index.php?option=com_fabrik&view=form&Itemid=135&formid="+self.options.listId+"&rowid="+htmlIdRow+"&listid="+self.options.listId,
                                        context: document.body
                                    }).done(function (ret) {
                                        var formToModify = jQuery(ret).find(".fabrikForm");
                                        if(formToModify[0]){
                                            //busca agora os elementos fileupload p/ adicionar no formulario
                                            jQuery.ajax({
                                                url: self.options.url + '&method=fileuploadelements_options',
                                                data: {
                                                    value: parseInt(htmlIdRow)
                                                }
                                            }).done(function (fileUploadEl) {
                                                JSON.parse(fileUploadEl).forEach(element => {
                                                    if(formToModify[0].getElementById(element.name)){
                                                        formToModify[0].getElementById(element.name).getParent().appendChild(new Element('input', {'type': 'hidden', 'name': element.name + '[id][' + element.strImg + ']' ,'value': element.value}));
                                                        formToModify[0].getElementById(element.name).getParent().appendChild(new Element('input', {'type': 'hidden', 'name': element.name + '[crop][' + element.strImg + ']' ,'value': element.param}));
                                                    }
                                                })

                                                var tagToModify = jQuery(formToModify[0]).find("#" + self.options.fullName);
                                                if(tagToModify[0]){
                                                    var option = new Element('option', { 'value': nodeId, 'selected': 'selected'});
                                                    tagToModify[0].appendChild(option);
                                                    formToModify.css("display", "none");
                                                    formToModify.appendTo(jQuery(document).find('body'));
                                                    formToModify.submit();
                                                } else {
                                                    tagToModify = jQuery(formToModify[0]).find(".selected-checkbox-" + self.options.singleName);
                                                    console.log(tagToModify[0]);
                                                    if(tagToModify[0]){
                                                        if(jQuery(tagToModify[0]).find(">:first-child")[0]){
                                                            jQuery(tagToModify[0]).find(">:first-child")[0].remove();
                                                            var input = new Element('input', { 'type':'checkbox', 'value': nodeId, 'hidden': true, 'style': 'display: none', 'checked': 'checked', 'name': self.options.fullName + '[]'});
                                                            tagToModify[0].appendChild(input);
                                                            formToModify.css("display", "none");
                                                            formToModify.appendTo(jQuery(document).find('body'));
                                                            formToModify.submit();
                                                        } else {
                                                            var input = new Element('input', { 'type':'checkbox', 'value': nodeId, 'hidden': true, 'style': 'display: none', 'checked': 'checked', 'name': self.options.fullName + '[]'});
                                                            tagToModify[0].appendChild(input);
                                                            formToModify.css("display", "none");
                                                            formToModify.appendTo(jQuery(document).find('body'));
                                                            formToModify.submit();
                                                        }
                                                    
                                                    }
                                                }
                                                
                                            });
                                        } else {
                                            alert("Error(8): Não foi possível atualizar o registro!");
                                        }
                                    });
                                    

                                }
                            }
                        }
                    }
                } else {
                    alert("Error(7): O drag-and-drop não está ativado para este filtro");
                }
            }

            jQuery(this.options.labeldivTree).tree({
                data: self.options.dataTree,
                selectable: false,
                dragAndDrop: true,
                onDragStop: handleStop,
                onCanMoveTo: function(moved_node, target_node, position) {
                    if (target_node.is_menu){}
                },
                onCreateLi: function (node, $li, is_selected) {
                    if (typeof (node.counter) != "undefined" && node.counter != 0) {
                        $li.find('.jqtree-title')[0].innerHTML = $li.find('.jqtree-title')[0].innerHTML + " (" + node.counter + ") "
                    }
                }
            })

            if (self.options.buildMethod == 1) {
                self.buildEventTreeRecursive();
            } else {
                self.addEventsInTree();
            }

        },

        buildEventTreeRecursive: function () {
            var self = this;
            //Event when user open a node
            jQuery(self.options.labeldivTree).on('tree.open',
                function (e) {
                    let dataSub = [];
                    let idOpened = parseInt(e.node.value);
                    JSON.parse(self.options.count).forEach(element => {
                        if (!element.children) {
                            element.children = [{}];
                        }
                        if (parseInt(element.parent) == idOpened) {
                            dataSub.push(element);
                        }
                    })

                    jQuery(self.options.labeldivTree).tree('loadData', dataSub, e.node);
                }
            );

            // On click on a node adds it to tags
            jQuery(self.options.labeldivTree).on(
                'tree.click',
                function (event) {
                    var node = event.node;
                    if (!self.existsTag(node.value)) {
                        self.addTag(node.name, node.value, true);
                    }
                }
            );

            var btnRefresh = jQuery(this.options.labeldivTree + '_refreshbutton');
            if(btnRefresh[0]){
                // bind 'tree.contextmenu' event
                jQuery(self.options.labeldivTree).on(
                    'tree.contextmenu',
                    function(event) {
                        // The clicked node is 'event.node'
                        var node = event.node;
                        window.open(window.location.pathname + '?option=com_fabrik&view=form&tmpl=component&formid=' + self.options.popUpId + '&rowid=' + node.id, "", "top=300,width=800,height=600");
                    }
                );
            }
            
        },

        buildResultTree: function (res) {
            let self = this;
            res.forEach(function (node, indexRes) {
                if (node.children) {
                    node.children = [{}];
                }
            })

            return res;
        },

        buildTree: function () {
            let self = this;
            this.ajax = new Request({
                url: this.options.url + '&method=treeview_options',
                data: {
                    value: ''
                },
                onSuccess: function (e) {
                    let res = this.buildResultTree(JSON.parse(e));
                    //Se nos parametros o usuário selecionou como desc (1)
                    res = self.options.sortedBy == 1 ? res.sort(self.compareValues('counter', 'desc')) : res.sort(self.compareValues('counter', 'asc'));
                    jQuery(this.options.labeldivTree).tree({
                        data: res,
                        selectable: false,
                        onCreateLi: function (node, $li, is_selected) {
                            if (typeof (node.counter) != "undefined" && node.counter != 0) {
                                $li.find('.jqtree-title')[0].innerHTML = $li.find('.jqtree-title')[0].innerHTML + " (" + node.counter + ") "
                            }
                        }
                    })
                    self.addEventsInTree();
                }.bind(this),
                onFailure: function (xhr) {
                    this.ajax = null;
                    fconsole('PITT treeview autocomplete: Ajax failure: Code ' + xhr.status + ': ' + xhr.statusText);
                }.bind(this)
            }).send();
            self.addSelectedTags();
        },

        addSelectedTags: function () {
            let self = this;
            if (self.options.default && self.options.defaultLabel) {
                for (var i = 0; i < self.options.default.length; i++) {
                    if (self.options.default[i] && !self.existsTag(self.options.default[i])) {
                        self.addTag(self.options.defaultLabel[i], self.options.default[i], false);
                    }
                }
            }
        },

        removeTag: function (tag, flag) {
            let self = this;
            self.options.labeldivSelected.parentNode.removeChild(tag.container);
            if (flag) {
                self.options.labeldivSelected.removeChild(tag.input);
            } else {
                var byValue = document.querySelectorAll('input[value="' + tag.input.value + '"]');
                if (byValue[0]) {
                    self.options.labeldivSelected.removeChild(byValue[0]);
                }
            }

            var divFilteredTags = jQuery('.filteredTags')[0];
            if(divFilteredTags && jQuery(divFilteredTags).find("span[tag-value='" + tag.input.value + "']")[0]){
                jQuery(divFilteredTags).find("span[tag-value='" + tag.input.value + "']")[0].remove();
            }

            Fabrik.fireEvent('fabrik.list.dofilter', [this]);
        },

        existsTag: function (id) {
            if (this.options.labeldivSelected.hasChildNodes()) {
                for (let key of this.options.labeldivSelected.childNodes) {
                    if (key.value == id) return true;
                }
            }
            return false;
        },

        addTag: function (text, id, flag) {
            let self = this;

            let tag = {
                id: id,
                text: text,
                container: new Element('div.tag-container'),
                content: new Element('span.tag-content'),
                input: new Element('input.fabrikinput.fabrik_filter', { 'tree-input-filter': self.options.fullName, 'data-filter-name': self.options.fullName, 'type': 'checkbox', 'styles': { 'display': 'none' }, 'name': this.options.nameElement + "[" + Math.floor(Math.random() * 10) + "]", 'checked': true }),
                closeButton: new Element('span.tag-close-button')
            };

            tag.input.value = id;
            tag.content.textContent = text;
            tag.closeButton.textContent = 'x';

            if (flag)
                self.options.labeldivSelected.appendChild(tag.input);

            tag.container.appendChild(tag.content);
            tag.container.appendChild(tag.closeButton);

            if (flag) {
                tag.closeButton.addEventListener('click', function () {
                    self.removeTag(tag, true);
                }, false);
            } else {
                tag.closeButton.addEventListener('click', function () {
                    self.removeTag(tag, false);
                }, false);
            }

            var divFilteredEls = jQuery('.filteredTags')[0];
            if(divFilteredEls){
                jQuery('.filteredTags').append('<span tag-value="'+id+'" class="tagSearched">' + text + '</span>');
            }

            self.options.labeldivSelected.parentNode.insertBefore(tag.container, self.options.labeldivSelected);
            Fabrik.fireEvent('fabrik.list.dofilter', [this]);
        },

        addEventsTreeAfterRefreshBtn: function(){
            let self = this;

            // Make an AJAX request when open a tree branch
            jQuery(self.options.labeldivTree).on('tree.open', function (e) {
                    let idNode = parseInt(e.node.value) ? parseInt(e.node.value) : parseInt(e.node.id);
                    if (idNode) {
                        this.ajax = new Request({
                            url: self.options.url + '&method=treeview_options',
                            data: {
                                value: idNode
                            },
                            onSuccess: function (result) {
                                jQuery(self.options.labeldivTree).tree('loadData', JSON.parse(result), e.node);
                            }.bind(this),
                            onFailure: function (xhr) {
                                this.ajax = null;
                                fconsole('Fabrik autocomplete: Ajax failure: Code ' + xhr.status + ': ' + xhr.statusText);
                                e.node.children = [];
                            }.bind(this)
                        }).send();
                    } else {
                        fconsole('Fabrik autocomplete: Ajax failure: Code ' + xhr.status + ': ' + xhr.statusText);
                        e.node.children = false;
                    }

            });

            // On click on a node adds it to tags
            jQuery(self.options.labeldivTree).on('tree.click', function (event) {
                var node = event.node;
                var nodeId = node.value ? node.value : node.id;
                
                if (!self.existsTag(nodeId)) {
                    self.addTag(node.name, nodeId, true);
                }
            });

            //if there is a refresh button in the filter
            var btnAddForm = jQuery(this.options.labeldivTree + '_popupformbtn');
            if(btnAddForm[0]){
                btnAddForm[0].addEventListener('click', function(){
                    var addUrl = window.location.origin + window.location.pathname + '?option=com_fabrik&view=form&tmpl=component&ajax=1&formid=' 
                            + self.options.popUpId + '&noredirect=1';
					window.open(addUrl, "", "top=300,width=800,height=600");
                });
            }

            var btnRefresh = jQuery(this.options.labeldivTree + '_refreshbutton');
            if(btnRefresh[0]){
                // bind 'tree.contextmenu' event
                jQuery(self.options.labeldivTree).on(
                    'tree.contextmenu',
                    function(event) {
                        // The clicked node is 'event.node'
                        var node = event.node;
                        window.open(window.location.pathname + '?option=com_fabrik&view=form&tmpl=component&formid=' + self.options.popUpId + '&rowid=' + node.id, "", "top=300,width=800,height=600");
                    }
                );
            }
            
        },

        addEventsInTree: function () {
            let self = this;
            // Make an AJAX request when open a tree branch
            jQuery(self.options.labeldivTree).on('tree.open',
                function (e) {
                    let idNode = parseInt(e.node.value) ? parseInt(e.node.value) : parseInt(e.node.id);
                    if (idNode) {
                        if (self.options.buildMethod == 2) {
                            function checkNode(node, idNode) {
                                if (!node.children) {
                                    node.children = [{}];
                                }
                                return node.parent == idNode;
                            }
                            dataNode = JSON.parse(self.options.count).filter(node => checkNode(node, idNode));
                            if (dataNode.length != 0) {
                                jQuery(self.options.labeldivTree).tree('loadData', dataNode, e.node);
                            } else {
                                this.ajax = new Request({
                                    url: self.options.url + '&method=treeview_options',
                                    data: {
                                        value: idNode
                                    },
                                    onSuccess: function (result) {
                                        jQuery(self.options.labeldivTree).tree('loadData', JSON.parse(result), e.node);
                                    }.bind(this),
                                    onFailure: function (xhr) {
                                        this.ajax = null;
                                        fconsole('Fabrik autocomplete: Ajax failure: Code ' + xhr.status + ': ' + xhr.statusText);
                                        e.node.children = [];
                                    }.bind(this)
                                }).send();
                            }
                        } else {
                            this.ajax = new Request({
                                url: self.options.url + '&method=treeview_options',
                                data: {
                                    value: idNode
                                },
                                onSuccess: function (result) {
                                    jQuery(self.options.labeldivTree).tree('loadData', JSON.parse(result), e.node);
                                }.bind(this),
                                onFailure: function (xhr) {
                                    this.ajax = null;
                                    fconsole('Fabrik autocomplete: Ajax failure: Code ' + xhr.status + ': ' + xhr.statusText);
                                    e.node.children = [];
                                }.bind(this)
                            }).send();
                        }
                    } else {
                        fconsole('Fabrik autocomplete: Ajax failure: Code ' + xhr.status + ': ' + xhr.statusText);
                        e.node.children = false;
                    }

                }
            );

            // On click on a node adds it to tags
            jQuery(self.options.labeldivTree).on(
                'tree.click',
                function (event) {
                    var node = event.node;
                    var nodeId = node.value ? node.value : node.id;
                    if (!self.existsTag(nodeId)) {
                        self.addTag(node.name, nodeId, true);
                    }
                }
            );

            var btnRefresh = jQuery(this.options.labeldivTree + '_refreshbutton');
            if(btnRefresh[0]){
                // bind 'tree.contextmenu' event
                jQuery(self.options.labeldivTree).on(
                    'tree.contextmenu',
                    function(event) {
                        // The clicked node is 'event.node'
                        var node = event.node;
                        window.open(window.location.pathname + '?option=com_fabrik&view=form&tmpl=component&formid=' + self.options.popUpId + '&rowid=' + node.id, "", "top=300,width=800,height=600");
                    }
                );
            }
        },

        compareValues: function (key, order = 'asc') {
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

    });

    return TreeViewAutoComplete;
})