/*! Fabrik */

define(["jquery","fab/fabrik","jQueryUI","fab/utils"],function(c,f,t){return f.getWindow=function(t){if(f.Windows[t.id])!1!==t.visible&&f.Windows[t.id].open(),f.Windows[t.id].setOptions(t);else switch(t.type?t.type:""){case"redirect":f.Windows[t.id]=new f.RedirectWindow(t);break;case"modal":f.Windows[t.id]=new f.Modal(t),c(window).on("resize",function(){t.id in f.Windows&&f.Windows[t.id].fitToContent(!1)});break;case"":default:f.Windows[t.id]=new f.Window(t)}return f.Windows[t.id]},f.Window=new Class({Implements:[Events,Options],options:{id:"FabrikWindow",data:{},title:"&nbsp;",container:!1,loadMethod:"html",contentURL:"",createShowOverLay:!1,width:300,height:300,loadHeight:100,expandable:!0,offset_x:null,offset_y:null,visible:!0,modalId:"",onClose:function(){},onOpen:function(){},onContentLoaded:function(){this.fitToContent(!1)},destroy:!0},modal:!1,classSuffix:"",expanded:!1,initialize:function(t){this.options=c.extend(this.options,t),this.makeWindow()},watchTabs:function(){var t=this;c(".nav-tabs a").on("mouseup",function(){t.fitToWidth(),t.drawWindow()})},deleteButton:function(){return c(f.jLayouts["modal-close"])[0]},contentHeight:function(){if("iframe"===this.options.loadMethod)return this.contentWrapperEl.find("iframe").height();var t=this.window.find(".contentWrapper");return t.css("height","auto"),t[0].getDimensions(!0).height},center:function(){var t,i,n=this.window,e=this.windowDimensionInPx("width"),o=this.windowDimensionInPx("height"),s=n.width(),d=n.height(),h={};s=null===s||"auto"===s?e:s,d=null===d||"auto"===d?o:d,s=parseInt(s,10),d=parseInt(d,10),t=c(window).height()/2-d/2,-1===c.inArray(c(n).css("position"),["fixed","static"])&&(t+=window.getScroll().y),h.top=null!==this.options.offset_y?window.getScroll().y+this.options.offset_y:t,i=c(window).width()/2+window.getScroll().x-s/2,h.left=null!==this.options.offset_x?window.getScroll().x+this.options.offset_x:i,h["margin-left"]=0,n.css(h)},windowDimensionInPx:function(t){var i=this.options[t]+"";return-1!==i.indexOf("%")?"height"===t?Math.floor(c(window).height()*(i.toFloat()/100)):Math.floor(c(window).width()*(i.toFloat()/100)):parseInt(i,10)},makeWindow:function(){var t,i,n=this;f.jLayouts[this.options.modalId]?(this.window=this.buildWinFromLayout(),this.window.find('*[data-role="title"]').text(this.options.title)):this.window=this.buildWinViaJS(),this.options.visible||this.window.addClass("fabrikHide"),c(document.body).append(this.window),this.loadContent(),this.options.visible||(this.window.hide(),this.window.removeClass("fabrikHide")),c(this.window).find('*[data-role="close"]').on("click",function(t){t.preventDefault(),n.close()}),this.window.find('*[data-role="expand"]').on("click",function(t){t.preventDefault(),n.expand()}),t=this.windowDimensionInPx("width"),i=this.contentHeight(),this.contentWrapperEl.css({height:i,width:t+"px"});var e=this.window.find('*[data-role="title"]');if(!this.options.modal){var o=0===this.window.find("*[data-draggable]").length?this.window:this.window.find("*[data-draggable]");o.draggable({handle:e,drag:function(){f.fireEvent("fabrik.window.resized",n.window),n.drawWindow()}}),o.resizable({containment:n.options.container?c("#"+n.options.container):null,handles:{n:".ui-resizable-n",e:".ui-resizable-e",s:".ui-resizable-s",w:".ui-resizable-w",ne:".ui-resizable-ne",se:".ui-resizable-se",sw:".ui-resizable-sw",nw:".ui-resizable-nw"},resize:function(){f.fireEvent("fabrik.window.resized",n.window),n.drawWindow()}})}0===c("div.modal-header .handlelabel").text().length&&c("div.itemContentPadder form").context.title.length&&c("div.modal-header .handlelabel").text(c("div.itemContentPadder form").context.title),this.window.css("width",this.options.width),this.window.css("height",parseInt(this.options.height)+this.window.find('*[data-role="title"]').height()),this.options.modal?this.fitToContent(!1):this.center(),this.options.visible&&this.open()},buildWinFromLayout:function(){var t=c(f.jLayouts[this.options.modalId]);return this.contentEl=t.find(".itemContentPadder"),this.contentWrapperEl=t.find("div.contentWrapper"),t},buildWinViaJS:function(){var t,i,n,e,o,s,d,h=[],a=this;this.window=new Element("div",{id:this.options.id,class:"fabrikWindow "+this.classSuffix+" modal"});var l=this.deleteButton();c(l).on("click",function(){a.close()});var w="handlelabel";this.options.modal||(w+=" draggable",t=c("<div />").addClass("bottomBar modal-footer"),i=c("<div />").addClass("dragger"),t.append(i)),e=c(f.jLayouts["icon-full-screen"]),o=c("<h3 />").addClass(w).text(this.options.title),c(o).data("role","title"),c(o).attr("data-role","title"),h.push(o),this.options.expandable&&!1===this.options.modal&&(n=c("<a />").addClass("expand").attr({href:"#"}).append(e),h.push(n)),h.push(l),this.handle=this.getHandle().append(h);var r=parseInt(this.options.height)-0-15;r<this.options.loadHeight&&(r=this.options.loadHeight),this.contentWrapperEl=c("<div />").addClass("contentWrapper").css({height:r+"px"});var p=c("<div />").addClass("itemContent");if(this.contentEl=c("<div />").addClass("itemContentPadder"),p.append(this.contentEl),this.contentWrapperEl.append(p),this.window=c(this.window),this.options.modal)this.window.append([this.handle,this.contentWrapperEl]);else for(this.window.append([this.handle,this.contentWrapperEl,t]),s=["n","e","s","w","nw","ne","se","sw"],d=0;d<s.length;d++)this.window.append(c('<div class="ui-resizable-'+s[d]+' ui-resizable-handle"></div>'));return this.window},expand:function(){if(this.expanded)this.window.css({left:this.unexpanded.left+"px",top:this.unexpanded.top+"px"}),this.window.css({width:this.unexpanded.width,height:this.unexpanded.height}),this.expanded=!1;else{this.expanded=!0,this.unexpanded=c.extend({},this.window.position(),{width:this.window.width(),height:this.window.height()});var t=window.getScroll();this.window.css({left:t.x+"px",top:t.y+"px"}),this.window.css({width:c(window).width(),height:c(window).height()})}this.drawWindow()},getHandle:function(){var t=this.handleClass();return c("<div />").addClass("draggable "+t)},handleClass:function(){return"modal-header"},loadContent:function(){var t,i=this;switch(window.fireEvent("tips.hideall"),this.options.loadMethod){case"html":if(void 0===this.options.content)return fconsole("no content option set for window.html"),void this.close();"element"===typeOf(this.options.content)?c(this.options.content).appendTo(this.contentEl):this.contentEl.html(this.options.content),this.options.onContentLoaded.apply(this),this.watchTabs();break;case"xhr":i.window.width(i.options.width),i.window.height(i.options.height),i.onContentLoaded=i.options.onContentLoaded,f.loader.start(i.contentEl),new c.ajax({url:this.options.contentURL,data:c.extend(this.options.data,{fabrik_window_id:this.options.id}),method:"post"}).success(function(t){f.loader.stop(i.contentEl),i.contentEl.append(t),i.watchTabs(),i.center(),i.onContentLoaded.apply(i)});break;case"iframe":var n=parseInt(this.options.height,10)-40,e=this.contentEl[0].scrollWidth,o=e+40<c(window).width()?e+40:c(window).width();t=this.window.find(".itemContent"),f.loader.start(t),this.iframeEl&&this.iframeEl.remove(),this.iframeEl=c("<iframe />").addClass("fabrikWindowIframe").attr({id:this.options.id+"_iframe",name:this.options.id+"_iframe",class:"fabrikWindowIframe",src:this.options.contentURL,marginwidth:0,marginheight:0,frameBorder:0,scrolling:"auto"}).css({height:n+"px",width:o}).appendTo(t),this.iframeEl.hide(),this.iframeEl.on("load",function(){f.loader.stop(i.window.find(".itemContent")),i.iframeEl.show(),c(i).trigger("onContentLoaded",[i]),i.watchTabs()})}},titleHeight:function(){var t=this.window.find("."+this.handleClass());return t=0<t.length?t.outerHeight():25,isNaN(t)&&(t=0),t},footerHeight:function(){var t=parseInt(this.window.find(".bottomBar").outerHeight(),10);return isNaN(t)&&(t=0),t},drawWindow:function(){var t=this.titleHeight(),i=this.footerHeight(),n=this.contentHeight(),e=(0===this.window.find("*[data-draggable]").length?this.window:this.window.find("*[data-draggable]")).width();n>this.window.height()&&(n=this.window.height()-t-i),this.contentWrapperEl.css("height",n),this.contentWrapperEl.css("width",e-2),"iframe"===this.options.loadMethod&&(this.iframeEl.css("height",this.contentWrapperEl[0].offsetHeight),this.iframeEl.css("width",this.contentWrapperEl[0].offsetWidth-10))},fitToContent:function(t,i){t=void 0===t||t,i=void 0===i||i,"iframe"!==this.options.loadMethod&&(this.fitToHeight(),this.fitToWidth()),this.drawWindow(),i&&this.center(),!this.options.offset_y&&t&&c("body").scrollTop(this.window.offset().top)},fitToHeight:function(){var t=this.contentHeight()+this.footerHeight()+this.titleHeight(),i=c(window).height(),n=t<i?t:i;this.window.css("height",n)},fitToWidth:function(){var t=this.window.find(".itemContent"),i=c(window).width(),n=t[0].scrollWidth,e=n+25<i?n+25:i;this.window.css("width",e)},close:function(t){t=t||!1,this.options.destroy||t?(this.window.remove(),delete f.Windows[this.options.id]):this.window.fadeOut({duration:0}),f.tips.hideAll(),this.fireEvent("onClose",[this]),f.fireEvent("fabrik.window.close",[this])},open:function(t){t&&t.stopPropagation(),this.window.show(),this.fireEvent("onOpen",[this])}}),f.Modal=new Class({Extends:f.Window,modal:!0,classSuffix:"fabrikWindow-modal",getHandle:function(){return c("<div />").addClass(this.handleClass())},fitToHeight:function(){var t=this.contentHeight()+this.footerHeight()+this.titleHeight(),i=c(window).height(),n=t<i?t:i;this.window.css("height",Math.max(parseInt(this.options.height),n))},fitToWidth:function(){this.window.css("width",this.options.width)}}),f.RedirectWindow=new Class({Extends:f.Window,initialize:function(t){var i={id:"redirect",title:t.title?t.title:"",loadMethod:void 0,width:t.width?t.width:300,height:t.height?t.height:320,minimizable:!1,collapsible:!0,contentURL:t.contentURL?t.contentURL:"",id:"redirect"},n=(t=c.merge(i,t)).contentURL;t.loadMethod="xhr",n.contains(f.liveSite)||!n.contains("http://")&&!n.contains("https://")?n.contains("tmpl=component")||(t.contentURL+=n.contains("?")?"&tmpl=component":"?tmpl=component"):t.loadMethod="iframe",this.options=c.extend(this.options,t),this.makeWindow()}}),f.Window});