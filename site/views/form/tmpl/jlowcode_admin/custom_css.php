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

.fabrikSubElementContainer label {
	display: flex;
	align-items: center;
}

.select2-dropdown {
	border: 1px solid rgba(0, 0, 0, 0.15) !important;
    border-radius: 0.25rem !important;
}

.select2-dropdown .select2-results__options {
	padding: 15px 0px;
}

.select2-results__option.select2-results__message {
    color: #3a87ad !important;
    background-color: #e2eff5;
	padding: 15px;
	border: 1px solid #c7e0ec;
    border-radius: 4px;
}

.modal-content {
    border-radius: 20px !important;
    box-shadow: 0px 6px 6px 0px #3c3939;
}

.fabrikElement .button {
	color: #333840 !important;
}

.fabrikgrid_radio label {
	margin-bottom: 0px !important;
	font-weight: 400 !important;
}

.fabrikgrid_radio {
	display: flex !important;
    align-items: center !important;
}

.btn-check {
	position: relative !important;
}

#fabrik-comments .button {
	color: #333840 !important;
}

/* Begin - Fileupload style */

.plupload table {
	border: 2px solid #eee !important;
	border-radius: 12px !important;
}

.plg-fileupload input {
	padding: 10px;
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

main .platform-content {
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
	color: #C2F6F9 !important;
}

.btn-save-new{
	background-color: #0a85d0 !important;
}
.btn-save-copy{
	background-color: #2591d4 !important;
}
.btn-save-details{
	background-color: #08629a !important;
}
.btn-save-back{
	background-color: #032b43 !important;
}
.btn-save-only{
	background-color: #054267 !important;
}

.btn-cancel-back {
	background-color: #1D1D1D !important;
}

.btn-reset {
	background-color: #3D3D3D !important;
}

.btn-delete {
	background-color: #5D5D5D !important;
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

@media (max-width: 700px){
    .row-fluid.footer-btn {
        display: inline-flex;
    }
	.row-fluid.footer-btn > .span6 {
		max-width: 50%;

	}

	main .platform-content {
    	padding: 5px !important;
	}

	.footer-btn {
		flex-direction: column
	}

	.footer-btn > div {
	    justify-content: center;
	    display: flex;
	}

	.g-content {
		padding: 1rem !important;
	}
}

.select2-results__option {
    white-space: normal !important;
	color: #011627 !important;
}

.dropdown-menu > li > a:hover {
	font-weight: 700;
	background: none !important;
}

.select2-dropdown ul > li:hover {
	font-weight: 700;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
	background-color: inherit !important;
}

.select2-container--default .select2-results__option[aria-selected=true] {
	background-color: #ddd !important;
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
	color: #C2F6F9 !important;
}



/* END - Your CSS styling ends here */

EOT;
