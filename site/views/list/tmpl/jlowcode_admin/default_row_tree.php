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
$elementModel  = $pluginManager->getPlugIn('workflow', 'form');
$elementModel->easyadmin = true;
$hasPermission = $elementModel->hasPermission(['easyadmin_modal___listid' => $this->getModel()->getId()]);
$titleDelAction = $hasPermission ? Text::_("PLG_FORM_WORKFLOW_DELETE_RECORD_LIST") : Text::_("PLG_FORM_WORKFLOW_REPORT_RECORD_LIST");
$imgDelAction = $hasPermission ? 'trash.png' : 'danger.png';

?>

<div class="tree-item" data-id=<?php echo @$this->_row->{$this->list->db_table_name.'___id_raw'}; ?>>
    <span class="tree-arrow" onclick="carregarFilhos(<?php echo @$this->_row->{$this->list->db_table_name.'___id_raw'}; ?>, this.parentElement)">
		<?php echo FabrikHelperHTML::image('arrow-round-right.png', 'list', $this->tmpl);?>
	</span>

	<span class="tree-text">
		<?php echo @$this->_row->{$this->list->db_table_name.'___name_raw'}; ?>
	</span>

	<?php
		foreach ($this->headings as $heading => $label) :
			if ($heading == 'fabrik_actions') :
				$d = @$this->_row->$heading;
				$d = str_replace('class="delete"','class="btn-delete"  data-rowid="xhr" data-loadmethod="xhr" target="_self" list-row-ids="' .$this->list->id . ':' . $this->_row->__pk_val . '" ', $d);
				$d = str_replace('Excluir', $titleDelAction, $d);
				$d = str_replace('close.png', $imgDelAction, $d);
				$d = str_replace('href="#"','onclick="onReportAbuse(this)"', $d);

				echo '<span class="actions' . $c['class'] . '" ' . $cStyle . '>' . $d . '</span>'; ?>
			<?php
			endif;
		endforeach;
	?>
</div>

