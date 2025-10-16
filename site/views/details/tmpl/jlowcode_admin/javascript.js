/**
 * Details helper
 *
 * @copyright: Copyright (C) 20  Media A-Team, Inc. - All rights reserved.
 * @license  : GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// details-share.js - código sem requirejs
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const shareButton = document.querySelector('.btn-share');
        const pageHeader = document.querySelector('.page-header');
        const titleH1 = document.querySelector('.page-header h1');
        
        if (shareButton && pageHeader && titleH1) {
            titleH1.parentNode.insertBefore(shareButton, titleH1.nextSibling);
            pageHeader.style.display = 'flex';
            pageHeader.style.alignItems = 'center';
            pageHeader.style.gap = '24px';
            titleH1.style.margin = '0';
            
            jQuery(shareButton).css({
                'background-color': 'rgba(220, 226, 249, 1)',
                'border-radius': '50%',
                'width': '40px',
                'height': '40px',
                'padding': '0px',
                'flex-shrink': '0'
            }).find('img').css({
                'margin-bottom': '3px'
            });
        }
        
        function setCopyToClipboard() {
            jQuery(document).off('click', '.btn-share');
            jQuery('.btn-share').on('click', function() {
                var tempInput = document.createElement('input');
                tempInput.value = window.location.href;
                document.body.appendChild(tempInput);
                tempInput.select();
                tempInput.setSelectionRange(0, 99999);
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                alert('URL copiada para a área de transferência!');
            });
        }
        
        setCopyToClipboard();
    }, 100);
});