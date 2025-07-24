<?php
/**
 * Fabrik List Template: Admin Row
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
$thumbnail_element_id = $this->params->get('thumbnail');

$regexTitle = $title_element_id . '_order';
$regexThumb = $thumbnail_element_id . '_order';
?>
<tr id="<?php echo $this->_row->id;?>" class="<?php echo $this->_row->class;?>">
	<?php foreach ($this->headings as $heading => $label) {
		$style = empty($this->cellClass[$heading]['style']) ? '' : 'style="'.$this->cellClass[$heading]['style'].'"';
		$cStyle = preg_match("/{$regexTitle}/", $this->headingClass[$heading]['class']) ? 'style="width: 100%;"' : '';
		if($cStyle && empty($style)){
			$style = $cStyle;
		}
		?>
		<td class="<?php echo $this->cellClass[$heading]['class']?>" <?php echo $style?>>
			<?php echo isset($this->_row->data) ? $this->_row->data->$heading : '';?>
		</td>
	<?php }?>
</tr>