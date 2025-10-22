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

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

$app = Factory::getApplication();
$model = $this->getModel();

$tmpl = $model->getFormModel()->getTmpl();
$document = $app->getDocument();
$columns = 100;

$document->addStyleSheet('components/com_fabrik/views/list/tmpl/' . $tmpl . '/css/subRenderMasonry.css');
$document->addStyleSheet('components/com_fabrik/views/list/tmpl/' . $tmpl . '/css/subRenderGrid.css');
$document->addStyleSheet('components/com_fabrik/views/list/tmpl/' . $tmpl . '/css/subRenderGridCard.css');

$document->addScript(Uri::base() . 'components/com_fabrik/views/list/tmpl/' . $tmpl . '/js/masonry.min.js');
$document->addScript(Uri::base() . 'components/com_fabrik/views/list/tmpl/' . $tmpl . '/js/imagesloaded.min.js');

?>
<form class="fabrikForm" action="<?= $this->table->action; ?>" method="post" id="<?= $this->formid; ?>" name="fabrikList">
    <div class="<?= in_array($this->params['show-table-filters'], [6, 7]) ? 'row' : ''; ?>">
        <div class="<?= in_array($this->params['show-table-filters'], [6, 7]) ? 'col-md-12' : ''; ?>">
            <?php
            if ($this->hasButtons) :
                echo $this->loadTemplate('buttons');
            endif;
            // Workflow code
            if ($_REQUEST['workflow']['showEventsButton']) :
            ?>
                <script type="text/javascript">
                    function showRequests() {
                        document.getElementById('eventsContainer').toggle();
                    }
                </script>
            <?php
                echo $this->loadTemplate('table_aditional_ajax');
            endif;
            // End workflow code
            ?>
        </div>

        <div class="
            <?php 
                echo $this->showFilters === true ? 'filterContentNotEmpty' : '';
                echo in_array($this->params['show-table-filters'], [6, 7]) && $this->showFilters ? ' col-md-12 col-lg-3 ' : '';
            ?>">

            <?php 
                if ($this->showFilters && $this->bootShowFilters) :
                    echo $this->layoutFilters();
                endif;
            ?>
        </div>

        <div class="subRenderGridCard subRenderGrid subRenderMasonry hideThead listContent fabrikDataContainer<?= $this->params['show-table-filters'] === '6' ? ' col-md-9 span9' : ''; ?>" data-cols="<?php echo $columns; ?>">
            <?php 
                foreach ($this->pluginBeforeList as $c) :
                    echo $c;
                endforeach;
            ?>

            <div class="fabrikList" id="list_<?= $this->table->renderid; ?>">
                <table class="<?= $this->list->class; ?>" id="list_<?= $this->table->renderid; ?>">
                    <colgroup>
                        <?php foreach ($this->headings as $key => $heading) : ?>
                            <col class="col-<?= $key; ?>">
                        <?php endforeach; ?>
                    </colgroup>
                    <tfoot class="d-none">
                        <tr class="fabrik___heading">
                            <td colspan="<?= count($this->headings); ?>">
                            </td>
                        </tr>
                    </tfoot>
                    <thead><?= $this->headingsHtml ?></thead>
                </table>
                <?php
                $gCounter = 0;
                foreach ($this->rows as $groupedBy => $group) : ?>
                    <?php if ($this->isGrouped) : ?>
                        <div class="fabrik_groupheading">
                            <?= $this->layoutGroupHeading($groupedBy, $group); ?>
                        </div>
                    <?php endif; ?>

                    <div class="fabrik_groupdata">
                        <div class="row js-masonry">
                            <div class="masonry-box-sizer"></div>

                            <div class="groupDataMsg">
                                <div class="emptyDataMessage" style="<?= $this->emptyStyle ?>">
                                    <?= $this->emptyDataMessage; ?>
                                </div>
                            </div>

                            <?php foreach ($group as $this->_row) : ?>
                                <?= $this->loadTemplate('row_masonry'); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
            <?php
            echo $this->nav;
            print_r($this->hiddenFields); ?>
        </div>
    </div>
</form>