/**
 * RangeSlider filter
 *
 * @copyright: Copyright (C) 2019-2020  Projeto PITT. - All rights reserved.
 * @license:   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
define(["jquery"],(function(e){return new Class({initialize:function(t){var a=this,i=e("#"+t+"_filter_tagcloud"),n=e(".tag");i.val(),e(n).on("click",(function(){i.val(e(this).attr("value")),e(this).css("font-weight","800"),e(this).css("text-decoration","underline");var t="";t!=e(this).text()&&(a.deleteSearchedTag(t),t=e(this).text(),a.addSearchedTag(e(this).text())),Fabrik.fireEvent("fabrik.list.dofilter",[this])}))},addSearchedTag:function(t){e(".filteredTags")[0]&&e(".filteredTags").append('<span tag-value="'+t+'" class="tagSearched">'+t+"</span>")},deleteSearchedTag:function(t){var a=e(".filteredTags")[0];a&&e(a).find("span[tag-value='"+t+"']")[0]&&e(a).find("span[tag-value='"+t+"']")[0].remove()}})}));