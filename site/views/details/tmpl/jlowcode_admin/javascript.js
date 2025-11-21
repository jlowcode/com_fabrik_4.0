/**
 * Details
 *
 * @copyright: Copyright (C) 2025  Media A-Team, Inc. - All rights reserved.
 * @license  : GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

jQuery(function($) {
    const shareButton = $('[id^="button_share_details_"]');
    const pageHeader = $('.page-header');
    const titleH1 = pageHeader.find('h1');

    if (shareButton.length && pageHeader.length && titleH1.length) {
        shareButton.insertAfter(titleH1);

        pageHeader.css({
            'display': 'flex',
            'align-items': 'center',
            'gap': '24px'
        });

        titleH1.css('margin', '0');

        shareButton.css({
            'background-color': 'rgba(220, 226, 249, 1)',
            'border-radius': '50%',
            'width': '40px',
            'height': '40px',
            'padding': '0px',
            'flex-shrink': '0'
        })
    }

    setCopyToClipboard();
});

/**
 * Sets up the button to copy the current page URL to the clipboard
 * 
 * @return  void
 * 
 * @since   version 4.0.5
 */
function setCopyToClipboard() {
    jQuery('[id^="button_share_details_"]').off('click').on('click', function() {
        const tempInput = document.createElement("input");
        tempInput.value = window.location.href;
        document.body.appendChild(tempInput);
        
        // Select the input value and copy to clipboard
        tempInput.select();
        tempInput.setSelectionRange(0, 99999);
        document.execCommand('copy');
        document.body.removeChild(tempInput);

        alert(Joomla.JText._("COM_FABRIK_COPIED_TO_CLIPBOARD"));
    });
}

