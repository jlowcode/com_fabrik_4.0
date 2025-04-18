<?php
/**
 * Default Form: Repeat group rendered as a table, <tr> template
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2020  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$group = $this->group;
?>
<tr class="fabrikSubGroupElements fabrikSubGroup">
<?php
if ($group->canOrder) :
?>
    <td><?php echo FabrikHelperHTML::icon('icon-menu-2'); ?></td>
<?php
endif;

foreach ($this->elements as $element) :
	$style = $element->hidden ? 'style="display:none"' : '';
	?>
	<td class="<?php echo $element->containerClass; ?>" <?php echo $style?>>
	<?php
	if ($this->tipLocation == 'above') :
	?>
		<div><?php echo $element->tipAbove; ?></div>
	<?php
	endif;
	echo $element->errorTag; ?>
	<div class="fabrikElement <?php echo $element->bsClass;?>">
		<?php echo $element->element; ?>
	</div>

	<?php if ($this->tipLocation == 'side') :
		echo $element->tipSide;
	endif;
	if ($this->tipLocation == 'below') : ?>
		<div>
			<?php echo $element->tipBelow; ?>
		</div>
	<?php endif;
	?>
	</td>
	<?php
	endforeach;
 	if ($group->editable) : ?>
		<td class="">
			<div class="fabrikGroupRepeater float-end btn-group-sm">
			<?php
			if ($group->canAddRepeat) :
				echo $this->addRepeatGroupButtonRow;
			endif;
			if ($group->canDeleteRepeat) :
				echo $this->removeRepeatGroupButtonRow;
			endif;
			?>
			</div>
		</td>
	<?php endif; ?>
</tr>
