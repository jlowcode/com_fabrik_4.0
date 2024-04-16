<?php
/**
 * Default Form Template: Custom CSS
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.0
 */

/**
 * If you need to make small adjustments or additions to the CSS for a Fabrik
 * template, you can create a custom_css.php file, which will be loaded after
 * the main template_css.php for the template.
 *
 * This file will be invoked as a PHP file, so the view type, form ID and row ID
 * can be used in order to narrow the scope of any style changes.  A new form will
 * have an ID of "form_X" (where X is the form's numeric ID), while edit forms (for existing
 * rows) will have an ID of "form_X_Y" (where Y is the rowid).  Detail views will always
 * be of the format "details_X_Y".
 *
 * So to apply styles for (say) form ID 123, you would use ...
 *
 * #form_123, #form_123_$rowid { ... }
 *
 * Or to style for any form / row, it would just be ...
 *
 * #$form { ... }
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
$c = (int) $_REQUEST['c'];
$view = isset($_REQUEST['view']) ? $_REQUEST['view'] : 'form';
$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : '';
$form = $view . '_' . $c;
if ($rowid !== '')
{
	$form .= '_' . $rowid;
}
echo <<<EOT

/* BEGIN - Your CSS styling starts here */


/* Begin - Fileupload style */
.plupload a, .plupload tr {
	color: #A6A6A6 !important;
}

.plupload table {
	border: 2px solid #eee !important;
	border-radius: 12px !important;
}

.plg-fileupload input {
	padding: 10px;
	color: #A6A6A6 !important;
}

.fabrikWindow-modal .button {
	background-color: #003785 !important;
}
/* End - Fileupload style */

.fabrikElementReadOnly {
	margin-top: 0px !important;
}

.repeatGroupTable {
	border: 2px solid #eee !important;
}

.g-menu-item span {
	color: #032B43 !important;
    font-weight: 400 !important;
}

.g-menu-item span:hover {
	font-weight: 700 !important;
}

.page-header {
	border-bottom: 2px solid #eee !important;
}

h1 {
	margin: 0px !important;
}

fieldset {
	margin-bottom: 1rem !important;
}

.fabrikActions {
	display: flex;
	padding: 19px 20px 20px;
}

/* Begin - Databasejoin style */
.jqtree-toggler {
	color: #777 !important;
}

.tag-content {
	color: #333840 !important;
}

.select2-container--default.select2-container--focus .select2-selection--multiple {
    border: 2px solid #eee !important;
	border-radius: 12px !important;
}

.select2-selection--multiple {
	border: 2px solid #eee !important;
	border-radius: 12px !important;
}


.select2-container input {
	border: inherit !important;
}
/* End - Databasejoin style */

input, select {
    height: 50px !important;
}

textarea, input, select {
    background-color: #fff !important;
	border-radius: 12px !important;
    border: 2px solid #eee !important;
}


.h6 a {
	color: #333840;
}

.jqtree-tree span:hover {
	font-weight: 600;
}

label {
	font-weight: bold;
}

label, span, select {
	color: #A6A6A6 !important;
}

.platform-content {
    background-color: #fff;
    border-radius: 12px;
    padding: 50px !important;
	margin-bottom: 40px !important;
}



#$form .foobar {
	display: none;
}

#form_123 .foobar, #form_123_$rowid .foobar {
	display: none;
}

.section-horizontal-paddings {
    padding-left: 7% !important;
    padding-right: 7% !important;
}

.footer-btn {
	background-color: #f6f6f6;
	border-radius: 8px;
	display: flex;
	flex-direction: row;
	justify-content: space-between;
    width: 100%;
	padding: 10px !important;
}

.fabrikPagePrevious,
.fabrikPageNext {
	text-shadow: none !important;
    border: none !important;  
    font-weight: 300 !important;
    margin: 10px 0px !important;
}

.btn-group-actions {
	text-shadow: none !important;
	border: none !important;
	width: 100% !important;
    display: block !important;
	font-weight: 300 !important;
	margin: 0px !important;
}

.button {
	padding: 8px 25px !important;
	border-radius: 12px !important;
	color: #fff !important;
}

.btn:hover {
	color: #ED7D3A !important;
}


.btn-save-new{
	background-color: #1465bb !important;
}
.btn-save-copy{
	background-color: #003EA1 !important;
}
.btn-save-details{
	background-color: #2196f3 !important;
}
.btn-save-back{
	background-color: #003785 !important;
}
.btn-save-only{
	background-color: #81c9fa !important;
}

.btn-cancel-back {
	background-color: #1D1D1D !important;
}

.btn-reset {
	background-color: #3D3D3D !important;
}

.btn-delete {
	background-color: #003EA1 !important;
}


.ul-btn-actions {
	list-style: none;
    transition: opacity 0.3s ease-out, transform 0.3s ease-out;
	margin: 10px;
}

.ul-btn-actions li{
    list-style: none;
    position: relative;
    padding-top: 1px;
}

.ul-btn-actions li ul {
	border-radius: 5px;
    position: absolute;
    display: none;
    width: 100%;
    margin: 0px;
    text-align: start;
    background: #ffffff00;
    z-index: 2;
}
.ul-btn-actions li:hover ul, .menu li.over ul{display:block;}

.ul-btn-actions li ul li {
    font-size: 1rem;
    display: block;
}

.fa-icon-down {
	margin: -5px -10px -10px 5px;
}

.toggle-editor.btn-toolbar.pull-right.clearfix {
	display:none !important;
}

@media (max-width: 600px){
    .row-fluid.footer-btn {
        display: inline-flex;
    }
	.row-fluid.footer-btn > .span6 {
		max-width: 50%;

	}
}

.dropdown-menu > li > a {
    white-space: normal !important;
}

.pull-right > .btn.btn-default{
    text-shadow: none !important;
	border: none !important;
    display: block !important;
	font-weight: 300 !important;
	margin: 0px !important;
	float: right;
    background-color: #032b43 !important;
    color: #fff !important;
}

.pull-right > .btn.btn-default:hover{
	color: #ED7D3A !important;
}



/* END - Your CSS styling ends here */

EOT;
