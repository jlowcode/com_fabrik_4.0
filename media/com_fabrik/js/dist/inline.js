/*! Fabrik */
var inline=new Class({Implements:Options,options:{},initialize:function(e,t){this.setOptions(t),document.addEvent("dblclick:relay("+e+")",function(e,t){var i;t.hide(),t.store("origValue",t.get("text")),t.retrieve("inline")?i=t.retrieve("inline"):((i=new Element("input")).addEvent("keydown",function(e){this.checkKey(e,t)}.bind(this)),i.inject(t,"after").focus(),i.hide(),t.store("inline",i)),i.set("value",t.get("text")).toggle().focus(),i.select()}.bind(this))},checkKey:function(e,t){"enter"!==e.key&&"esc"!==e.key&&"tab"!==e.key||(t.retrieve("inline").hide(),t.show()),"enter"!==e.key&&"tab"!==e.key||(t.set("text",e.target.get("value")),Fabrik.fireEvent("fabrik.inline.save",[t,e]))}});
//# sourceMappingURL=inline.js.map