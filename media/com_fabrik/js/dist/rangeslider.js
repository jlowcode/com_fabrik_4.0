/**
 * RangeSlider filter
 *
 * @copyright: Copyright (C) 2019-2020  Projeto PITT. - All rights reserved.
 * @license:   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

/* jshint mootools: true */
/* global fconsole:true, Joomla:true,  */

define(['jquery', 'lib/jquery-ui/jquery-ui.min'], function (jQuery) {

    var RangeSlider = new Class({
        
        initialize: function (max, min, elementName) {
            var handleMin   = jQuery("#handle_filter_range_0"),
                handleMax   = jQuery("#handle_filter_range_1");
                inputMin    = jQuery("#" + elementName + "_filter_range_0"),
                inputMax    = jQuery("#" + elementName + "_filter_range_1"),
                clearFilter = document.querySelector('.clearFilters');

            this.calculateSize(handleMin);
            this.calculateSize(handleMax);

            jQuery(".slider-range").slider({
                range: true,
                min: min,
                max: max,
                values: [inputMin.val(), inputMax.val()], // Determine initial values
                animate: 'slow',
                slide: function(_event, ui) {
                    handleMin.text(ui.values[0]);
                    handleMax.text(ui.values[1]);
                    inputMin.val(ui.values[0]);
                    inputMax.val(ui.values[1]);

                    // Remove div that hides the element when it fires
                    if (jQuery('.slider-range').parent().is('div')) { 
                        jQuery('.slider-range').unwrap();
                        jQuery('.slider-range').removeAttr('style');
                    }
                }
            });

            var self = this;
            // Filter without having to click the 'go' button
            var oldInputMin = '', oldInputMax = '';
            jQuery(".ui-slider-handle").on('click', function () {
                if(oldInputMin != '' && oldInputMin != inputMin.val()){
                    self.deleteSearchedTag(oldInputMin);
                }

                if(oldInputMax != '' && oldInputMax != inputMax.val()){
                    self.deleteSearchedTag(oldInputMax);
                }

                if(oldInputMin != inputMin.val()){
                    self.addSearchedTag(inputMin.val());
                }
                if(oldInputMax != inputMax.val()){
                    self.addSearchedTag(inputMax.val());
                }
                
                oldInputMin = inputMin.val();
                oldInputMax = inputMax.val();

                Fabrik.fireEvent('fabrik.list.dofilter', [this]); 
            });

            // DIV that expands the click space of the handle
            jQuery(".click").on('click', function () { 
                Fabrik.fireEvent('fabrik.list.dofilter', [this]); 
            });
            
            // Clean the slicer by clicking the Clear filter button
            clearFilter.addEventListener("click", function() {  
                jQuery(".slider-range").slider({
                    values: [min, max]
                });
                handleMin.text(min);
                handleMax.text(max);
            });
        },

        // Calculate the width and left margin of the div according to the number of digits
        calculateSize: function (handle) {
            // Default sizes
            let marginLeft  = -3, 
                width       = 22;

            if (handle.text().length > 3) {
                
                // Each digit beyond the third adds 10px the width and -4px the left margin
                calcWidth   = width + ((handle.text().length - 3) * 10);
                calcMargin  = marginLeft - ((handle.text().length - 3) * 4);
                
                handle.attr('style', 'width: ' + calcWidth + 'px !important');
                handle.css('margin-left', calcMargin + 'px');

            } else if (handle.text().length == 3) {

                handle.attr('style', 'width: 27px !important');
                handle.css('margin-left', '-5px');

            }
        },

        /**
         * Add a tag near to clear filters button, to represent the selected filter
         */
        addSearchedTag: function(text){
            var divFilteredEls = jQuery('.filteredTags')[0];
            if(divFilteredEls){
                jQuery('.filteredTags').append('<span tag-value="'+ text + '" style="padding: 2px 8px 2px 8px;border-radius: 10px;background-color: #EEEEEF;font-weight: normal">' + text + '</span>');
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

    return RangeSlider;

})
