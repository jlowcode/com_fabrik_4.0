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

.filterContent .span12 {
	width: 91.48936170212765%;
}

.filterContent .span11 {
	width: 82.97872340425532%;
}

.filterContent .span10 {
	width: 74.46808510638297%;
}

.filterContent .span9 {
	width: 65.95744680851064%;
}

.filterContent .span8 {
	width: 57.44680851063829%;
}

.filterContent .span7 {
	width: 48.93617021276595%;
}

.filterContent .span6 {
	width: 40.42553191489362%;
}

.filterContent .span5 {
	width: 31.914893617021278%;
}

.filterContent .span4 {
	width: 23.404255319148934%;
}

.filterContent .span3 {
	width: 14.893617021276595%;
}

.filterContent .span2 {
	width: 6.382978723404255%;
}

.filterContent .span1 {
	width: 6.382978723404255%;
}

.heading.fabrik_ordercell {
    max-width:15%;
    width:15% !important;
}

.addbutton.addRecord{
	background: #032B43;
    border-radius: 5px;
    color: #fff;
}

.page-header{
	border-bottom: 1px solid #508AA8;
    color: #032B43;
}

.fabrik_filter.search-query.input-medium{
	border: 1px solid #508AA8;
}


/*Joyce*/
.heading.fabrik_ordercell.fabrik_actions .btn-group,
.fabrik_actions.fabrik_element .btn-group {
    display:flex !important;
}

/*Joyce*/
.btn.fabrik_view,
.btn.fabrik_edit,
.btn.btn-default.delete,
.btn.php-1.listplugin.btn-default,
.btn.php-3.listplugin.btn-default {
    background-color: #e3ecf1 !important;
    padding: 0.2rem 0.4rem;
    border-radius: 5px !important;
    margin-left: 5px !important;
    border: 1px solid #BAD0DC !important;
    width: 100% !important;
}

/*Joyce*/
.btn.btn-default.delete {
    color: #A41623 !important;
}

/*Joyce*/
.btn.fabrik_edit,
.btn.fabrik_edit:hover {
    color: #0E534A !important;
}

/*Joyce*/
.btn.fabrik_view,
.btn.fabrik_view:hover {
    color: #054267 !important;
}

/*Joyce*/
.btn.php-1.listplugin.btn-default,
.btn.php-1.listplugin.btn-default:hover,
.btn.php-3.listplugin.btn-default,
.btn.php-3.listplugin.btn-default:hover {
    color: #A41623 !important;
}

.fabrik_actions .btn-group {
    padding: 0rem !important;
    float: right;
}

/*Joyce*/
#listform_104_mod_fabrik_list_136 .addbutton.addRecord{
    display:none;
}

/* END - Your CSS styling ends here */
EOT;
