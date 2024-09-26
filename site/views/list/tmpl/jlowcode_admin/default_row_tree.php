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

?>


<div class="tree-item" data-id=<?php echo @$this->_row->{$this->list->db_table_name.'___id_raw'}; ?>>
    <span class="tree-arrow" onclick="carregarFilhos(<?php echo @$this->_row->{$this->list->db_table_name.'___id_raw'}; ?>, this.parentElement)">▶</span>
    <span class="tree-text"> <?php echo @$this->_row->{$this->list->db_table_name.'___name_raw'}; ?></span>

<?php foreach ($this->headings as $heading => $label) :
	if ($heading == 'fabrik_actions') : 
	// $d = @$this->_row->data->$heading;
	$d = @$this->_row->$heading;
	?>
	
		<?php 
		$d = str_replace('class="delete"','class="btn-delete"  data-rowid="xhr" data-loadmethod="xhr" target="_self" list-row-ids="'.$this->list->id.':'.$this->_row->__pk_val.'" ',$d);
		$d = str_replace('Excluir','Reportar/Excluir',$d);
		$d = str_replace('href="#"','onclick="onReportAbuse(this)"',$d);
		//$d = str_replace('href="#"','',$d);

		echo '<span class="actions' . $c['class'] . '" ' . $cStyle . '>' . $d . '</span>'; ?>
	<?php
	endif;
endforeach;
?>
</div>
