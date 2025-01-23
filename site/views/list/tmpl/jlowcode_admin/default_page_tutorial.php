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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$data = $this->dataTemplateTutorial;
$input = $this->app->getInput();
$menu = $app->getMenu();

$url = "index.php?option=com_fabrik&view=list&listid={$this->get('id')}";
$menuLinked = $menu->getItems('link', $url, true);
$route = $menuLinked->route;
$link = '/' . (isset($route) ? $route."?" : $url."&");

$ids = json_encode(array_map(function($valor) {
    return $valor['id'];
}, $data));
$urlPdf = $link . "format=pdf&layout=jlowcode_admin&render=page_tutorial&tmpl=jlowcode_admin&listid=".$this->get('id')."&listRowIds=$ids";

?>

<div class="tutorial ajax-filters">
    <?php foreach ($data as $key => $row) : ?>
            <section class="tutorial-section" data-id=<?php echo $row['id']; ?>>
                <div class="header-tutorial">
                    <h2><?php echo $row['parent_name'] ?></h2>

                    <?php if($key == 0 && $input->get('format') != 'pdf') : ?>
                        <a class="pdf-header" href="<?php echo $urlPdf ?>" target="_blank">
                            <?php echo FabrikHelperHTML::image('csv-import.png', 'list', $this->tmpl); ?>
                            <?php echo Text::_("COM_FABRIK_PDF"); ?>
                        </a>
                    <?php endif ?>
                </div>

                <div class="tutorial-paragraph">
                    <?php echo $row['desc'] ?>
                </div>

                <?php 
                    if(!empty($row['children'])) {
                        foreach ($row['children'] as $children) {
                            $children['hierarchy'] = 3;
                            $this->row_tutorial = $children;
                            echo $this->loadTemplate('row_tutorial');
                        }
                    }
                ?>
            </section>
    <?php endforeach ?>
</div>