<?php

/**
 * Bootstrap Details Template
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.1
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;

$form = $this->form;
$model = $this->getModel();
$listModel = $model->getlistModel();

$app = Factory::getApplication();
$menu = $app->getMenu();
$menuItem = $menu->getActive();
$route = $menuItem->route;

// Sometimes $route and $routeList are different
$idList = $listModel->getId();
$url = "index.php?option=com_fabrik&view=list&listid=$idList";
$menuLinked = $menu->getItems('link', $url, true);
$routeList = $menuLinked->route;
$linkList = '/' . (isset($routeList) ? $routeList : $url);

if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx') ?>">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</div>
<?php
endif;

if ($this->params->get('show-title', 1)) : ?>
	<div class="header-title">
		<div class="page-header">
			<h1><?php echo $form->label; ?></h1>
		</div>
		<div class="breadcum">
			<span class="h6">
				<a onclick="parent.location='<?php echo $linkList ?>'"><i class="fa fa-angle-left" aria-hidden="true"></i> IR PARA LISTA</a>
			</span>
		</div>
	</div>
	<?php echo $this->loadTemplate('buttons'); ?>
<?php
endif;

echo $form->intro;
if ($this->isMambot) :
	echo '<div class="fabrikForm fabrikDetails fabrikIsMambot" id="' . $form->formid . '">';
else :
	echo '<div class="fabrikForm fabrikDetails" id="' . $form->formid . '">';
endif;
echo $this->plugintop;
echo $this->loadTemplate('relateddata');
foreach ($this->groups as $group) :
	$this->group = $group;
?>

	<div class="<?php echo $group->class; ?>" id="group<?php echo $group->id; ?>" style="<?php echo $group->css; ?>">

		<?php
		if ($group->showLegend) : ?>
			<h3 class="legend">
				<span><?php echo $group->title; ?></span>
			</h3>
		<?php endif;

		if (!empty($group->intro)) : ?>
			<div class="groupintro"><?php echo $group->intro ?></div>
		<?php
		endif;

		// Load the group template - this can be :
		//  * default_group.php - standard group non-repeating rendered as an unordered list
		//  * default_repeatgroup.php - repeat group rendered as an unordered list
		//  * default_repeatgroup_table.php - repeat group rendered in a table.

		$this->elements = $group->elements;
		echo $this->loadTemplate($group->tmpl);

		if (!empty($group->outro)) : ?>
			<div class="groupoutro"><?php echo $group->outro ?></div>
		<?php
		endif;
		?>

		<?php if($this->params->get('show-complete-data', '1') == '1') : ?>
		<script>
			var fields = jQuery('.fabrikElementReadOnly');
			Object.keys(fields).forEach(function(key) {
				if (fields[key].outerText == '' && !jQuery(fields[key]).closest('.plg-display').length) {
					if((jQuery(fields[key]).find('img').length < 0 && jQuery(fields[key]).closest('.plg-fileupload').length) || jQuery(fields[key]).closest('.plg-fileupload').length <= 0) {
						const alert = document.createElement('div');
						alert.innerHTML = '<div class="alert alert-warning" role="alert"><a href="index.php?option=com_fabrik&view=form/<?php echo $form->id; ?>/&formid=<?php echo $this->rowid; ?>" class="alert-link">Completar esse dado!</a></div>';
						fields[key].appendChild(alert);
					}
				};
			});
		</script>
		<?php endif; ?>
	</div>
<?php
endforeach;

echo $this->pluginbottom;

if ($this->access == 2) : ?>
	<div class="fabrikActions form-actions">
	<div class="footer-btn">
		<div>
			<div class="btn-group ">
			<a onclick="parent.location='/<?php echo $route ?>/form/<?php echo $form->id; ?>/<?php echo $this->rowid; ?>'" title="Editar"><button  class="btn btn_jlowcode_admin btn_jlowcode_admin_edit" name="edit" id="fabrikSubmit_19">Editar</button></a>
			</div>
		</div>
	
								
		<div>
			<div class="btn-group">
			<a onclick="parent.location='/<?php echo $route ?>'" title="Voltar"><button class="btn btn_jlowcode_admin btn_jlowcode_admin_back">Voltar</button></a>
			</div>
		</div>
	</div>
</div>
	<?php
endif;

echo '</div>';
echo $form->outro;
echo $this->pluginend;
