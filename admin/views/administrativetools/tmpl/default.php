<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>

<div id="j-main-container" class="span10">
    <div id="j-main-container">
        <ul class="nav nav-tabs">
            <li class="<?php echo $this->tab1;?>"><a href="#transformation" data-toggle="tab"><?php echo FText::_('COM_FABRIK_ADMINISTRATIVETOOLS_TAB_TITLE_TRANSFORMATION1');?></a></li>
            <li class="<?php echo $this->tab2;?>"><a href="#haversting" data-toggle="tab"><?php echo FText::_('COM_FABRIK_ADMINISTRATIVETOOLS_TAB_TITLE_TRANSFORMATION2');?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane <?php echo $this->tab1;?>" id="transformation"><?php echo $this->loadTemplate('element_transformation'); ?></div>
            <div class="tab-pane <?php echo $this->tab2;?>" id="haversting"><?php echo $this->loadTemplate('haversting'); ?></div>
        </div>
    </div>
</div>