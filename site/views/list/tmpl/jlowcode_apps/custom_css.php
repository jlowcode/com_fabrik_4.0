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

#listform_$c {
	margin-top: 25px !important;
}


.fabrik_row.well.col-md-4.galery-div.span3 {
    background: #ccc;
    border-radius: 20px;
}

.row-fluid.fabrikDivElement a,
.row-fluid.fabrikDivElement{
    color: #fff;
    text-align: center;
}

a.btn.fabrik_view.fabrik__rowlink.btn-default{
    background-color: #00000050 !important;
    text-shadow: none;
    border: 0px;
    margin: 0px;
}



@-webkit-keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; } 
    }
    @-moz-keyframes fadeIn {
    0% { opacity: 0;}
    100% { opacity: 1; }
    }
    @-o-keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
    }
    @keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
    }

    .fadeIn {
        -webkit-animation: fadeIn 3s ease-in-out;
        -moz-animation: fadeIn 3s ease-in-out;
        -o-animation: fadeIn 3s ease-in-out;
        animation: fadeIn 3s ease-in-out;
    }

/* END - Your CSS styling ends here */
EOT;


