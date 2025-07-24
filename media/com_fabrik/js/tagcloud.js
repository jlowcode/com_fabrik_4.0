/**
 * RangeSlider filter
 *
 * @copyright: Copyright (C) 2019-2020  Projeto PITT. - All rights reserved.
 * @license:   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

/* jshint mootools: true */
/* global fconsole:true, Joomla:true,  */

define(['jquery'], function (jQuery) {

    var TagCloud = new Class({
        
        initialize: function (elementName) {
            var self = this;
            var input = jQuery("#"+elementName+"_filter_tagcloud"), // tag <input>
                a     = jQuery(".tag"); // tag <a>

            // style the tag when it has default
            if (input.val() != "") {
                // jQuery("a[value=" + input.val() + "]").css("color", "#5b1e77");
            }

            jQuery(a).on('click', function () {

                input.val(jQuery(this).attr('value'));

                var lastText = '';
                if(lastText != jQuery(this).text()){
                    self.deleteSearchedTag(lastText);
                    lastText = jQuery(this).text();
                    self.addSearchedTag(jQuery(this).text());
                }

                // a.css("color", "#4db2b3");
                // jQuery(this).css("color", "#5b1e77");

                // filter without having to click the 'go' button
                Fabrik.fireEvent('fabrik.list.dofilter', [this]);

            });
        },

        /**
         * Add a tag near to clear filters button, to represent the selected filter
         */
        addSearchedTag: function(text){
            var divFilteredEls = jQuery('.filteredTags')[0];
            if(divFilteredEls){
                jQuery('.filteredTags').append('<span tag-value="'+ text + '" class="tagSearched">' + text + '</span>');
            }
        },

        /**
         * Remove tag
         */
        deleteSearchedTag: function(text){
            var divFilteredEls = jQuery('.filteredTags')[0];
            if(divFilteredEls){
                if(jQuery(divFilteredEls).find("span[tag-value='" + text + "']")[0]){
                    jQuery(divFilteredEls).find("span[tag-value='" + text + "']")[0].remove();
                }
            }
        }

    })

    return TagCloud;

})