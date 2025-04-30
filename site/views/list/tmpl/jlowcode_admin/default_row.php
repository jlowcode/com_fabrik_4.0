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

$likeNotifications = $this->params->get('show_list_with_replies', '0');
$x = 0;
if($likeNotifications) {
	$els = $this->getModel()->getElements('id');
	$parentEl = $els[$this->params->get('parent_element')]->getFullName();
	$replyEl = $els[$this->params->get('reply_element')]->getFullName();
	$data = !is_null($this->_row->data->$parentEl) && $likeNotifications ? $this->pad = "|&nbsp&nbsp" : $this->pad = '';
}

?>

<tr id="<?php echo $this->_row->id;?>" class="<?php echo $this->_row->class;?>">
	<?php foreach ($this->headings as $heading => $label) : ?>
		<?php
			$style = empty($this->cellClass[$heading]['style']) ? '' : 'style="'.$this->cellClass[$heading]['style'].'"';
			$cStyle = preg_match("/{$regexTitle}/", $this->headingClass[$heading]['class']) ? 'style="width: 100%;"' : '';
			if($cStyle && empty($style)) {
				$style = $cStyle;
			}
			$columnData = $replyEl == $heading && $likeNotifications && !is_null($this->_row->data->$parentEl) ? '' : $this->_row->data->$heading;
			$x++;
		?>
		<td class="<?php echo $this->cellClass[$heading]['class']?>" <?php echo $style?>>
			<?php echo isset($this->_row->data) ? ($x == 1 && $likeNotifications ? $this->pad.$columnData : $columnData) : '';?>
		</td>
	<?php endforeach; ?>
</tr>