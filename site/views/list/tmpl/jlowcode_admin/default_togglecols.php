<?php
/**
 * Bootstrap List Template - Toggle columns widget
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

?>
<li class="dropdown togglecols">
	<a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
		<?php echo FabrikHelperHTML::image("view.png", 'list'); ?>
		<span><?php echo Text::_('COM_FABRIK_TOGGLE')?></span>
		<b class="caret"></b>
	</a>
	<ul class="dropdown-menu">
	<?php
	$groups = array();

	foreach ($this->toggleCols as $group) :
		?>
		<li>
			<a data-toggle-group="<?php echo $group['name']?>" data-toggle-state="open">
				<?php echo FabrikHelperHTML::image("view.png", 'list'); ?>
				<strong><?php echo Text::_($group['name']);?></strong>
			</a>
		</li>
		<?php
		foreach ($group['elements'] as $element => $label) :
		?>
		<li>
			<a data-toggle-col="<?php echo $element?>" data-toggle-parent-group="<?php echo $group['name']?>" data-toggle-state="open">
				<?php echo FabrikHelperHTML::image("view.png", 'list'); ?>
				<span><?php echo Text::_($label)?></span>
			</a>
		</li>
		<?php
		endforeach;

	endforeach;

	?>
	</ul>
</li>
