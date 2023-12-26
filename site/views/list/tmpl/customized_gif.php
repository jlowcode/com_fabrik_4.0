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
$customCssLoading = '';
// if the user set a different path for loading gif
if(isset($_REQUEST['loadinggif']) && !empty($_REQUEST['loadinggif'])){
    $basicUrl = "http://" . $_SERVER['HTTP_HOST'];
    $customLoadingGif = $basicUrl . '/' . $_REQUEST['loadinggif'];
    $width = getimagesize($customLoadingGif)[0];
    $height = getimagesize($customLoadingGif)[1];
    $customCssLoading = '.spinner-img { background: url(' . $customLoadingGif . ') no-repeat; width: ' . $width . 'px; height: ' . $height . 'px; margin: 0 auto; }';
}

echo "
    $customCssLoading

    .spinner-content {
        top: 40px !important;
    }
";?>
