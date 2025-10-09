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

use Joomla\CMS\Factory;

$input = Factory::getApplication()->input;

if (isset($_SESSION['modo']) && $_SESSION['modo']['lista'] == $this->table->db_table_name) {
	$modoExibicao = $_SESSION['modo']['template'];
} else if($input->get('layout_mode')) {
	$modoExibicao = $input->get('layout_mode');
} else if ($this->params->get('layout_mode')) {
	$modoExibicao = $this->params->get('layout_mode');
} else {
	$modoExibicao = 'list';
}

echo match ($modoExibicao) {
    default => $this->loadTemplate('row'),
    'grid', '1' => $this->loadTemplate('empty_row_grid'),
    'card', '4' => $this->loadTemplate('empty_row_card'),
    'masonry', '5' => $this->loadTemplate('empty_row_masonry'),
};