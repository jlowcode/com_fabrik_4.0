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

?>
<div class="header">
    <div class="header_titles">
        <?php if ($this->params->get('show_page_heading')) :
            echo '<h1>' . $this->params->get('page_heading') . '</h1>';
        endif;

        if ($this->showTitle == 1) : ?>
            <div class="page-header">
                <h1><?php echo $this->table->label; ?></h1>
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
                <a class="addbutton addRecord" href="<?php echo $this->addRequestLink;?>" style="margin-left: 20px">
                <?php echo FabrikHelperHTML::icon('icon-plus', $this->requestLabel);?>
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
