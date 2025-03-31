<?php

/**
 * JLowCode List Template - Header
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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$db = Factory::getContainer()->get('DatabaseDriver');
$app = Factory::getApplication();
$menu = $app->getMenu();

$idModalLearnMore = 'modal-learn-more';

$url = "index.php?option=com_fabrik&view=list&listid={$this->get('id')}";
$menuLinked = $menu->getItems('link', $url, true);
$route = $menuLinked->route;
$link = '/' . (isset($route) ? $route : $url);

// Workflow code
if (isset($_REQUEST['workflow'])) {
	$this->showAddRequest = $_REQUEST['workflow']['showAddRequest'];
	$this->addRequestLink = $_REQUEST['workflow']['addRequestLink'];
	$this->requestLabel = $_REQUEST['workflow']['requestLabel'];
} else {
	$this->showAddRequest = null;
	$this->addRequestLink = null;
	$this->requestLabel = null;
}
// End workflow code

// Action code 
if (isset($_REQUEST['action']) && isset($_REQUEST['action']['showButton'])) {
	$this->showActionButton = $_REQUEST['action']['showButton'];
} else {
	$this->showActionButton = null; 
}
// End action code
?> 

<div class="header d-flex">
    <?php if($this->params->get('show_thumb_list', '1')) : ?>
        <?php 
            $idList = $this->list->id;
            $query = $db->getQuery(true);
            $query->select('miniatura')->from('adm_cloner_listas')->where('id_lista = ' . $idList);
            $db->setQuery($query);
            $miniatura = $db->loadResult();
        ?>
        <div class="header-thumb d-flex col-sm-2">
            <?php if($miniatura) : ?>
                <img class="img-card" src="<?php echo $miniatura ?>" alt="<?php echo $this->table->label ?>">
            <?php else : ?>
                <?php
                    preg_match_all('/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]/u', $this->table->label, $consonants);
                    $r = rand(220, 250);
                    $g = rand(220, 250);
                    $b = rand(220, 250);
                    $style = $this->modalLearnMore['callModal'] ? $this->modalLearnMore['backgroundThumb'] : "background: rgba($r, $g, $b, 1)";
                    $span = $consonants[0][0].$consonants[0][1];
                ?>
                <div class="img-card-default d-flex justify-content-center" style="<?php echo $style ?>;"><?php echo strtoupper($span) ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="header-info">
        <?php if ($this->params->get('show_page_heading')) :
            echo '<h1>' . $this->params->get('page_heading') . '</h1>';
        endif;

        if ($this->showTitle == 1) : ?>
            <div class="header-title d-flex">
                <?php if($app->input->get('listid') != $this->get('id')) : ?>
                    <h1><a href="<?php echo $link ?>"><?php echo $this->table->label; ?></a></h1>
                <?php else : ?>
                    <?php if(!$this->modalLearnMore['callModal']) : ?>
                        <h1><?php echo $this->table->label; ?></h1>
                    <?php else : ?>
                        <span style="font-weight: 700; font-size: 2.25rem; font-family: 'Nunito';"><?php echo $this->table->label; ?></span>
                    <?php endif ?>
                <?php endif ?>
            </div>
            <?php if($this->params->get('show_description_list', '1')) : ?>
                <div class="header-desc">
                    <span class="owner-name"><?php echo $this->owner_user->get('name'); ?></span>
                    <?php if($this->table->intro) : ?>
                        <div class="intro-container <?php echo $this->modalLearnMore['callModal'] ? 'd-none' : ''; ?>">
                            <div class="text-intro-content">
                                <?php echo $this->table->intro; ?>
                                <?php if(strlen($this->table->intro) > 180) : ?>
                                    <a class="learn-more" href="#<?php echo $idModalLearnMore ?>" data-bs-toggle="modal"><?php echo Text::_("JGLOBAL_LEARN_MORE") ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php if($this->modalLearnMore['callModal']) : ?>
    <div class="intro-container">
        <div class="text-intro-content">
            <?php echo $this->table->intro; ?>
        </div>
    </div>
<?php endif; ?>

<?php
if(!$this->modalLearnMore['callModal']) {
    $this->modalLearnMore['callModal'] = 1;
    $this->modalLearnMore['backgroundThumb'] = $style;

    $bodyLearnMore = $this->loadTemplate('header');
    $modalLearnMore = HTMLHelper::_(
        'bootstrap.renderModal',
        $idModalLearnMore,
        [
            'title'       	=> '',
            'keyboard'    	=> false,
            'focus'			=> false,
            'closeButton' 	=> true,
            'footer'      	=> '',
			'modalWidth'    => 60,
        ],
        $bodyLearnMore
    );
    echo $modalLearnMore;
}
?>
