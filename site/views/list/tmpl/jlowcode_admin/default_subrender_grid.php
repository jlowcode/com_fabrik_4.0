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

// The number of columns to split the list rows into
$columns = 3;
// Show the labels next to the data:
$this->showLabels = false;
// Show empty data
$this->showEmpty = true;

?>
<form class="fabrikForm" action="<?php echo $this->table->action; ?>" method="post" id="<?php echo $this->formid; ?>" name="fabrikList">
    <div class="<?php echo $this->params['show-table-filters'] === '6' ? 'row' : ''; ?>">
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
                        //document.getElementById('list_<?php echo $this->table->renderid; ?>').toggle();
                    };
                </script>
            <?php
                echo $this->loadTemplate('table_aditional_ajax');
            endif;
            // End workflow code
            ?>
        </div>
        <div class="<?php echo $this->params['show-table-filters'] === '6' ? ' col-md-2 span2 ' : '';
                    echo $this->showFilters === true ? 'filterContentNotEmpty' : '' ?>" style="margin-bottom: 30px">

            <?php
            if ($this->showFilters) {
                echo $this->layoutFilters();
            }
            ?>
        </div>

        <div class="subrender-grid listContent fabrikDataContainer<?php echo $this->params['show-table-filters'] === '6' ? ' col-md-9 span9' : ''; ?>" data-cols="<?php echo $columns; ?>" style="">
            <?php foreach ($this->pluginBeforeList as $c) {
                echo $c;
            } ?>
            <div class="fabrikList" id="list_<?php echo $this->table->renderid; ?>">
                <table style="<?php echo $cssWidth; ?>" class="<?php echo $this->list->class; ?>" id="list_<?php echo $this->table->renderid; ?>">
                    <colgroup>
                        <?php foreach ($this->headings as $key => $heading) : ?>
                            <col class="col-<?php echo $key; ?>">
                        <?php endforeach; ?>
                    </colgroup>
                    <tfoot>
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
                    <?php
                    if ($this->isGrouped) :
                        $imgProps = array('alt' => FText::_('COM_FABRIK_TOGGLE'), 'data-role' => 'toggle', 'data-expand-icon' => 'fa fa-arrow-down', 'data-collapse-icon' => 'fa fa-arrow-right');
                    ?>
                        <div class="fabrik_groupheading">
                            <?php echo $this->layoutGroupHeading($groupedBy, $group); ?>
                        </div>
                    <?php
                    endif;
                    ?>
                    <div class="fabrik_groupdata">
                        <div class="groupDataMsg">
                            <div class="emptyDataMessage" style="<?php echo $this->emptyStyle ?>">
                                <?php echo $this->emptyDataMessage; ?>
                            </div>
                        </div>
                        <?php
                        $items = array();
                        foreach ($group as $this->_row) :
                            $items[] = $this->loadTemplate('row_gallery');
                            $ids[] = $this->_row->id;
                        endforeach;

                        $class = 'fabrik_row well col-md-4 galery-div';
                        echo FabrikHelperHTML::bootstrapGrid($items, $columns, $class, true, $ids);
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