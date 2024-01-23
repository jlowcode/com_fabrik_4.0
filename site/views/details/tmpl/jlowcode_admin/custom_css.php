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


/* Begin - Repeat groups styles */
.repeatGroupTable tr {
    color: #A6A6A6 !important;
}

.fabrikElementReadOnly {
	margin-top: 0px !important;
}

.repeatGroupTable {
	border: 2px solid #eee !important;
}
/* Begin - Repeat groups styles */


/* Begin - Elements styles*/
.bg-info {
    background-color: #a6a6a6 !important;
}
/* End - Elements styles*/


/* Begin - Details styles */
.detailsContent {
    display: flex;
    flex-direction: column;
    line-height: 1;
}

.detailsContent > div {
    display: flex;
    flex-direction: row;
    border-top: 1px solid #eee;
    padding: 15px 0px 15px 10px;
}

.detailsContent > div:hover {
    background-color: #f6f6f6;
}

/*.detailsContent > div > div {
    width: 50%;
}*/

.fabrikLabel {
    font-weight: 900;
    color: #A6A6A6 !important;
}

.fabrikDetails a, .fabrikElement > div {
	color: #333840 !important;
}

.fabrikDetails a {
    text-decoration: underline !important;
}

textarea, input, select {
    background-color: #fff !important;
	border-radius: 12px !important;
    border: 2px solid #eee !important;
}
/* End - Details styles */


/* Begin - Footer styles */
.footer-btn {
	background-color: #f6f6f6;
	border-radius: 8px;
	display: flex;
	flex-direction: row;
	justify-content: space-between;
    width: 100%;
	padding: 10px !important;
}

.btn_jlowcode_admin {
	border-radius: 12px !important;
	color: #fff !important;
}

.btn_jlowcode_admin {
    text-shadow: none !important;
	border: none !important;
	width: 100% !important;
    display: block !important;
    float: right;
	font-weight: 300 !important;
	margin: 0px !important;
    color: #fff !important;
    padding: 0.4rem 2.5rem  !important;
}
.btn.btn_jlowcode_admin_edit {
    background-color: #003785 !important;

}
.btn.btn_jlowcode_admin_back {
    background-color: #1d1d1d !important;

}

.btn.btn_jlowcode_admin:hover {
	color: #ED7D3A !important;
}
/* End - Footer styles */


/* Begin - Page styles */
.h6 a {
	color: #333840;
}

h1 {
	margin: 0px !important;
}

.page-header {
	border-bottom: 2px solid #eee !important;
}

.platform-content {
    background-color: #fff;
    border-radius: 12px;
    padding: 50px !important;
}
/* End - Page styles */









#listform_$c {
	margin-top: 25px !important;
}

.section-horizontal-paddings {
    padding-left: 7% !important;
    padding-right: 7% !important;
}

.btn-group {
    padding: 1rem;

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
