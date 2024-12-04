<?php
/**
 * Fabrik List Template: Bootstrap
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

.fabrikDataContainer {
	clear:both;
	/*
		dont use this as it stops dropdowns from showing correctly
		overflow: auto;*/
		
}

.fabrikDataContainer .pagination a{
	float: left;
}

ul.fabrikRepeatData {
	list-style: none;
    list-style-position:inside;
	margin: 0;
	padding-left: 0;
}

td.repeat-merge div, td.repeat-reduce div,
td.repeat-merge i, td.repeat-reduce i {
padding: 5px !important;
}

@media only screen and (min-width: 360px) {
    .show-mobile {
      display: block;
    }
    .hide-mobile {
      display: none;
    }
  }

@media only screen and (min-width: 1023px) {
    .show-mobile {
      display: none;
    }
    .hide-mobile {
      display: block;
    }
}

.nav li {
list-style: none;
}

.filtertable_horiz {
	display: inline-block;
	vertical-align: top;
}

/* Left filter layout */

.buttons {
	height: 600px;
	overflow: hidden;
}

.row {
	display: flex;
}

.column {
}

/*Workflow css code*/


.loader {
    position: relative;
    margin: 0 auto;
    width: 50px;
}
.loader:before {
    content: '';
    display: block;
    padding-top: 100%;
}
.circular {
    animation: rotate 2s linear infinite;
    height: 100%;
    transform-origin: center center;
    width: 100%;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    margin: auto;
}
.path {
    stroke-dasharray: 1, 200;
    stroke-dashoffset: 0;
    animation: dash 1.5s ease-in-out infinite, color 6s ease-in-out infinite;
    stroke-linecap: round;
}
@keyframes rotate {
100% {
    transform: rotate(360deg);
}
}
@keyframes dash {
0% {
    stroke-dasharray: 1, 200;
    stroke-dashoffset: 0;
}
50% {
    stroke-dasharray: 89, 200;
    stroke-dashoffset: -35px;
}
100% {
    stroke-dasharray: 89, 200;
    stroke-dashoffset: -124px;
}
}
@keyframes color {
100%, 0% {
    stroke: #d62d20;
}
40% {
    stroke: #0057e7;
}
66% {
    stroke: #008744;
}
80%, 90% {
    stroke: #ffa700;
}
}
body {
    background-color: #eee;
}
.showbox {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 5%;
}


.modalContainer {
	z-index: 2;
	position: fixed;
	display: none;
	background: rgba(0,0,0,0.5);
	top: 0;
	left: 0;
	height: 100vh;
	width: 100%;
}

.modalContent {
	position: relative;
	background-color: #FFF;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	height: 80%;
	width: 50%;
	min-width: 600px;
	padding: 20px;
	overflow:auto;
}

.modalCloseBtn {
	position: absolute;
	right: 0;
	top: 0;
	margin-right: 20px;
	font-size:20px;
	cursor: pointer;
	font-weight: bold;
	z-index: 100;
}

/*Workflow css code end*/

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
