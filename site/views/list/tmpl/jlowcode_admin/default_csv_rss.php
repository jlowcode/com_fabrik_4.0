<?php

/**
 * Bootstrap List Template - Csv
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

if ($this->showCSV) :
	if ($this->showCSVImport) :?>
		<li>
			<a href="<?php echo $this->csvImportLink ?>" class="csvImportButton"> 
				<?php echo $this->buttons->csvimport ?>
				<span><?php echo Text::_('COM_FABRIK_IMPORT_FROM_CSV') ?></span>
			</a>
		</li>
	<?php endif;
	if ($this->showCSV) : ?>
		<li>
			<a href="#" class="csvExportButton">
				<?php echo $this->buttons->csvexport ?>
				<span><?php echo Text::_('COM_FABRIK_EXPORT_TO_CSV') ?></span>
			</a>
		</li>
	<?php endif;
	if ($this->showRSS) : ?>
		<li>
			<a href="<?php echo $this->rssLink; ?>" class="feedButton">
				<?php echo FabrikHelperHTML::image('feed.png', 'list', $this->tmpl); ?>
				<?php echo Text::_('COM_FABRIK_SUBSCRIBE_RSS'); ?>
			</a>
		</li>
	<?php endif;
	if ($this->showPDF) : ?>
		<li><a href="<?php echo $this->pdfLink; ?>" class="pdfButton">
				<?php echo FabrikHelperHTML::icon('icon-file', Text::_('COM_FABRIK_PDF')); ?>
			</a></li>
	<?php endif;
	if ($this->emptyLink) : ?>
		<li>
			<a href="<?php echo $this->emptyLink ?>" class="doempty">
				<?php echo $this->buttons->empty; ?>
				<?php echo Text::_('COM_FABRIK_EMPTY') ?>
			</a>
		</li>
	<?php endif;
?>
<?php endif ?>