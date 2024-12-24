/*! Fabrik */
define(["jquery"],function(jQuery){var ListForm=new Class({autoChangeDbName:!0,Implements:[Options],initialize:function(e){var t;window.addEvent("domready",function(){this.setOptions(e),this.watchTableDd(),this.watchLabel(),document.id("addAJoin")&&document.id("addAJoin").addEvent("click",function(e){e.stop(),this.addJoin()}.bind(this)),document.getElement("table.linkedLists")&&(t=document.getElement("table.linkedLists").getElement("tbody"),new Sortables(t,{handle:".handle",onSort:function(e,t){var n=this.serialize(1,function(e){return e.getElement("input")?e.getElement("input").name.split("][").getLast().replace("]",""):""}),a=[];n.each(function(e){""!==e&&a.push(e)}),document.getElement("input[name*=faceted_list_order]").value=JSON.stringify(a)}})),document.getElement("table.linkedForms")&&(t=document.getElement("table.linkedForms").getElement("tbody"),new Sortables(t,{handle:".handle",onSort:function(e,t){var n=this.serialize(1,function(e){return e.getElement("input")?e.getElement("input").name.split("][").getLast().replace("]",""):""}),a=[];n.each(function(e){""!==e&&a.push(e)}),document.getElement("input[name*=faceted_form_order]").value=JSON.stringify(a)}})),this.joinCounter=0,this.watchOrderButtons(),this.watchDbName(),this.watchJoins()}.bind(this))},watchLabel:function(){this.autoChangeDbName=""===jQuery("#jform__database_name").val(),jQuery("#jform_label").on("keyup",function(e){var t;this.autoChangeDbName&&(t=(t=jQuery("#jform_label").val().trim().toLowerCase()).replace(/\W+/g,"_"),jQuery("#jform__database_name").val(t))}.bind(this)),jQuery("#jform__database_name").on("keyup",function(){this.autoChangeDbName=!1}.bind(this))},watchOrderButtons:function(){document.getElements(".addOrder").removeEvents("click"),document.getElements(".deleteOrder").removeEvents("click"),document.getElements(".addOrder").addEvent("click",function(e){e.stop(),this.addOrderBy()}.bind(this)),document.getElements(".deleteOrder").addEvent("click",function(e){e.stop(),this.deleteOrderBy(e)}.bind(this))},addOrderBy:function(e){e=e?e.target.getParent(".orderby_container"):document.getElement(".orderby_container");e.clone().inject(e,"after"),this.watchOrderButtons()},deleteOrderBy:function(e){1<document.getElements(".orderby_container").length&&(e.target.getParent(".orderby_container").dispose(),this.watchOrderButtons())},watchDbName:function(){document.id("database_name")&&document.id("database_name").addEvent("blur",function(e){""===document.id("database_name").get("value")?document.id("tablename").disabled=!1:document.id("tablename").disabled=!0})},_buildOptions:function(e,t){var n=[];return 0<e.length&&("object"==typeof e[0]?e.each(function(e){e[0]===t?n.push(new Element("option",{value:e[0],selected:"selected"}).set("text",e[1])):n.push(new Element("option",{value:e[0]}).set("text",e[1]))}):e.each(function(e){n.push((e===t?new Element("option",{value:e,selected:"selected"}):new Element("option",{value:e})).set("text",e))})),n},watchTableDd:function(){document.id("tablename")&&document.id("tablename").addEvent("change",function(e){var cid=document.getElement("input[name*=connection_id]").get("value"),table=document.id("tablename").get("value"),url="index.php?option=com_fabrik&format=raw&task=list.ajax_updateColumDropDowns&cid="+cid+"&table="+table,myAjax=new Request({url:url,method:"post",onComplete:function(r){eval(r)}}).send()})},watchFieldList:function(e){document.getElement("div[id^=table-sliders-data]").addEvent("change:relay(select[name*="+e+"])",function(e,t){this.updateJoinStatement(t.getParent("tr").id.replace("join",""))}.bind(this))},_findActiveTables:function(){document.getElements(".join_from").combine(document.getElements(".join_to")).each(function(e){e=e.get("value");-1===this.options.activetableOpts.indexOf(e)&&this.options.activetableOpts.push(e)}.bind(this)),this.options.activetableOpts.sort()},addJoin:function(e,t,n,a,i,o,d,l,s,m){n=n||"left",d=d||"",a=a||"",i=i||"",o=o||"",e=e||"",t=t||"",(m=m||!1)?(u='checked="checked"',c=""):(c='checked="checked"',u=""),this._findActiveTables(),l=l||[["-",""]],s=s||[["-",""]];var m=new Element("tbody"),t=new Element("input",{readonly:"readonly",size:"2",class:"disabled readonly input-mini",name:"jform[params][join_id][]",value:t}),r=new Element("a",{href:"#",class:"btn btn-danger btn-sm",events:{click:function(e){return this.deleteJoin(e),!1}.bind(this)}}),d=(r.set("html",'<i class="icon-minus"></i> '),n=new Element("select",{name:"jform[params][join_type][]",class:"form-select-sm inputbox"}).adopt(this._buildOptions(this.options.joinOpts,n)),new Element("select",{name:"jform[params][join_from_table][]",class:"form-select-sm join_from inputbox"}).adopt(this._buildOptions(this.options.activetableOpts,d))),a=(e=new Element("input",{type:"hidden",name:"group_id[]",value:e}),new Element("select",{name:"jform[params][table_join][]",class:"form-select-sm join_to inputbox"}).adopt(this._buildOptions(this.options.tableOpts,a))),l=new Element("select",{name:"jform[params][table_key][]",class:"table_key inputbox form-select-sm"}).adopt(this._buildOptions(l,i)),i=(o=new Element("select",{name:"jform[params][table_join_key][]",class:"table_join_key inputbox form-select-sm"}).adopt(this._buildOptions(s,o)),'<fieldset class="radio"><input type="radio" id="joinrepeatno'+this.joinCounter+'" value="0" name="jform[params][join_repeat]['+this.joinCounter+'][]" '+c+'/><label style="padding: 0.1rem 0.6rem 0.1rem 0.2rem" for="joinrepeatno'+this.joinCounter+'">'+Joomla.JText._("JNO")+'</label><input type="radio" id="joinrepeat'+this.joinCounter+'" value="1" name="jform[params][join_repeat]['+this.joinCounter+'][]" '+u+'/><label style="padding: 0.1rem 0.6rem 0.1rem 0.2rem" for="joinrepeat'+this.joinCounter+'">'+Joomla.JText._("JYES")+"</label></fieldset>"),s=new Element("thead").adopt(new Element("tr").adopt([new Element("th").set("text","id"),new Element("th").set("text",Joomla.JText._("COM_FABRIK_JOIN_TYPE")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_FROM")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_TO")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_FROM_COLUMN")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_TO_COLUMN")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_REPEAT_GROUP_BUTTON_LABEL")),new Element("th")])),c=new Element("tr",{id:"join"+this.joinCounter}).adopt([new Element("td").adopt(t),new Element("td").adopt([e,n]),new Element("td").adopt(d),new Element("td").adopt(a),new Element("td.table_key").adopt(l),new Element("td.table_join_key").adopt(o),new Element("td").set("html",i),new Element("td").adopt(r)]),u=new Element("table",{class:"table-striped table",id:""}).adopt([s,m.adopt(c)]);0===this.joinCounter?u.inject(document.id("joindtd")):(t=document.id("joindtd").getElement("tbody"),c.inject(t)),this.joinCounter++},deleteJoin:function(e){var t;e.stop(),t=e.target.getParent("tr"),e=e.target.getParent("table"),t.dispose(),0===e.getElements("tbody tr").length&&e.dispose()},watchJoins:function(){document.getElement("div[id^=table-sliders-data]").addEvent("change:relay(.join_from)",function(e,t){var n=t.getParent("tr"),a=n.id.replace("join",""),a=(this.updateJoinStatement(a),t.get("value")),t=document.getElement("input[name*=connection_id]").get("value"),n=n.getElement("td.table_key");new Request.HTML({url:"index.php?option=com_fabrik&format=raw&task=list.ajax_loadTableDropDown&table="+a+"&conn="+t,method:"post",update:n}).send()}.bind(this)),document.getElement("div[id^=table-sliders-data]").addEvent("change:relay(.join_to)",function(e,t){var n=t.getParent("tr"),a=n.id.replace("join","");this.updateJoinStatement(a);a="index.php?name=jform[params][table_join_key][]&option=com_fabrik&format=raw&task=list.ajax_loadTableDropDown&table="+t.get("value")+"&conn="+document.getElement("input[name*=connection_id]").get("value"),t=n.getElement("td.table_join_key");new Request.HTML({url:a,method:"post",update:t}).send()}.bind(this)),this.watchFieldList("join_type"),this.watchFieldList("table_join_key"),this.watchFieldList("table_key")},updateJoinStatement:function(e){var t=document.getElements("#join"+e+" .inputbox"),n=(t=Array.mfrom(t))[0].get("value"),a=t[1].get("value"),i=t[2].get("value"),n=n+" JOIN "+i+" ON "+a+"."+t[3].get("value")+" = "+i+"."+t[4].get("value"),a=document.id("join-desc-"+e);"null"!==typeOf(a)&&a.set("html",n)}});return ListForm});
//# sourceMappingURL=listform-min.js.map
