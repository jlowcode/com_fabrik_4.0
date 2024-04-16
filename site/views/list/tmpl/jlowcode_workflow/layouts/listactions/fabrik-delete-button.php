<?php
/**
 * Layout: list row buttons - rendered as a Bootstrap dropdown
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
$d = $displayData;
use Joomla\CMS\Language\Text;

?>
<a href="#" class="<?php echo $d->btnClass;?>delete" data-listRef="list_<?php echo $d->renderContext;?>"
	title="<?php echo Text::_('COM_FABRIK_DELETE'); ?>">
	<?php echo FabrikHelperHTML::image('warning', 'list', $d->tpl, array('alt' => $d->label))?> <?php echo $d->text;?></a>