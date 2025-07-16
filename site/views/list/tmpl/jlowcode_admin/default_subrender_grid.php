<?php

/**
 * Bootstrap List Template - Default
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

$document = Factory::getDocument();
$document->addStyleSheet('components/com_fabrik/views/list/tmpl/' . $this->getModel()->getFormModel()->getTmpl() . '/css/subRenderGrid.css');
$document->addStyleSheet('components/com_fabrik/views/list/tmpl/' . $this->getModel()->getFormModel()->getTmpl() . '/css/subRenderGridCard.css');

$columns = 3;
?>
<form class="fabrikForm" action="<?php echo $this->table->action; ?>" method="post" id="<?php echo $this->formid; ?>" name="fabrikList">
    <div class="<?php echo in_array($this->params['show-table-filters'], [6, 7]) ? 'row' : ''; ?>" style="width: 100%;">
        <div class="<?php echo $this->params['show-table-filters'] === '6' ? 'col-md-12' : ''; ?>">
            <?php
            if ($this->hasButtons) :
                echo $this->loadTemplate('buttons');
            endif;
            // Workflow code
            if ($_REQUEST['workflow']['showEventsButton'] == true) :
            ?>
                <script type="text/javascript">
                    function showRequests() {
                        document.getElementById('eventsContainer').toggle();
                    };
                </script>
            <?php
                echo $this->loadTemplate('table_aditional_ajax');
            endif;
            // End workflow code
            ?>
        </div>

        <div class="<?php 
            echo $this->params['show-table-filters'] === '6' ? ' col-md-2 span2 ' : ''; 
            echo $this->showFilters === true ? 'filterContentNotEmpty' : '' ?>">

            <?php 
                if ($this->showFilters) :
                    echo $this->layoutFilters();
                endif;
            ?>
        </div>

        <div class="subRenderGridCard subRenderGrid hideThead listContent fabrikDataContainer<?php echo $this->params['show-table-filters'] === '6' ? ' col-md-9 span9' : ''; ?>" data-cols="<?php echo $columns; ?>" style="">
            <?php 
                foreach ($this->pluginBeforeList as $c) :
                    echo $c;
                endforeach;
            ?>

            <div class="fabrikList" id="list_<?php echo $this->table->renderid; ?>">
                <table style="<?php echo $cssWidth; ?>" class="<?php echo $this->list->class; ?>" id="list_<?php echo $this->table->renderid; ?>">
                    <colgroup>
                        <?php foreach ($this->headings as $key => $heading) : ?>
                            <col class="col-<?php echo $key; ?>">
                        <?php endforeach; ?>
                    </colgroup>
                    <tfoot class="d-none">
                        <tr class="fabrik___heading">
                            <td colspan="<?php echo count($this->headings); ?>">
                            </td>
                        </tr>
                    </tfoot>
                    <thead><?php echo $this->headingsHtml ?></thead>
                </table>
                <?php
                $gCounter = 0;
                foreach ($this->rows as $groupedBy => $group) : ?>
                    <?php if ($this->isGrouped) : ?>
                        <div class="fabrik_groupheading">
                            <?php echo $this->layoutGroupHeading($groupedBy, $group); ?>
                        </div>
                    <?php endif; ?>

                    <div class="fabrik_groupdata d-flex flex-column">
                        <div class="groupDataMsg">
                            <div class="emptyDataMessage" style="<?php echo $this->emptyStyle ?>">
                                <?php echo $this->emptyDataMessage; ?>
                            </div>
                        </div>

                        <?php
                            $items = array();
                            foreach ($group as $this->_row) :
                                $items[] = $this->loadTemplate('row_grid');
                                $ids[] = $this->_row->id;
                            endforeach;

                            $class = 'fabrik_row well gallery-div';
                            $classRow = 'flex-column flex-md-row';
                            echo FabrikHelperHTML::bootstrapGridCards($items, $columns, $class, true, $ids, $classRow);
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php
            echo $this->nav;
            print_r($this->hiddenFields); ?>
        </div>
    </div>
</form>