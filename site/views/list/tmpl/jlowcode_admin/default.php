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

JHtml::_('script', 'components/com_fabrik/views/list/tmpl/' . $this->getModel()->getFormModel()->getTmpl() . '/js/sortable.min.js', array('relative' => false));

if (!function_exists('getItens')) {
    function getItens($self, $parent)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $model = $self->getModel();
        $elements = $model->getElements('id');

        $query = $db->getQuery(true);
        $query->select(Array('*'));

        if ($parent == null) {
            $query->from($db->quoteName($self->table->db_table_name))->where($self->elTree . " IS NULL");
        } else {
            $query->from($db->quoteName($self->table->db_table_name))->where($self->elTree . " = " . $parent);
        }

        if($self->canShowTutorialTemplate) {
            $idElOrder = isset($self->getModel()->fieldsTemplateTutorial->ordering) ? $self->getModel()->fieldsTemplateTutorial->ordering : $self->getModel()->fieldsTemplateTutorial->field;
            $elOrder = $elements[$idElOrder];
            $query->order($elOrder->getElement()->get('name'));
        } else {
            $orders = json_decode($model->getTable()->get('order_by'));
            $orders_dir = json_decode($model->getTable()->get('order_dir'));
            foreach ($orders as $key => $idEl) {
                $el = $elements[$idEl];
                $query->order($el->getElement()->get('name') . ' ' . $orders_dir[$key]);
            }
        }

        $db->setQuery($query);
        $results = $db->loadObjectList();

        return $results;
    }
}

if (!function_exists('getItensChild')) {
    function getItensChild($db_table_name, &$self)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $model = $self->getModel();
        $elements = $model->getElements('id');
        $parent = isset($_POST['id']) ? intval($_POST['id']) : 0;

        foreach ($elements as $el) {
            $params = $el->getParams();
            if (
                str_contains($el->getName(), 'Databasejoin') && $params->get('database_join_display_type') == 'auto-complete'
                && $params->get('join_db_name') == $model->getTable()->get('db_table_name') &&
                ($params->get('database_join_display_style') == 'both-treeview-autocomplete' || $params->get('database_join_display_style') == 'only-treeview')
            ) {
                $elTree = $el->getParams()->get('tree_parent_id');
            }

            if (str_contains($el->getName(), 'Field') && is_null($self->elFieldTree)) {
                $self->elFieldTree = $el->element->name;
            }
        }

        $query = $db->getQuery(true);
        $query->select(array('*'))
            ->from($db->quoteName($db_table_name))
            ->where($elTree . " = " . $parent);

        if($self->canShowTutorialTemplate) {
            $idElOrder = isset($self->getModel()->fieldsTemplateTutorial->ordering) ? $self->getModel()->fieldsTemplateTutorial->ordering : $self->getModel()->fieldsTemplateTutorial->field;
            $elOrder = $elements[$idElOrder];
            $query->order($elOrder->getElement()->get('name'));
        } else {
            $orders = json_decode($model->getTable()->get('order_by'));
            $orders_dir = json_decode($model->getTable()->get('order_dir'));
            foreach ($orders as $key => $idEl) {
                $el = $elements[$idEl];
                $query->order($el->getElement()->get('name') . ' ' . $orders_dir[$key]);
            }
        }

        $db->setQuery($query);
        $results = $db->loadObjectList();

        return $results;
    }
}

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

?> 

<div class="fabrik-list">

<?php
$this->headingsHtml = $this->loadTemplate('headings');
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

$this->modalLearnMore = Array('callModal' => 0);
echo $this->loadTemplate('header');

?>

<div id="loadingModal" class="modal">
    <div class="spinner"></div>
</div>

<?php

switch ($modoExibicao["template"]) {
    case 'list':
    case '0':
        echo $this->loadTemplate('subrender_list');
        break;

    case 'grid':
    case '1':
        echo $this->loadTemplate('subrender_grid');
        break;

    case 'tree':
    case '2':
        echo $this->loadTemplate('subrender_tree');
        break;

    case 'tutorial':
    case '3':
        echo $this->loadTemplate('subrender_tutorial');
        break;
}

echo $this->table->outro;
if ($pageClass !== '') :
    echo '</div>';
endif;
?>
</div>