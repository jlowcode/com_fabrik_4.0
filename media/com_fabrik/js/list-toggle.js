/**
 * List Toggle
 *
 * @copyright: Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license:   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

define(['jquery'], function (jQuery) {

    var FbListToggle = new Class({

        initialize: function (form) {

            // Stop dropdown closing on click
            jQuery('#' + form.id + ' .togglecols .dropdown-menu a, #' + form.id
                + ' .togglecols .dropdown-menu li').click(function (e) {
                e.stopPropagation();
            });

            // Set up toggle events for elements
            form.addEvent('mouseup:relay(a[data-bs-toggle-col])', function (e, btn) {
                var state = jQuery(btn).data('bs-toggle-state');
                var col = jQuery(btn).data('bs-toggle-col');
                this.toggleColumn(col, state, btn);
            }.bind(this));

            // Toggle events for groups (toggles all elements in group)
            var groups = form.getElements('a[data-bs-toggle-group]');
            form.addEvent('mouseup:relay(a[data-bs-toggle-group])', function (e, group) {
                var state = jQuery(group).data('bs-toggle-state'), muted,
                    groupName = jQuery(group).data('bs-toggle-group'),
                    links = document.getElements('a[data-bs-toggle-parent-group=' + groupName + ']');

                links.each(function (btn) {
                    var col = jQuery(btn).data('bs-toggle-col');
                    this.toggleColumn(col, state, btn);
                }.bind(this));

                state = state === 'open' ? 'close' : 'open';
                muted = state === 'open' ? '' : ' muted';
                jQuery(group).find('*[data-isicon]')
                    .removeClass()
                    .addClass('icon-eye-' + state + muted);
                jQuery(group).data('bs-toggle-state', state);

            }.bind(this));
        },

        /**
         * Toggle column
         *
         * @param col   Element name
         * @param state Open/closed
         * @param btn   Button/link which initiated the toggle
         */
        toggleColumn: function (col, state, btn) {
            var muted;
            state = state === 'open' ? 'close' : 'open';

            if (state === 'open') {
                jQuery('.fabrik___heading .' + col).addClass('d-table-cell').removeClass('d-none d-md-none d-lg-none');
                jQuery('.fabrikFilterContainer .' + col).addClass('d-table-cell').removeClass('d-none d-md-none d-lg-none');
                jQuery('.fabrik_row  .' + col).addClass('d-table-cell').removeClass('d-none d-md-none d-lg-none');
                jQuery('.fabrik_calculations  .' + col).addClass('d-table-cell').removeClass('d-none d-md-none d-lg-none');
                muted = '';
            } else {
                jQuery('.fabrik___heading .' + col).addClass('d-none').removeClass('d-table-cell d-md-table-cell d-lg-table-cell');
                jQuery('.fabrikFilterContainer .' + col).addClass('d-none').removeClass('d-table-cell d-md-table-cell d-lg-table-cell');
                jQuery('.fabrik_row  .' + col).addClass('d-none').removeClass('d-table-cell d-md-table-cell d-lg-table-cell');
                jQuery('.fabrik_calculations  .' + col).addClass('d-none').removeClass('d-table-cell d-md-table-cell d-lg-table-cell');
                muted = ' muted';
            }

            jQuery(btn).find('*[data-isicon]')
                .removeClass()
                .addClass('icon-eye-' + state + muted);
            jQuery(btn).data('bs-toggle-state', state);
        }
    });

    return FbListToggle;
});