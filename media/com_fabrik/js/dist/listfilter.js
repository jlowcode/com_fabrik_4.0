/*! Fabrik */

define(["jquery","fab/fabrik","fab/advanced-search"],(function(t,e,i){return new Class({Implements:[Events],Binds:[],options:{container:"",filters:[],type:"list",id:"",ref:"",advancedSearch:{controller:"list"}},initialize:function(n){var a=this;this.filters={},this.options=t.extend(this.options,n),this.advancedSearch=!1,this.container=t("#"+this.options.container),this.filterContainer=this.container.find(".fabrikFilterContainer"),this.filtersInHeadings=this.container.find(".listfilter");var r=this.container.find(".toggleFilters");if(r.on("click",(function(t){t.preventDefault(),a.filterContainer.toggle(),a.filtersInHeadings.toggle(),"7"==this.dataset.filterMode&&(classContainer=a.filterContainer[0].parentNode.getElementsByClassName("listContent")[0].classList,-1!==classContainer.value.indexOf("col-lg-9")?classContainer.remove("col-lg-9"):classContainer.add("col-lg-9"))})),r.length>0&&(this.filterContainer.hide(),this.filtersInHeadings.toggle()),0!==this.container.length){this.getList();var s=this.container.find(".clearFilters");s.off(),s.on("click",(function(e){e.preventDefault(),a.container.find(".fabrik_filter").each((function(e,i){a.clearAFilter(t(i))})),a.clearPlugins(),a.submitClearForm()})),e.addEvent("fabrik.listfilter.clear",(function(){a.container.find(".fabrik_filter").each((function(e,i){t(i).prop("name").contains("fabrik_list_filter_all")&&a.clearAFilter(t(i))})),a.clearPlugins(),a.submitClearForm()})),this.container.find(".advanced-search-link").on("click",(function(n){n.preventDefault();var r,s=t(n.target);"A"!==s.prop("tagName")&&(s=s.closest("a"));var o=s.prop("href");o+="&listref="+a.options.ref,r={id:"advanced-search-win"+a.options.ref,modalId:"advanced-filter",title:Joomla.JText._("COM_FABRIK_ADVANCED_SEARCH"),loadMethod:"xhr",evalScripts:!0,contentURL:o,width:710,height:340,y:a.options.popwiny,onContentLoaded:function(){var t=e.blocks["list_"+a.options.ref];void 0===t&&(t=e.blocks[a.options.container],a.options.advancedSearch.parentView=a.options.container),t.advancedSearch=new i(a.options.advancedSearch),this.fitToContent(!1)}},e.getWindow(r)})),t(".fabrik_filter.advancedSelect").on("change",{changeEvent:"change"},(function(t){this.fireEvent(t.data.changeEvent,new Event.Mock(document.getElementById(this.id),t.data.changeEvent))})),this.watchClearOne()}},getList:function(){return this.list=e.blocks[this.options.type+"_"+this.options.ref],void 0===this.list&&(this.list=e.blocks[this.options.container]),this.list},addFilter:function(t,e){!1===this.filters.hasOwnProperty(t)&&(this.filters[t]=[]),this.filters[t].push(e)},onSubmit:function(){this.filters.date&&t.each(this.filters.date,(function(t,e){e.onSubmit()})),this.filters.jdate&&t.each(this.filters.jdate,(function(t,e){e.onSubmit()})),this.showFilterState()},onUpdateData:function(){this.filters.date&&t.each(this.filters.date,(function(t,e){e.onUpdateData()})),this.filters.jdate&&t.each(this.filters.jdate,(function(t,e){e.onUpdateData()}))},getFilterData:function(){var e={};return this.container.find(".fabrik_filter").each((function(){if(void 0!==t(this).prop("id")&&t(this).prop("id").test(/value$/)){var i=t(this).prop("id").match(/(\S+)value$/)[1];"SELECT"===t(this).prop("tagName")&&-1!==this.selectedIndex?e[i]=t(this.options[this.selectedIndex]).text():e[i]=t(this).val(),e[i+"_raw"]=t(this).val()}})),e},update:function(){t.each(this.filters,(function(t,e){e.each((function(t){t.update()}))}))},clearAFilter:function(e){var i;(e.prop("name").contains("[value]")||e.prop("name").contains("fabrik_list_filter_all")||e.hasClass("autocomplete-trigger")||e.parent().find(".tag-container").length>0)&&("SELECT"===e.prop("tagName")?(i=e.prop("multiple")?-1:0,e.prop("selectedIndex",i)):"checkbox"===e.prop("type")?e.prop("checked",!1):0==e.parent().find(".tag-container").length&&e.val(""),e.hasClass("advancedSelect")&&e.trigger("chosen:updated"),e.parent().find(".tag-container").length>0&&e.parent().find(".tag-container").each((function(e,i){t(i).remove()})))},clearPlugins:function(){var t=this.getList().plugins;null!==t&&t.each((function(t){t.clearFilter()}))},submitClearForm:function(){var e="FORM"===this.container.prop("tagName")?this.container:this.container.find("form");t("<input />").attr({name:"resetfilters",value:1,type:"hidden"}).appendTo(e),"list"===this.options.type?this.list.submit("list.clearfilter"):this.container.find("form[name=filter]").submit()},watchClearOne:function(){var e=this;this.container.find("*[data-filter-clear]").on("click",(function(i){i.stopPropagation();var n=i.event?i.event.currentTarget:i.currentTarget,a=t(n).data("filter-clear");t('*[data-filter-name="'+a+'"]').each((function(i,n){e.clearAFilter(t(n))})),e.submitClearForm(),e.showFilterState()}))},showFilterState:function(){var i,n,a,r=t(e.jLayouts["modal-state-label"]),s=this,o=!1,l=this.container.find("*[data-modal-state-display]");0!==l.length&&(l.empty(),t.each(this.options.filters,(function(e,c){var d=s.container.find('*[data-filter-name="'+c.name+'"]');"SELECT"===d.prop("tagName")&&-1!==d[0].selectedIndex?(n=t(d[0].options[d[0].selectedIndex]).text(),a=d.val()):n=a=d.val(),null!=n&&""!==n&&""!==a&&(o=!0,(i=r.clone()).find("*[data-filter-clear]").data("filter-clear",c.name),i.find("*[data-modal-state-label]").text(c.label),i.find("*[data-modal-state-value]").text(n),l.append(i))})),o?this.container.find("*[data-modal-state-container]").show():this.container.find("*[data-modal-state-container]").hide(),this.watchClearOne())},updateFilterCSS:function(t){var e=this.container.find(".clearFilters");e&&(t.hasFilters?e.addClass("hasFilters"):e.removeClass("hasFilters"))}})}));