<?php

/**
 * Button to open events
 *
 * @package     Joomla
 * @subpackage  Fabrik.list.workflow
 * @copyright   Copyright (C) 2018-2018  Marcel Ferrante - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

$listModel = $this->_models["list"];
$elsList = $listModel->getElements('id');
$tree = false;

foreach ($elsList as $el) {
    $params = $el->getParams();
    if (
        str_contains($el->getName(), 'Databasejoin') && $params->get('database_join_display_type') == 'auto-complete'
        && $params->get('join_db_name') == $listModel->getTable()->get('db_table_name') &&
        ($params->get('database_join_display_style') == 'both-treeview-autocomplete' || $params->get('database_join_display_style') == 'only-treeview')
    ) {
        $this->elTree = $el->getParams()->get('tree_parent_id');
        $tree = true;
    }

    if (str_contains($el->getName(), 'Field') && is_null($this->elFieldTree)) {
        $this->elFieldTree = $el->element->name;
    }
}

?>

<div style="color: #011627" class="title">Exibição: </div>
<div class="radio-group">
    <span type="radio" id="list-view" name="view" onclick="handleRadioClick(this)">
        <?php echo FabrikHelperHTML::image('list.png', 'list', $this->tmpl);?>
    </span>
    <span type="radio" id="grid-view" name="view" onclick="handleRadioClick(this)">
        <?php echo FabrikHelperHTML::image('grid.png', 'list', $this->tmpl);?>
    </span>

    <?php if ($tree == true): ?>
        <span type="radio" id="tree-view" name="view" onclick="handleRadioClick(this)">
            <?php echo FabrikHelperHTML::image('hierarchy.png', 'list', $this->tmpl);?>
        </span>
    <?php endif; ?>

    <?php if ($this->canShowTutorialTemplate == true): ?>
        <span type="radio" id="tutorial-view" name="view" onclick="handleRadioClick(this)">
            <?php echo FabrikHelperHTML::image('notification.png', 'list', $this->tmpl);?>
        </span>
    <?php endif; ?>
</div>

<?php
if ($_REQUEST['workflow']['showEventsButton'] == true):
    $url = ($_REQUEST['wfl_action'] == 'list_requests') ? $_REQUEST['workflow']['list_link'] : $_REQUEST['workflow']['requests_link'];
    $active = ($_REQUEST['wfl_action'] == 'list_requests') ? 'active' : '';
    ?>
    <li class="<?php echo $active ?>">
        <!--<a class="showRequests" href="javascript://" onclick="showRequests();">-->
        <a id="showRequests" class="showRequests" href="<?php echo $url ?>">
            <?php //echo $this->buttons->requests; ?>
            <?php echo $_REQUEST['workflow']['eventsButton'] ?>
            <span class="badge bg-primary rounded-pill">
                <?php echo $_REQUEST['workflow']['requests_count'] ?>
            </span>
        </a>
    </li>
<?php endif; ?>