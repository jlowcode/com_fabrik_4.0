/*! Fabrik */
define(["jquery"],function(e){return new Class({Implements:[Options],initialize:function(e,t,n){this.el=document.id(e),this.fields=t,this.setOptions(n),this.filters=[],this.counter=0},addHeadings:function(){new Element("thead").adopt(new Element("tr",{id:"filterTh",class:"title"}).adopt(new Element("th").set("text",Joomla.JText._("COM_FABRIK_JOIN")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_FIELD")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_CONDITION")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_VALUE")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_TYPE")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_APPLY_FILTER_TO")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_GROUPED")),new Element("th").set("text",Joomla.JText._("COM_FABRIK_DELETE")))).inject(document.id("filterContainer"),"before")},deleteFilterOption:function(e){this.counter--,e.stop(),e.target.id.replace("filterContainer-del-","").toInt();var t=e.target.getParent("tr"),e=e.target.getParent("table");0===this.counter&&e.hide(),t.getElements("input, select, textarea").dispose(),t.hide()},_makeSel:function(e,t,n,l,a){var i=[];return(a=!0===a)&&i.push(new Element("option",{value:""}).set("text",Joomla.JText._("COM_FABRIK_PLEASE_SELECT"))),n.each(function(e){e.value===l?i.push(new Element("option",{value:e.value,selected:"selected"}).set("text",e.label)):i.push(new Element("option",{value:e.value}).set("text",e.label))}),new Element("select",{class:e,name:t}).adopt(i)},addFilterOption:function(e,t,n,l,a,i,o){this.counter<=0&&(this.el.getParent("table").getElement("thead")||this.addHeadings()),e=e||"",t=t||"",n=n||"",l=l||"",a=a||"",o=o||"";var s,d,m,r=this.options.filterCondDd,p=new Element("tr"),h=0===this.counter?new Element("span").set("text","WHERE").adopt(new Element("input",{type:"hidden",id:"paramsfilter-join",class:"form-control inputbox",name:"jform[params][filter-join][]",value:e})):(c=("AND"===e?(h=new Element("option",{value:"AND",selected:"selected"}).set("text","AND"),new Element("option",{value:"OR"})):(h=new Element("option",{value:"AND"}).set("text","AND"),new Element("option",{value:"OR",selected:"selected"}))).set("text","OR"),new Element("select",{id:"paramsfilter-join",class:"form-select-sm inputbox input-small",name:"jform[params][filter-join][]"}).adopt([h,c])),E=(this.counter<=0?((m=new Element("td")).appendChild(new Element("input",{type:"hidden",name:"jform[params][filter-grouped]["+this.counter+"]",value:"0"})),m.appendChild(new Element("span").set("text","n/a"))):(c="jform_params_filter-grouped_"+this.counter,E="jform[params][filter-grouped]["+this.counter+"]",f=new Element("fieldset",{class:"btn-group radio",id:c}),(u={id:c+"_0",type:"radio",name:E,value:"0"}).checked="1"!==o?"checked":"",f.appendChild(new Element("input",u)),u={for:c+"_0",class:"btn"+("1"!==o?" active":"")},f.appendChild(new Element("label",u).set("text",Joomla.JText._("JNO"))),(u={id:c+"_1",type:"radio",name:E,value:"1"}).checked="1"===o?"checked":"",f.appendChild(new Element("input",u)),u={for:c+"_1",class:"btn"+("1"===o?" active":"")},f.appendChild(new Element("label",u).set("text",Joomla.JText._("JYES"))),m=new Element("td").adopt(f)),new Element("td")),c=(E.appendChild(h),new Element("td")),o=(c.innerHTML=this.fields,new Element("td")),u=(o.innerHTML=r,new Element("td")),f=new Element("td"),h=(f.innerHTML=this.options.filterAccess,new Element("td")),r=new Element("textarea",{name:"jform[params][filter-value][]",class:"form-control inputbox",cols:17,rows:2}).set("text",l),l=(u.appendChild(r),u.appendChild(new Element("br")),[{value:0,label:Joomla.JText._("COM_FABRIK_TEXT")},{value:1,label:Joomla.JText._("COM_FABRIK_EVAL")},{value:2,label:Joomla.JText._("COM_FABRIK_QUERY")},{value:3,label:Joomla.JText._("COM_FABRIK_NO_QUOTES")}]),r=new Element("td").adopt(this._makeSel("form-select-sm inputbox elementtype input-small","jform[params][filter-eval][]",l,i,!1)),l=this.el.id+"-del-"+this.counter,i='<button id="'+l+'" class="btn btn-danger btn-sm"><i class="icon-minus" style="margin:0"></i></button>';if(h.set("html",i),p.appendChild(E),p.appendChild(c),p.appendChild(o),p.appendChild(u),p.appendChild(r),p.appendChild(f),p.appendChild(m),p.appendChild(h),this.el.appendChild(p),this.el.getParent("table").show(),document.id(l).addEvent("click",function(e){this.deleteFilterOption(e)}.bind(this)),document.id(this.el.id+"-del-"+this.counter).click=function(e){this.deleteFilterOption(e)}.bind(this),""!==e&&1<=(d=Array.mfrom(E.getElementsByTagName("SELECT"))).length)for(s=0;s<d[0].length;s++)d[0][s].value===e&&(d[0].options.selectedIndex=s);if(""!==t&&1<=(d=Array.mfrom(c.getElementsByTagName("SELECT"))).length)for(s=0;s<d[0].length;s++)d[0][s].value===t&&(d[0].options.selectedIndex=s);if(""!==n&&1<=(d=Array.mfrom(o.getElementsByTagName("SELECT"))).length)for(s=0;s<d[0].length;s++)d[0][s].value===n&&(d[0].options.selectedIndex=s);if(""!==a&&1<=(d=Array.mfrom(f.getElementsByTagName("SELECT"))).length)for(s=0;s<d[0].length;s++)d[0][s].value===a&&(d[0].options.selectedIndex=s);this.counter++}})});
//# sourceMappingURL=admin-filters-min.js.map