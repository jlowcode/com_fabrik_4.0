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

/** Hide the checkbox in each record*/
@import '../pitt-bootstrap.css';

$customCssLoading 

@media only screen and (min-width: 360px) {
    .show-mobile {
      display: block;
    }
    .hide-mobile {
      display: none;
    }
  }

@media only screen and (min-width: 1680px) {
    .show-mobile {
      display: none;
    }
    .hide-mobile {
      display: block;
    }
}

ul.fabrikRepeatData {
    overflow:hidden;
}

a:hover { 
    color: #06c !important;
}

hr {
    border-style: solid none;
    border-width: 1px 0;
}
.card-container {
    display: flex;
    width: 100%;
    padding: 20px;
}

.item {
    height: 100%;
    padding-top: 10px;
}
.align-right {
    margin-left: auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.card-button {
}
.cards-body {
    margin-right: auto;
}

.btn-group {
    position: absolute !important;
    right: 0px;
}

.fabrikLightBoxImage {
	max-width: 100%;
}

//cards css end



#listform_$c .fabrikList input[type=checkbox] {
	display: none;
}
#listform_$c .well {
	position: relative;
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

.btn-group {
    float: right;
    position: relative !important;
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

";
