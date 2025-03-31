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

$customCssLoading

";?>
