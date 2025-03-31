<?php
/**
 * Layout: List Pagination Active Item
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2020  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.4.2
 */

$d = $displayData;
$item = $d->item;

switch ($item->key) {
    case 'previous':
        $rel = 'rel="prev" ';
        $item->text = FabrikHelperHTML::image('arrow-left-one.png');
        break;
    case 'next':
        $rel = 'rel="next" ';
        $item->text = FabrikHelperHTML::image('arrow-right-one.png');
        break;
    case 'end':
        $item->text = FabrikHelperHTML::image('arrow-right.png');
        break;
    case 'start':
        $item->text = FabrikHelperHTML::image('arrow-left.png');
        break;
    default:
        $rel = '';
}

?>
<a <?php echo $rel; ?> title="<?php echo $item->link; ?>" href="<?php echo $item->link; ?>" class="pagenav"><?php echo $item->text; ?></a>


