/*! Fabrik */

define(["jquery","fab/element"],function(jQuery,FbElement){return window.FbElementList=new Class({Extends:FbElement,type:"text",initialize:function(t,e){this.parent(t,e),this.addSubClickEvents(),this._getSubElements(),!0===this.options.allowadd&&!1!==this.options.editable&&(this.watchAddToggle(),this.watchAdd())},_getSubElements:function(){var t=this.getElement();return this.subElements=t?t.getElements("input[type="+this.type+"]"):[],this.subElements},addSubClickEvents:function(){this._getSubElements().each(function(t){t.addEvent("click",function(t){Fabrik.fireEvent("fabrik.element.click",[this,t])})})},eventDelegate:function(){return"input[type="+this.type+"][name^="+this.options.fullName+"]"},checkEventAction:function(t){return t},addNewEvent:function(action,js){var r,delegate,uid,c;action=this.checkEventAction(action),"load"===action?(this.loadEvents.push(js),this.runLoadEvent(js)):(c=this.form.form,delegate=this.eventDelegate(),"null"===typeOf(this.form.events[action])&&(this.form.events[action]={}),uid="function"==typeof js?1e3*Math.random(100):(r=new RegExp("[^a-z|0-9]","gi"),delegate+js.replace(r,"")),"null"===typeOf(this.form.events[action][uid])&&(this.form.events[action][uid]=!0,jQuery(c).on(action,delegate,function(event){event.stopPropagation();var target=jQuery(event.currentTarget),elid,that,subEls;if("LABEL"===target.prop("tagName")){var for_id=target.prop("for");target=for_id?jQuery("#"+for_id):target.find("input")}elid=target.closest(".fabrikSubElementContainer").prop("id"),that=this.form.formElements[elid],subEls=that._getSubElements(),0<target.length&&subEls.contains(target[0])&&("function"!=typeof js?(js=js.replace(/\bthis\b/g,"that"),eval(js)):js.delay(0))}.bind(this))))},checkEnter:function(t){"enter"===t.key&&(t.stop(),this.startAddNewOption())},startAddNewOption:function(){var t,e=this.getContainer(),n=e.getElement("input[name=addPicklistLabel]"),i=e.getElement("input[name=addPicklistValue]"),s=n.value;if(""===(t=i?i.value:s)||""===s)window.alert(Joomla.JText._("PLG_ELEMENT_CHECKBOX_ENTER_VALUE_LABEL"));else{var a=this.subElements.getLast().findClassUp("fabrikgrid_"+this.type).clone(),l=a.getElement("input");if(l.value=t,l.checked="checked","checkbox"===this.type){var o=l.name.replace(/^(.*)\[.*\](.*?)$/,"$1$2");l.name=o+"["+this.subElements.length+"]"}a.getElement("."+this.type+" span").set("text",s),a.inject(this.subElements.getLast().findClassUp("fabrikgrid_"+this.type),"after");var r=0;"radio"===this.type&&(r=this.subElements.length);var d=$$("input[name="+l.name+"]");document.id(this.form.form).fireEvent("change",{target:d[r]}),this._getSubElements(),i&&(i.value=""),n.value="",this.addNewOption(t,s),this.mySlider&&this.mySlider.toggle()}},watchAdd:function(){if(!0===this.options.allowadd&&!1!==this.options.editable){var t=this.getContainer();t.getElements("input[name=addPicklistLabel], input[name=addPicklistValue]").addEvent("keypress",function(t){this.checkEnter(t)}.bind(this)),t.getElement("input[type=button]").addEvent("click",function(t){t.stop(),this.startAddNewOption()}.bind(this)),document.addEvent("keypress",function(t){"esc"===t.key&&this.mySlider&&this.mySlider.slideOut()}.bind(this))}},getFocusEvent:function(){return"click"}}),window.FbElementList});