/*! Fabrik */

define(["jquery","fab/encoder","fab/fabrik","lib/debounce/jquery.ba-throttle-debounce"],(function(e,t,n,i){return new Class({Implements:[Options,Events],Binds:[],options:{menuclass:"auto-complete-container dropdown",classes:{ul:"dropdown-menu",li:"result"},url:"index.php",max:10,onSelection:Class.empty,autoLoadSingleResult:!0,minTriggerChars:1,debounceDelay:500,storeMatchedResultsOnly:!1},initialize:function(t,s){if(this.matchedResult=!1,this.setOptions(s),t=t.replace("-auto-complete",""),this.options.labelelement="null"===typeOf(document.id(t+"-auto-complete"))?document.getElement(t+"-auto-complete"):document.id(t+"-auto-complete"),this.cache={},this.selected=-1,this.mouseinsde=!1,document.addEvent("keydown",function(e){this.doWatchKeys(e)}.bind(this)),this.element="null"===typeOf(document.id(t))?document.getElement(t):document.id(t),this.buildMenu(),this.getInputElement()){this.getInputElement().setProperty("autocomplete","off");var l=this;e(this.getInputElement()).bind("keyup",i(this.options.debounceDelay,(function(e){l.search(e)}))),e(this.getInputElement()).bind("input",i(this.options.debounceDelay,(function(e){l.search(e)}))),this.getInputElement().addEvent("blur",function(e){this.options.storeMatchedResultsOnly&&(this.matchedResult||void 0!==this.data&&1===this.data.length&&this.options.autoLoadSingleResult||(this.element.value="")),""===this.getInputElement().get("value")&&(this.element.value="",n.fireEvent("fabrik.list.dofilter",[this]))}.bind(this))}else fconsole("autocomplete didn't find input element")},search:function(e){var t;if(this.isMinTriggerlength()){if(9===e.which||13===e.which)return e.preventDefault(),this.closeMenu(),this.ajax&&this.ajax.cancel(),void this.element.fireEvent("change",new Event.Mock(this.element,"change"),500);this.matchedResult=!1;var i=this.getInputElement().get("value");""===i&&(this.element.value=""),i!==this.searchText&&""!==i&&(!1===this.options.storeMatchedResultsOnly&&(this.element.value=i),this.positionMenu(),this.cache[i]?this.populateMenu(this.cache[i])&&this.openMenu():(this.ajax&&(this.closeMenu(),this.ajax.cancel()),this.ajax=new Request({url:this.options.url,data:{value:i},onRequest:function(){n.loader.start(this.getInputElement())}.bind(this),onCancel:function(){n.loader.stop(this.getInputElement()),this.ajax=null}.bind(this),onSuccess:function(e){if(n.loader.stop(this.getInputElement()),this.ajax=null,"null"===typeOf(e)){fconsole("Fabrik autocomplete: Ajax response empty");var s=n.getBlock(this.options.formRef).formElements.get(this.element.id);return t=Joomla.JText._("COM_FABRIK_AUTOCOMPLETE_AJAX_ERROR"),void s.setErrorMessage(t,"fabrikError",!0)}this.completeAjax(e,i)}.bind(this),onFailure:function(e){n.loader.stop(this.getInputElement()),this.ajax=null,fconsole("Fabrik autocomplete: Ajax failure: Code "+e.status+": "+e.statusText);var i=n.getBlock(this.options.formRef).formElements.get(this.element.id);t=Joomla.JText._("COM_FABRIK_AUTOCOMPLETE_AJAX_ERROR"),i.setErrorMessage(t,"fabrikError",!0)}.bind(this)}).send())),this.searchText=i}},completeAjax:function(e,t){e=JSON.parse(e),this.cache[t]=e,this.populateMenu(e)&&this.openMenu()},buildMenu:function(){this.menu=new Element("ul.dropdown-menu",{role:"menu",styles:{"z-index":1056}}),this.menu.inject(document.body),this.menu.addEvent("mouseenter",function(){this.mouseinsde=!0}.bind(this)),this.menu.addEvent("mouseleave",function(){this.mouseinsde=!1}.bind(this)),this.menu.addEvent("click:relay(a)",function(e,t){this.makeSelection(e,t)}.bind(this))},getInputElement:function(){return this.options.labelelement?this.options.labelelement:this.element},positionMenu:function(){var e=this.getInputElement().getCoordinates();this.menu.setStyles({left:e.left,top:e.top+e.height-1,width:e.width})},populateMenu:function(e){var i,s,l,o;e.map((function(e,n){return e.text=t.htmlDecode(e.text),e})),this.data=e;var h=this.getListMax(),a=this.menu;if(a.empty(),1===e.length&&this.options.autoLoadSingleResult)return this.element.value=e[0].value,this.getInputElement().value=e[0].text,this.closeMenu(),this.fireEvent("selection",[this,this.element.value]),!1!==(s=n.getBlock(this.options.formRef))&&(l=s.formElements.get(this.element.id).getBlurEvent(),this.element.fireEvent(l,new Event.Mock(this.element,l),700)),n.fireEvent("fabrik.autocomplete.selected",[this,this.element.value]),!1;0===e.length&&new Element("li").adopt(new Element("div.alert.alert-info").adopt(new Element("i").set("text",Joomla.JText._("COM_FABRIK_NO_AUTOCOMPLETE_RECORDS")))).inject(a);for(var u=0;u<h;u++)o=e[u],i=new Element("a",{href:"#","data-value":o.value,tabindex:"-1"}).set("text",o.text),new Element("li").adopt(i).inject(a);return e.length>this.options.max&&new Element("li").set("text","....").inject(a),!0},makeSelection:function(e,t){e.preventDefault(),"null"!==typeOf(t)?(this.getInputElement().value=t.get("text"),this.element.value=t.getProperty("data-value"),this.closeMenu(),this.fireEvent("selection",[this,this.element.value]),this.element.fireEvent("change",new Event.Mock(this.element,"change"),700),this.element.fireEvent("blur",new Event.Mock(this.element,"blur"),700),n.fireEvent("fabrik.list.dofilter",[this]),n.fireEvent("fabrik.autocomplete.selected",[this,this.element.value])):n.fireEvent("fabrik.autocomplete.notselected",[this,this.element.value])},closeMenu:function(){this.shown&&(this.shown=!1,e(this.menu).hide(),this.selected=-1,document.removeEvent("click",this.doCloseEvent))},openMenu:function(){this.shown||this.isMinTriggerlength()&&(this.menu.show(),this.shown=!0,this.doCloseEvent=this.doTestMenuClose.bind(this),document.addEvent("click",this.doCloseEvent),this.selected=0,this.highlight())},isMinTriggerlength:function(){return this.getInputElement().get("value").length>=this.options.minTriggerChars},doTestMenuClose:function(){this.mouseinsde||this.closeMenu()},getListMax:function(){return"null"===typeOf(this.data)?0:this.data.length>this.options.max?this.options.max:this.data.length},doWatchKeys:function(e){if(document.activeElement===this.getInputElement()){var t,n,i=this.getListMax();if(this.shown)if(this.isMinTriggerlength())switch("enter"!==e.key&&"tab"!==e.key||window.fireEvent("blur"),e.code){case 40:this.shown||this.openMenu(),this.selected+1<=i&&this.selected++,this.highlight(),e.stop();break;case 38:this.selected-1>=-1&&(this.selected--,this.highlight()),e.stop();break;case 13:case 9:e.stop(),(t=this.getSelected())&&(n=new Event.Mock(t,"click"),this.makeSelection(n,t),this.closeMenu());break;case 27:e.stop(),this.closeMenu()}else e.stop(),this.closeMenu();else 13===e.code.toInt()&&e.stop(),40===e.code.toInt()&&this.openMenu()}},getSelected:function(){var e=this.menu.getElements("li"),t=e.filter(function(e,t){return t===this.selected}.bind(this));return"element"===typeOf(t[0])?t[0].getElement("a"):e.length>0&&e[0].getElement("a")},highlight:function(){this.matchedResult=!0,this.menu.getElements("li").each(function(e,t){t===this.selected?e.addClass("selected").addClass("active"):e.removeClass("selected").removeClass("active")}.bind(this))}})}));