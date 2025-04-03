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


// Show the labels next to the data:
$this->showLabels = true;
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
        <div class="subrender-tree listContent fabrikDataContainer col-md-12 span9" data-cols="<?php echo $columns; ?>" style="float: right">
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
                <div id="registros-container">
                    <?php
                        $self = $this;
                        $itens = getItens($self, null);

                        foreach ($itens as $row) {
                            $this->_row = $this->_models["list"]->getRow($row->id, true);
                            echo $this->loadTemplate('row_tree');
                        }
                    ?>
                </div>
            </div>
            <?php
            print_r($this->hiddenFields); ?>
        </div>
    </div>
</form>