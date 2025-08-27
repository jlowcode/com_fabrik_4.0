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

switch ($modoExibicao) {
    case 'list':
    case '0':
		echo $this->loadTemplate('row');	// Default list view
        break;

    case 'grid':
    case '1':
		echo $this->loadTemplate('empty_row_grid');
        break;

    case 'tree':
    case '2':
		echo $this->loadTemplate('row');	// Tree view is not implemented, using list as fallback
        break;

    case 'tutorial':
    case '3':
		echo $this->loadTemplate('row');	// Tutorial view is not implemented, using list as fallback
        break;
    
    case 'card':
    case '4':
		echo $this->loadTemplate('empty_row_card');
        break;
}