<?php
/**
 * Admin List Tmpl
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2020  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

?>
<div class="tab-pane active" id="jlowcode">

	<ul class="nav nav-tabs" id="Fab_List_NavJlowcode" role="tablist">
	  <li class="nav-item" role="">
		<button class="nav-link active" id="" data-bs-toggle="tab" data-bs-target="#jlowcode-layout" type="button" role="tab" aria-controls="" aria-selected="true">
			<?php echo Text::_('COM_FABRIK_LAYOUT'); ?>
		</button>
	  </li>
	</ul>

	<div class="tab-content">

		<div class="tab-pane" id="jlowcode-filters">
			<legend></legend>
		    <fieldset>
				<?php
				foreach ($this->form->getFieldset('main_filter') as $this->field) :
					echo $this->loadTemplate('control_group');
				endforeach;
				foreach ($this->form->getFieldset('filters') as $this->field) :
					echo $this->loadTemplate('control_group');
				endforeach;
				?>
			</fieldset>
		</div>

		<div class="tab-pane active" id="jlowcode-layout">
			<legend></legend>
			<fieldset>
				<?php foreach ($this->form->getFieldset('main') as $this->field) :
					echo $this->loadTemplate('control_group');
				endforeach;
				?>
				<?php foreach ($this->form->getFieldset('jlowcode2') as $this->field) :
					echo $this->loadTemplate('control_group');
				endforeach;
				?>
			</fieldset>
		</div>
	</div>
</div>