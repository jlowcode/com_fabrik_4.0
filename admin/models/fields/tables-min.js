/*! Fabrik */
var tablesElement=new Class({Implements:[Options,Events],options:{conn:null},initialize:function(t,e){this.el=t,this.setOptions(e),"null"===typeOf(document.id(this.options.conn))?this.periodical=this.getCnn.periodical(500,this):this.setUp()},cloned:function(){},getCnn:function(){"null"!==typeOf(document.id(this.options.conn))&&(this.setUp(),clearInterval(this.periodical))},setUp:function(){this.el=document.id(this.el),this.cnn=document.id(this.options.conn),this.loader=document.id(this.el.id+"_loader"),this.cnn.addEvent("change",function(t){this.updateMe()}.bind(this));var t=this.cnn.get("value");""!==t&&-1!==t&&this.updateMe()},updateMe:function(t){t&&t.stop(),this.loader&&this.loader.show();t=this.cnn.get("value"),t=new Request({url:"index.php",data:{option:"com_fabrik",format:"raw",task:"plugin.pluginAjax",g:"element",plugin:"field",method:"ajax_tables",cid:t.toInt()},onComplete:function(t){t=JSON.parse(t);"null"!==typeOf(t)&&(t.err?alert(t.err):(this.el.empty(),t.each(function(t){var e={value:t};t===this.options.value&&(e.selected="selected"),this.loader&&this.loader.hide(!0),new Element("option",e).set("text",t).inject(this.el)}.bind(this))))}.bind(this),onFailure:function(t){this.el.empty(),this.loader&&this.loader.hide(),alert(t.status+": "+t.statusText)}.bind(this)});Fabrik.requestQueue.add(t)}});
//# sourceMappingURL=tables-min.js.map
