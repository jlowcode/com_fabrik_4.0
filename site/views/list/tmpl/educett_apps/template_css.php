<?php
/**
 * Fabrik List Template: Div CSS
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

header('Content-type: text/css');
$c = $_REQUEST['c'];
$buttonCount = (int) $_REQUEST['buttoncount'];
$buttonTotal = $buttonCount === 0 ? '100%' : 30 * $buttonCount ."px";

echo "
/** Hide the checkbox in each record*/

#listform_$c .fabrikList input[type=checkbox] {
	display: none;
}
#listform_$c .well {
	position: relative;
}

ul.fabrikRepeatData {
    overflow:hidden;
}

#listform_$c .fabrik_action {
	position: absolute;
	top: 10px;
	right: 10px;
}

.filtertable_horiz {
	display: inline-block;
	vertical-align: top;
}

.well {
    background-color: #ffffff;
}

.list-striped, .row-striped {
    border: 1px solid #e0e0e5;
}

.row-striped .row-fluid {
	background-color: #ffffff !important;
	border-bottom: none !important;
	padding: 0 !important;
}

//Fixing autocomplete dropdown list
.dropdown-menu > li > a {
    white-space: normal !important;
}

.fabrik_filter {
	max-width : 100% !important;
    border-radius: 4px 4px 4px 4px !important;
	-webkit-border-radius: 4px 4px 4px 4px !important;
    -moz-border-radius: 4px 4px 4px 4px !important;
}

";?>
