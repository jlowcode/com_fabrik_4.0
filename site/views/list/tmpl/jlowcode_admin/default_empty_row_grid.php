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

$columns = 3;
$items[] = $this->loadTemplate('row_grid');
$class = 'fabrik_row well gallery-div';

$html = FabrikHelperHTML::bootstrapGridCards($items, $columns, $class, true, $ids, '', false);
echo $html;