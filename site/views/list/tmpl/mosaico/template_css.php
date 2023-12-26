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
$customCssLoading = '';
// if the user set a different path for loading gif
if(isset($_REQUEST['loadinggif']) && !empty($_REQUEST['loadinggif'])){
    $basicUrl = "http://" . $_SERVER['HTTP_HOST'];
    $whatINeed = explode('/', $_SERVER['REQUEST_URI'])[1];
    $customLoadingGif = $basicUrl . '/' . $whatINeed . '/' . $_REQUEST['loadinggif'];
    $customCssLoading = '.spinner-img { background: url(' . $customLoadingGif . ') no-repeat; width: 60px; height: 80px; margin: 0 auto; }';
}

echo "
@import '../pitt-bootstrap.css';

$customCssLoading

ul.fabrikRepeatData {
    overflow:hidden;
}

/** Hide the checkbox in each record*/

.card-button {
}
.card-title {
	font-size: 10px;
}
.metadados-title {
	font-size: 20px;
}

.thumb-container {
    text-align: center;
	width:100% !important;
	oveflow: hidden;
}

.fabrikLightBoxImage {
	max-width: 100%;
}

//cards css end
.card-body {
    flex: 1 1 auto;
    padding: 1.0rem;
}

.row-fluid [class*='span'] {
	display: inline-block;
	page-break-inside: avoid;
}

.row-fluid .span4 {
    width: 100% !important;
}

/* temp fix for columns issue in alpha 4 */
.card-columns .card {
	column-break-inside: avoid;
    display: inline-block;
	width:100%;
	overflow: hidden; /* Fix for firefox and IE 10-11  */
	-webkit-column-break-inside: avoid; /* Chrome, Safari, Opera */
	page-break-inside: avoid; /* Firefox */
	break-inside: avoid; /* IE 10+ */
	break-inside: avoid-column;
}

#listform_$c .fabrikList input[type=checkbox] {
	display: none;
}
#listform_$c .well {
	position: relative;
}

.filtertable_horiz {
	display: inline-block;
	vertical-align: top;
}

.btn-group > .btn, .btn-group > .dropdown-menu, .btn-group > .popover {
    font-size: 14px;
    position: relative;
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
