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

$width_list = (int) $this->params->get('width_list');
if ($width_list) {
    if ($width_list > 100) $cssOverflow = 'overflow-x: scroll;';
    $cssWidth = "width: $width_list%;";
}

?>
<form class="fabrikForm form-search" action="<?php echo $this->table->action; ?>" method="post" id="<?php echo $this->formid; ?>" name="fabrikList" style="width: 100%;">
    <div class="<?php echo in_array($this->params['show-table-filters'], [6, 7]) ? 'row' : ''; ?>" style="width: 100%;">
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
        <div class="filterContent fabrikFilterContainer <?php echo in_array($this->params['show-table-filters'], [6, 7]) && $this->showFilters ? ' col-md-12 col-lg-3 ' : '';
                                                        echo $this->showFilters === true ? 'filterContentNotEmpty' : '' ?>">
            <?php
            if ($this->showFilters && $this->bootShowFilters) :
                echo $this->layoutFilters();
            endif;
            echo $this->loadTemplate('tabs'); ?>
        </div>
        <div style="<?php echo $cssOverflow; ?>" class="subrender-list listContent fabrikDataContainer<?php echo in_array($this->params['show-table-filters'], [6]) && $this->showFilters ? ' col-md-12 col-lg-9' : ''; ?>">

            <?php foreach ($this->pluginBeforeList as $c) :
                echo $c;
            endforeach;
            ?>
            <table style="<?php echo $cssWidth; ?>" class="<?php echo $this->list->class; ?>" id="list_<?php echo $this->table->renderid; ?>">
                <colgroup>
                    <?php foreach ($this->headings as $key => $heading) : ?>
                        <col class="col-<?php echo $key; ?>">
                    <?php endforeach; ?>
                </colgroup>
                
                <thead><?php echo $this->headingsHtml ?></thead>
                <?php
                if ($this->isGrouped && empty($this->rows)) :
                ?>
                    <tbody style="<?php echo $this->emptyStyle ?>">

                    </tbody>
                    <?php
                endif;
                $gCounter = 0;
                foreach ($this->rows as $groupedBy => $group) :
                    if ($this->isGrouped) : ?>
                        <tbody>
                            <tr class="fabrik_groupheading info">
                                <td colspan="<?php echo $this->colCount; ?>">
                                    <?php echo $this->layoutGroupHeading($groupedBy, $group); ?>
                                </td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                    <tbody class="fabrik_groupdata <?php echo ($this->table->db_table_name); ?>">

                        <?php
                        foreach ($group as $this->_row) :
                            echo $this->loadTemplate('row');
                        endforeach
                        ?>
                    </tbody>

                    <?php if ($this->hasCalculations) : ?>
                        <tfoot>
                            <tr class="fabrik_calculations">

                                <?php
                                foreach ($this->headings as $key => $heading) :
                                    $h = $this->headingClass[$key];
                                    $style = empty($h['style']) ? '' : 'style="' . $h['style'] . '"'; ?>
                                    <td class="<?php echo $h['class'] ?>" <?php echo $style ?>>
                                        <?php
                                        $cal = $this->calculations[$key];
                                        echo array_key_exists($groupedBy, $cal->grouped) ? $cal->grouped[$groupedBy] : $cal->calc;
                                        ?>
                                    </td>
                                <?php
                                endforeach;
                                ?>

                            </tr>
                        </tfoot>
                    <?php endif ?>
                <?php
                    $gCounter++;
                endforeach ?>
            </table>
            <?php
            print_r($this->hiddenFields);
            ?>
        </div>

        <tfoot>
            <tr class="fabrik___heading">
                <td colspan="<?php echo count($this->headings); ?>">
                    <?php echo $this->nav; ?>
                </td>
            </tr>
        </tfoot>
</form>