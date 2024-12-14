<?php

/**
 * JLowCode List Template - Header
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.1
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$menu = $app->getMenu();

$url = "index.php?option=com_fabrik&view=list&listid={$this->get('id')}";
$menuLinked = $menu->getItems('link', $url, true);
$route = $menuLinked->route;
$link = '/' . (isset($route) ? $route : $url);

// Workflow code
if (isset($_REQUEST['workflow'])) {
	$this->showAddRequest = $_REQUEST['workflow']['showAddRequest'];
	$this->addRequestLink = $_REQUEST['workflow']['addRequestLink'];
	$this->requestLabel = $_REQUEST['workflow']['requestLabel'];
} else {
	$this->showAddRequest = null;
	$this->addRequestLink = null;
	$this->requestLabel = null;
}
// End workflow code

// Action code 
if (isset($_REQUEST['action']) && isset($_REQUEST['action']['showButton'])) {
	$this->showActionButton = $_REQUEST['action']['showButton'];
} else {
	$this->showActionButton = null;
}
// End action code 

?>
<div class="header">
    <div class="header_titles">
        <?php if ($this->params->get('show_page_heading')) :
            echo '<h1>' . $this->params->get('page_heading') . '</h1>';
        endif;

        if ($this->showTitle == 1) : ?>
            <div class="page-header">
                <span class="owner-name"><?php echo $this->owner_user->get('name'); ?></span>

                <?php if($app->input->get('listid') != $this->get('id')) { ?>
                    <h1><a href="<?php echo $link ?>"><?php echo $this->table->label; ?></a></h1>
                <?php } else { ?>
                    <h1><?php echo $this->table->label; ?></h1>
                <?php } ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="header_buttons">
    <?php
        if ($this->showAdd) : ?>
            <li>
                <a class="addbutton addRecord" href="<?php echo $this->addRecordLink; ?>">
                    <?php echo FabrikHelperHTML::icon('icon-plus', $this->addLabel); ?>
                </a>
            </li>
        <?php
        endif;
        // Workflow code
        if ($this->showAddRequest) :?>
            <li>
                <a class="addbutton addRecord" href="<?php echo $this->addRequestLink;?>">
                <?php echo FabrikHelperHTML::icon('icon-plus', $this->addLabel);?>
                </a>
            </li>
        <?php
        endif;
        // End workflow code

        // Action code 
        if ($this->showActionButton) :?>
            <li>
                <a class="actionButton"></a>
            </li>
        <?php endif;
        // End action code ?>
    </div>
</div>
