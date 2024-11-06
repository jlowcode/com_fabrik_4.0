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

JHtml::_('script', 'components/com_fabrik/views/list/tmpl/jlowcode_admin/sortable.min.js', array('version' => 'auto', 'relative' => false));
$db = Factory::getContainer()->get('DatabaseDriver');
$input = Factory::getApplication()->input;

// Obtém o valor enviado através do POST
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    switch ($action) {
        case 'getFilhos':
            $paiId = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $db_table_name = $this->table->db_table_name;
            $results = getItensChild($db_table_name, $this);
            foreach ($results as $index => $value) {
                $this->_row = $this->_models["list"]->getRow($value->id, true);
                $results[$index]->actions = $this->loadTemplate('row_tree');
            }
            echo json_encode($results);
            exit();
        default:
            $_SESSION['modo']['template'] = $_POST['modo'];
            $_SESSION['modo']['lista'] = $this->table->db_table_name;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
    }
}

if (isset($_SESSION['modo']) && $_SESSION['modo']['lista'] == $this->table->db_table_name) {
    $modoExibicao = $_SESSION['modo'];
} else if($input->get('layout_mode')) {
    $modoExibicao["template"] = $input->get('layout_mode');
} else if ($this->params->get('layout_mode')) {
    $modoExibicao["template"] = $this->params->get('layout_mode');
} else {
    $modoExibicao["template"] = 'list';
}

$headingsHtml = $this->loadTemplate('headings');
// Workflow code
echo $this->loadTemplate('modal');
// End workflow code

$pageClass = $this->params->get('pageclass_sfx', '');
if ($pageClass !== '') :
    echo '<div class="' . $pageClass . '">';
endif;

if ($this->tablePicker != '') : ?>
    <div style="text-align:right"><?php echo Text::_('COM_FABRIK_LIST') ?>: <?php echo $this->tablePicker; ?></div>
<?php
endif;

if ($this->params->get('show_page_heading')) :
    echo '<h1>' . $this->params->get('page_heading') . '</h1>';
endif;

$idList = $this->list->id;
$query = $db->getQuery(true);
$query->select('miniatura')->from('adm_cloner_listas')->where('id_lista = ' . $idList);
$db->setQuery($query);
$miniatura = $db->loadResult();

if($miniatura) { ?>
    <div style="display: flex; padding-bottom: 10px; border-bottom: 2px solid #eee;">
        <img style="margin-right: 50px; width: 300px; object-fit=contain" src="<?php echo $miniatura; ?>"/>
        <?php echo $this->loadTemplate('header'); ?>
    </div>
<?php } else { ?>
    <div style="display: flex; border-bottom: 2px solid #eee;">
        <?php echo $this->loadTemplate('header'); ?>
    </div>
<?php }

if($this->table->intro) : ?>

<div class="intro-container">
    <div class="text-intro-content">
            <?php echo $this->table->intro; ?>
    </div>
    <i class="fa fa-angle-down" aria-hidden="true"></i>
</div>

<?php endif; ?>

<div id="loadingModal" class="modal">
    <div class="spinner"></div>
</div>

<?php
if ($modoExibicao["template"] == 'list' || $modoExibicao["template"] == '0') {
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
                //for some really ODD reason loading the headings template inside the group
                //template causes an error as $this->_path['template'] doesn't contain the correct
                // path to this template - go figure!
                //$headingsHtml = $this->loadTemplate('headings');
                echo $this->loadTemplate('tabs'); ?>
            </div>
            <div style="<?php echo $cssOverflow; ?>" class="listContent fabrikDataContainer<?php echo in_array($this->params['show-table-filters'], [6]) && $this->showFilters ? ' col-md-12 col-lg-9' : ''; ?>">

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
                    <tfoot>
                        <tr class="fabrik___heading">
                            <td colspan="<?php echo count($this->headings); ?>">
                                <?php echo $this->nav; ?>
                            </td>
                        </tr>
                    </tfoot>
                    <thead><?php echo $headingsHtml ?></thead>
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
    </form>
    <?php
    echo $this->table->outro;
    if ($pageClass !== '') :
        echo '</div>';
    endif;
}

if ($modoExibicao["template"] == 'grid' || $modoExibicao["template"] == '1') {
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

            <div class="fabrikDataContainer<?php echo $this->params['show-table-filters'] === '6' ? ' col-md-9 span9' : ''; ?>" data-cols="<?php echo $columns; ?>" style="">
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
                        <thead><?php echo $headingsHtml ?></thead>
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
                            endforeach;
                            $class = 'fabrik_row well col-md-4 galery-div';
                            echo FabrikHelperHTML::bootstrapGrid($items, $columns, $class, true, $this->_row->id);
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
    <?php
    echo $this->table->outro;

    if ($pageClass !== '') :
        echo '</div>';
    endif;
} 

if ($modoExibicao["template"] == 'tree' || $modoExibicao["template"] == '2') {

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
            <div class="fabrikDataContainer col-md-12 span9" data-cols="<?php echo $columns; ?>" style="float: right">
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
                        <thead><?php echo $headingsHtml ?></thead>
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
<?php
    echo $this->table->outro;
    if ($pageClass !== '') :
        echo '</div>';
    endif;
}

if (!function_exists('getItens')) {
    function getItens($self, $parent)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $model = $self->getModel();
        $elements = $model->getElements('id');

        $query = $db->getQuery(true);
        $query->select(Array('*'));

        if ($parent == null) {
            $query->from($db->quoteName($self->table->db_table_name))->where("parent IS NULL");
        } else {
            $query->from($db->quoteName($self->table->db_table_name))->where("parent = " . $parent);
        }

        $orders = json_decode($model->getTable()->get('order_by'));
        $orders_dir = json_decode($model->getTable()->get('order_dir'));
        foreach ($orders as $key => $idEl) {
            $el = $elements[$idEl];
            $query->order($el->getElement()->get('name') . ' ' . $orders_dir[$key]);
        }

        $db->setQuery($query);
        $results = $db->loadObjectList();

        return $results;
    }
}

if (!function_exists('getItensChild')) {
    function getItensChild($db_table_name, $self)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $model = $self->getModel();
        $elements = $model->getElements('id');
        $parent = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        $query = $db->getQuery(true);
        $query->select(array('*'))
            ->from($db->quoteName($db_table_name))
            ->where("parent = " . $parent);

        $orders = json_decode($model->getTable()->get('order_by'));
        $orders_dir = json_decode($model->getTable()->get('order_dir'));
        foreach ($orders as $key => $idEl) {
            $el = $elements[$idEl];
            $query->order($el->getElement()->get('name') . ' ' . $orders_dir[$key]);
        }
        
        $db->setQuery($query);
        $results = $db->loadObjectList();

        return $results;
    }
}

?>