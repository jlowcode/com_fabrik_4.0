<?php
/**
 * Fabrik List Template: Div Row
 * Note the div cell container is now generated in the default template
 * in FabrikHelperHTML::bootstrapGrid();
 * 
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

$pluginManager = FabrikWorker::getPluginManager();
$params = $this->getModel()->getFormModel()->getParams();
$workflowPlugin = array_search('workflow', $params->get('plugins'));
$hasWorkflow = $workflowPlugin != null && $params->get('plugin_state')[$workflowPlugin] == '1';
$elementModel  = $pluginManager->getPlugIn('workflow', 'form');

$elementModel->easyadmin = true;
$hasPermission = $elementModel->hasPermission(['easyadmin_modal___listid' => $this->getModel()->getId()]);
$titleDelAction = $hasPermission ? Text::_("PLG_FORM_WORKFLOW_DELETE_RECORD_LIST") : Text::_("PLG_FORM_WORKFLOW_REPORT_RECORD_LIST");
$imgDelAction = $hasPermission ? 'trash.png' : 'danger.png';

$listModel = $this->_models["list"];
$elsList = $listModel->getElements('id');
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

<div class="tree-item" data-id=<?php echo @$this->_row->{$this->list->db_table_name.'___id_raw'}; ?>>
    <span class="tree-arrow" onclick="carregarFilhos(<?php echo @$this->_row->{$this->list->db_table_name.'___id_raw'}; ?>, this.parentElement)">
		<?php echo FabrikHelperHTML::image('arrow-round-right.png', 'list', $this->tmpl);?>
	</span>

	<span class="tree-text">
		<?php echo @$this->_row->{$this->list->db_table_name.'___' . $this->elFieldTree . '_raw'}; ?>
	</span>

	<?php
		$d = @$this->_row->fabrik_actions;

		if ($hasWorkflow) {
			$d = str_replace('class="delete"','class="btn-delete"  data-rowid="xhr" data-loadmethod="xhr" target="_self" list-row-ids="' .$this->list->id . ':' . $this->_row->__pk_val . '" ', $d);
			$d = str_replace('Excluir', $titleDelAction, $d);
			$d = str_replace('close.png', $imgDelAction, $d);
			$d = str_replace('href="#"','onclick="onReportAbuse(this)"', $d);

			if(!$hasPermission) {
				$d = str_replace(' '.Text::_("COM_FABRIK_EDIT"), ' '.Text::_("PLG_FORM_WORKFLOW_REPORT_EDIT_RECORD_LIST"), $d);
			}
		}

		echo '<span class="actions' . $c['class'] . '" ' . $cStyle . '>' . $d . @$this->_row->fabrik_select . '</span>';
	?>
</div>