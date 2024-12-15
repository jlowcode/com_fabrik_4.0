<?php
/**
 * Jlowcode Admin List Template - Default row to subrender tutorial
 *
 * 
 * @package     Joomla
 * @subpackage  Fabrik.view.list.tmpl
 * @copyright   Copyright (C) 2024 Jlowcode Org - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$limitH = 5;
$row = $this->row_tutorial;
$h = $row['hierarchy'];

if($row['hierarchy'] != 5) {
	echo "<h{$h}>" . $row['parent_name'] . "</h{$h}>";
} else {
	$h = $row['hierarchy']-1;
	echo "<h$h style='font-style: italic': '>" . $row['parent_name'] . "</h{$h}>";
}

echo $row['desc'];

if(!empty($row['children'])) {
	foreach ($row['children'] as $children) {
		$row['hierarchy'] >= $limitH ? $children['hierarchy'] = $limitH : $children['hierarchy'] = ++$row['hierarchy'];
		$this->row_tutorial = $children;
		echo $this->loadTemplate('row_tutorial');
	}
}
?>