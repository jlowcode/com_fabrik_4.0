<?php
/**
 * Fabrik List Template: Default Custom CSS
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

/**
* If you need to make small adjustments or additions to the CSS for a Fabrik
* list template, you can create a custom_css.php file, which will be loaded after
* the main template_css.php for the template.
*
* This file will be invoked as a PHP file, so the list ID
* can be used in order to narrow the scope of any style changes.  You do
* this by prepending #listform_$c to any selectors you use.  This will become
* (say) #listform_12, owhich will be the HTML ID of your list on the page.
*
* See examples below, which you should remove if you copy this file.
*
* Don't edit anything outside of the BEGIN and END comments.
*
* For more on custom CSS, see the Wiki at:
*
* http://www.fabrikar.com/forums/index.php?wiki/form-and-details-templates/#the-custom-css-file
*
* NOTE - for backward compatibility with Fabrik 2.1, and in case you
* just prefer a simpler CSS file, without the added PHP parsing that
* allows you to be be more specific in your selectors, we will also include
* a custom.css we find in the same location as this file.
*
*/

header('Content-type: text/css');
$c = $_REQUEST['c'];
echo <<<EOT
/* BEGIN - Your CSS styling starts here */

#listform_$c {
	margin-top: 25px !important;
}

.footer-btn{
    background-color: #e3ecf1;
    display: flex;
}

.btn-group {
    padding: 1rem;

}

.btn_edu{
    text-shadow: none !important;
	border: none !important;
	width: 100% !important;
    display: block !important;
    float: right;
	font-weight: 300 !important;
	margin: 0px !important;
    color: #fff !important;
    font-size: 1rem;
    padding: 0.4rem 4.5rem  !important;
    border-radius: 0.4rem;
}
.btn.btn_edu_edit{
    background-color: #032b43 !important;

}
.btn.btn_edu_back{
    background-color: #1d1d1d !important;

}
.btn.btn_edu a{
    color: #fff !important;
}

.btn.btn_edu:hover{
	color: #ED7D3A !important;
}

.btn.btn-default{
    text-shadow: none !important;
	border: none !important;
	width: 15% !important;
    display: block !important;
	font-weight: 300 !important;
	margin: 0px !important;
    background-color: #032b43 !important;
    color: #fff !important;
    float: right;

}

.btn.btn-default:hover{
	color: #ED7D3A !important;
}

.row-striped .row-fluid{
    display: flex;
}

@media (max-width: 600px){
    .fabrikElementReadOnly {
        margin: 10px;
    }
	.row-striped .row-fluid [class*="span"]:first-child {
		margin-left: 10px !important;
	}
    .fabrikLabel{
        font-weight: 600;
    }
    
}
.fabrikGroup{
    padding: 30px 0px;
}

/* END - Your CSS styling ends here */
EOT;
