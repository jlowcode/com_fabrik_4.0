<?php
/**
 * Fabrik Form View Template: Bootstrap Tab CSS
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

header('Content-type: text/css');
$c = (int) $_REQUEST['c'];
$view = isset($_REQUEST['view']) ? $_REQUEST['view'] : 'form';
echo "
.fabrikGroup {
    clear: left;
}

.b-menu {
    display: flex;
}

.b-menu > div:nth-child(1) {
    width: 20vw;
}

.b-menu > div:nth-child(2) {
    width: 78vw;
    margin-left: 2vw;
}

@media (max-width: 765px) {
    .b-menu {
        display: grid;
    }

    .b-menu > div {
        width: 100vw !important;
    }
}

.nav-pills {
    margin-top: 10px;
    display: grid;
}

.nav-pills > li > a {
    padding-top: 10px !important;
    padding-bottom: 10px !important;
    margin: 0 !important;
    line-height: 1.3em !important;
}

.nav-pills > .active > a,
.nav-pills > .active > a:hover, 
.nav-pills > .active > a:focus {
    background-color: #4db2b3 !important;
}

.legend {
    margin-top: -0.5rem !important;
}

.plg-btn_group > div > div > div {
    display: grid !important;
    gap: 1em;
    font-size: inherit !important;
    margin-top: 10px;
}

.btn-group > button {
    border-radius: 5px !important;
}

/* color & highlight group with validation errors */
.fabrikErrorGroup a {
    background-color: rgb(242, 222, 222) !important;
    color: #b94a48;
}
 
.active.fabrikErrorGroup a,
.active.fabrikErrorGroup a:hover,
.active.fabrikErrorGroup a:focus {
    border: 1px solid #b94a48 !important;
    border-bottom-color: transparent !important;
    color: #b94a48 !important;
    background-color: rgb(255, 255, 255) !important;
}
 
.fabrikErrorGroup a:hover,
.fabrikErrorGroup a:focus {
    background-color: rgb(222, 173, 173) !important;
    color: #b94a48;
}";
?>