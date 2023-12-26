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

$rowClass = isset($this->_row->rowClass) ? $this->_row->rowClass : '';
// Catching the elements IDs in params
$title_element_id = $this->params->get('titulo');
$regexTitle = $title_element_id. '_order';
?>
<div class="<?php echo $rowClass; ?>">
<?php foreach ($this->headings as $heading => $label) :
	$d = @$this->_row->data->$heading;

	//skip empty elements but don't skip the checkbox (delete, list plugins)
	if (isset($this->showEmpty) && $this->showEmpty === false && trim(strip_tags($d)) == '' && $heading != 'fabrik_select') :
		continue;
	endif;
	$h = $this->headingClass[$heading];
	$c = $this->cellClass[$heading];
	$hStyle = empty($h['style']) ? '' : 'style="' . $h['style'] . '"';
	$cStyle = empty($c['style']) ? '' : 'style="'. $c['style'].'"';
	$cStyle = preg_match("/{$regexTitle}/", $h['class']) ? 'style="font-weight: bold;"' : ''
	?>
    <?php if (isset($this->showLabels) && $this->showLabels) :
			echo '<span style="margin: 5px 0px" class="muted ' . $h['class'] . '" ' . $hStyle . '>' . $label . ': </span>';
		endif; ?>

	<?php echo '<span style="margin: 5px 0px" class="' . $c['class'] . '" ' . $cStyle . '>' . $d . '</span>'; ?>
	<?php
endforeach;
?>
</div>
