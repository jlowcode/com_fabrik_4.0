<?php
/**
 * Bootstrap Form Template
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
$groupTmpl = $model->editable ? 'group' : 'group_details';
$active = ($form->error != '') ? '' : ' fabrikHide';

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
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</div>
<?php
endif;

if ($this->params->get('show-title', 1)) :?>
<div class="header-title">
	<div class="page-header">
		<h1><?php echo $form->label;?></h1>
	</div>
	<div class="breadcum">
		<span class="h6">
			<a onclick="parent.location='<?php echo $linkList ?>'">
			<i class="fa fa-angle-left" aria-hidden="true"></i> IR PARA LISTA</a>
		</span>
	</div>
</div>
<?php
endif;


echo $form->intro;
?>

<form method="post" <?php echo $form->attribs?>>

<?php
echo $this->plugintop;
?>

<div class="fabrikMainError alert alert-error fabrikError<?php echo $active?>">
	<button class="close" data-dismiss="alert">×</button>
	<?php echo $form->error; ?>
</div>



<div class="row-fluid nav">
	<div class="<?php echo FabrikHelperHTML::getGridSpan(6); ?> pull-right">
		<?php
		echo $this->loadTemplate('buttons');
		?>
	</div>
	<div class="<?php echo FabrikHelperHTML::getGridSpan(6); ?>">
		<?php
		echo $this->loadTemplate('relateddata');
		?>
	</div>
</div>



<?php
foreach ($this->groups as $group) :
	$this->group = $group;
	?>

	<fieldset class="<?php echo $group->class; ?>" id="group<?php echo $group->id;?>" style="<?php echo $group->css;?>">
		<?php
		if ($group->showLegend) :?>
			<legend class="legend"><?php echo $group->title;?></legend>
		<?php
		endif;

		if (!empty($group->intro)) : ?>
			<div class="groupintro"><?php echo $group->intro ?></div>
		<?php
		endif;

		/* Load the group template - this can be :
		 *  * default_group.php - standard group non-repeating rendered as an unordered list
		 *  * default_repeatgroup.php - repeat group rendered as an unordered list
		 *  * default_repeatgroup_table.php - repeat group rendered in a table.
		 */
		$this->elements = $group->elements;
		echo $this->loadTemplate($group->tmpl);

		if (!empty($group->outro)) : ?>
			<div class="groupoutro"><?php echo $group->outro ?></div>
		<?php
		endif;
	?>
	</fieldset>
<?php
endforeach;
if ($model->editable) : ?>
<div class="fabrikHiddenFields">
	<?php echo $this->hiddenFields; ?>
</div>
<?php
endif;

echo $this->pluginbottom;
echo $this->loadTemplate('actions');
?>
</form>
<?php
echo $form->outro;
echo $this->pluginend;
echo FabrikHelperHTML::keepalive();


echo '<script>
	jQuery(".icon-eye-open.small").each(function() {
		jQuery(this).parent().append("<span style=\\"color:#bd0003 !important\\">*</span>");
		jQuery(this).remove()
	});
</script>';