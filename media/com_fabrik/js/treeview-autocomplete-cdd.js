/**
 * Created by rob on 18/03/2016.
 */
define(['jquery', 'fab/autocomplete-bootstrap', 'fab/fabrik', 'lib/debounce/jquery.ba-throttle-debounce', 'fab/list'],

    function (jQuery, AutoComplete, Fabrik, debounce, FbList) {
        var FabCddTreeviewAutocomplete = new Class({

            Extends: AutoComplete,

            initialize: function (element, options) {
                // not sure why we use domready, but causes issues in popups on second+ open, doesn't fire
                //window.addEvent('domready', function () {
                this.matchedResult = false;
                this.setOptions(options);
                this.options.labelelement = typeOf(document.id(element + '-auto-complete')) === 'null' ?
                    document.getElement(element + '-auto-complete') : document.id(element + '-auto-complete');
                this.cache = {};
                this.selected = -1;
                this.mouseinsde = false;
                document.addEvent('keydown', function (e) {
                    this.doWatchKeys(e);
                }.bind(this));
                this.element = element;
                this.buildMenu();
                if (!this.getInputElement()) {
                    fconsole('autocomplete didn\'t find input element');
                    return;
                }
                this.getInputElement().setProperty('autocomplete', 'off');

                /**
                 * Using a 3rd party jQuery lib to 'debounce' the input, so the search doesn't fire until
                 * the user has stopped typing for more than X ms.  Don't use on() here, use bind(), otherwise
                 * we get multiple events when form popups are opened multiple times.
                 */
                var self = this;
                jQuery(this.getInputElement()).bind('keyup', debounce(this.options.debounceDelay, function (e) {
                    self.search(e);
                }));

                this.getInputElement().addEvent('blur', function (e) {
                    if (this.options.storeMatchedResultsOnly) {
                        if (!this.matchedResult) {
                            if (typeof (this.data) === 'undefined' ||
                                !(this.data.length === 1 && this.options.autoLoadSingleResult)) {
                                this.element.value = '';
                            }
                        }
                    }
                    Fabrik.fireEvent('fabrik.list.watchfilters', [this]);
                }.bind(this));

                Fabrik.fireEvent('fabrik.list.watchfilters', [this]);

                this.addSelectedTags();
                //}.bind(this));
            },

            addSelectedTags: function () {
                let self = this;
                if(self.options.defaultLabel.length != 0){
                    if (self.options.default && self.options.defaultLabel) {
                        for (var i = 0; i < self.options.default.length; i++) {
                            if (self.options.default[i]) {
                                self.addTag(self.options.defaultLabel[i], self.options.default[i], false);
                            }
                        }
                    }
                }
            },

            makeSelection: function (e, li) {
                let self = this;
                if (!self.existsTag(li.getProperty('data-value'))) {
                    this.addTag(li.get('text'), li.getProperty('data-value'), true);
                    this.getInputElement().fireEvent('blur', new Event.Mock(this.element, 'blur'), 0);
                    Fabrik.fireEvent('fabrik.list.dofilter', [this]);
                    this.closeMenu();
                    this.element.value = '';
                }
            },

            addTag: function (text, id, flag) {
                let self = this;

                let tag = {
                    id: id,
                    text: text,
                    container: new Element('div.tag-container'),
                    content: new Element('span.tag-content'),
                    input: new Element('input.fabrikinput.fabrik_filter', { 'tree-input-filter': self.options.fullName, 'data-filter-name': self.options.fullName, 'type': 'checkbox', 'styles': { 'display': 'none' }, 'name': this.options.nameElement + "[" + Math.floor(Math.random() * 10) + "]"}),
                    closeButton: new Element('span.tag-close-button')
                };

                tag.input.value = id;
                tag.content.textContent = text;
                tag.closeButton.textContent = 'x';

                if (flag)
                    self.options.labeldivSelected.appendChild(tag.input);

                tag.input.checked = true;
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
            },

            existsTag: function (id) {
                if (this.options.labeldivSelected.hasChildNodes()) {
                    for (let key of this.options.labeldivSelected.childNodes) {
                        if (key.value == id) return true;
                    }
                }
                return false;
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
        })
        return FabCddTreeviewAutocomplete;
    })