/**
 * Treeview filter
 *
 * @copyright: Copyright (C) 2019-2020  Projeto PITT. - All rights reserved.
 * @license:   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

/*jshint mootools: true */
/*global fconsole:true, Joomla:true,  */

define(['jquery', 'lib/tree.jquery'], function (jQuery) {

    var TreeView = new Class({
        Implements: [Options, Events],

        options: {
            typeFilter: ''
        },

        initialize: function (element, elementSelectedTree, elementTree, options) {
            var self = this;
            
            this.setOptions(options);
            this.options.dataTree = [];
            this.options.labeldivTree = elementTree;
            this.options.labeldivSelected = document.getElement(elementSelectedTree);

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

            this.buildTreeRecursive();
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
        },

        addEventsTreeAfterRefreshBtn: function(){
            let self = this;

            // Make an AJAX request when open a tree branch
            jQuery(self.options.labeldivTree).on('tree.open', function (e) {
                    let idNode = parseInt(e.node.value) ? parseInt(e.node.value) : parseInt(e.node.id);
                    if (self.options.typeFilter == 'datetree') idNode = e.node.name;
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

            self.options.dataTree = self.options.sortedBy == 1 ? 
            self.options.dataTree.sort(self.compareValues('counter', 'desc')) : 
            self.options.dataTree.sort(self.compareValues('counter', 'asc'));

            function checkInArray(group) {
                return group == 7 || group == 8;
            }

            function handleStop(node, e) {
                if(self.options.dragndropProp){
                    //usa o plugin treeview recursivo
                    if(self.options.dragndropProp == 1){
                        if(self.options.userGroups.find(checkInArray)){
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
                                                    alert("Error(1): Não foi possível atualizar o registro!");
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
                                                    var option = null;
                                                    if(/selected-checkbox-/.test(jQuery(tagToModify[0]).attr('class'))){
                                                        option = new Element('input', { 'type':'checkbox', 'value': nodeId, 'hidden': true, 'style': 'display: none', 'checked': 'checked', 'name': self.options.fullName + '[]'});
                                                    } else {
                                                        option = new Element('option', { 'value': nodeId, 'selected': 'selected'});
                                                    }
                                                    tagToModify[0].appendChild(option);
                                                    formToModify.css("display", "none");
                                                    formToModify.appendTo(jQuery(document).find('body'));
                                                    formToModify.submit();
                                                } else {
                                                    tagToModify = jQuery(formToModify[0]).find(".selected-checkbox-" + self.options.singleName);
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
                    alert("Error(6): O drag-and-drop não está ativado para este filtro");
                }
            }

            jQuery(this.options.labeldivTree).tree({
                data: self.options.dataTree,
                selectable: false,
                dragAndDrop: true,
                onDragStop: handleStop,
                onCanMoveTo: function(moved_node, target_node, position) {
                    if (target_node.is_menu) { }
                },
                onCreateLi: function (node, $li, is_selected) {
                    if (typeof (node.counter) != "undefined" && node.counter != 0) {
                        $li.find('.jqtree-title')[0].innerHTML = $li.find('.jqtree-title')[0].innerHTML + " (" + node.counter + ") "
                    }
                }
            })

            // DateTree filter is made with recorded data, but works with AJAX
            if (self.options.buildMethod == 1 && self.options.typeFilter != 'datetree') {
                self.buildEventTreeRecursive();
            } else {
                self.addEventsInTree();
            }

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
                            if (node.counter) {
                                $li.find('.jqtree-title').after('&nbsp<span>(' + node.counter + ')</span>');
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
        },

        
        buildResultTree: function (res) {
            res.forEach(node => {
                if (node.children) {
                    node.children = [{}];
                }

                //If the administrator parameter Order by is 'Count'
                JSON.parse(this.options.count).forEach(element => {
                    if (element.value == node.id) {
                        node.counter = element.counter;
                        return;
                    }
                });

            });
            return res;
        },

        compareValues: function (key, order = 'asc') {
            return function (a, b) {
                if (!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
                    return 0;
                }

                const varA = typeof(a[key]) == 'string' ? parseInt(a[key]) : a[key];
                const varB = typeof(b[key]) == 'string' ? parseInt(b[key]) : b[key];

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
        },

        removeTag: function (tag) {
            let self = this;
            self.options.labeldivSelected.parentNode.removeChild(tag.container);
            self.options.labeldivSelected.removeChild(tag.input);
            
            //remove the top tag near to clear filters button
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

        existsTagDateTree: function () {            
            if (this.options.labeldivSelected.childNodes.length > 1 && this.options.typeFilter == 'datetree') 
            {
                let elementPrev = this.options.labeldivSelected.previousElementSibling;

                if (elementPrev != null) {
                    elementPrev.style.background = "#ff000021";
                    setTimeout(function() { elementPrev.style.removeProperty("background")} , 500);
                }
                return true;
            }
            return false;
        },

        addTag: function (text, id) {
            let self  = this,
                dates = null;
            var id = id.toString();
            
            if (self.options.typeFilter == 'datetree') dates = id.split(' - ');

            let tag = {
                id: id,
                text: text,
                container: new Element('div.tag-container'),
                content: new Element('span.tag-content'),
                input: new Element('input.fabrikinput.fabrik_filter', { 'tree-input-filter': self.options.fullName, 'data-filter-name': self.options.fullName, 'type': 'checkbox', 'styles': { 'display': 'none' }, 'name': this.options.nameElement + "[" + Math.floor(Math.random() * 10) + "]", 'checked': true }),
                closeButton: new Element('span.tag-close-button')
            };

            tag.input.value = (dates !== null) ? dates[0] : id;
            tag.content.textContent = (self.options.typeFilter == 'treedate' && isNaN(text.split(' - ')[0])) ? text + '/' + id.substr(0, 4) : text;
            tag.closeButton.textContent = 'x';

            self.options.labeldivSelected.appendChild(tag.input);

            // DateTree filter will consist of 2 'values' but only 1 tag
            if (self.options.typeFilter != 'datetree') {
                tag.container.appendChild(tag.content);
                tag.container.appendChild(tag.closeButton);

                tag.closeButton.addEventListener('click', function () {
                    let ano  = (text.split(' - ').length == 2) ? text.split(' - ') : id.split('/'),
                        type = self.options.typeFilter;
                        
                    if (type == 'datetree') {
                        let elements = document.querySelectorAll("input[tree-input-filter=" + self.options.fullName + "][value^='" + ano[0] + "']:not([value='"+id+"'])");
                        elements.forEach(element => {
                            element.remove();
                        });
                    }
                    self.removeTag(tag);
                }, false);

                //add a second tag near to clear filters button
                var divFilteredEls = jQuery('.filteredTags')[0];
                if(divFilteredEls){
                    jQuery('.filteredTags').append('<span tag-value="'+id+'" class="tagSearched">' + text + '</span>');
                }
            } else {
                document.getElementsByClassName
                tag.container = new Element('div', {'styles': { 'display': 'none' } });
            }

            self.options.labeldivSelected.parentNode.insertBefore(tag.container, self.options.labeldivSelected);
            
            if (dates != null) {
                self.options.typeFilter = 'treedate';
                self.addTag(text, dates[1]);
            } else if (self.options.typeFilter == 'treedate') {
                self.options.typeFilter = 'datetree';
            }
            
            Fabrik.fireEvent('fabrik.list.dofilter', [this]);
        },

        addEventsInTree: function () {
            let self = this;
            // Make an AJAX request when open a tree branch
            jQuery(self.options.labeldivTree).on('tree.open',
                function (e) {
                    let idNode = parseInt(e.node.value) ? parseInt(e.node.value) : parseInt(e.node.id);
                    if (self.options.typeFilter == 'datetree') idNode = e.node.name;
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
                    
                }
            );

            // On click on a node adds it to tags
            jQuery(self.options.labeldivTree).on(
                'tree.click',
                function (event) {
                    var node = event.node;
                    var nodeId = node.value ? node.value : node.id;
                    if (!self.existsTagDateTree() && !self.existsTag(nodeId)) {
                        self.addTag(node.name, nodeId, true);
                    }
                }
            );
        },
    })

    return TreeView;
})