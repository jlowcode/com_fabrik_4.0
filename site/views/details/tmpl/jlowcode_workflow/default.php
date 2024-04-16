<?php
/**
 * Bootstrap Details Template
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2020  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.1
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$form = $this->form;
$model = $this->getModel();

if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</div>
<?php
endif;

if ($this->params->get('show-title', 1)) :?>
<div class="page-header">
	<h1><?php echo $form->label;?></h1>
</div>
<?php
endif;

echo $form->intro;

if ($this->isMambot) :
	echo '<div class="fabrikForm fabrikDetails fabrikIsMambot" id="' . $form->formid . '">';
else :
	echo '<div class="fabrikForm fabrikDetails" id="' . $form->formid . '">';
endif;

echo $this->plugintop;
?>
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
				<legend class="mt-3 legend"><?php echo $group->title;?></legend>
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
	</fieldset>

	<script>
	var fields = jQuery('.fabrikElementReadOnly');
	Object.keys(fields).forEach(function (key) {
		if (fields[key].outerText == '') {
			const alert = document.createElement('div')
			alert.innerHTML = '<div class="alert alert-warning" role="alert"><a href="index.php?option=com_fabrik&view=form/<?php echo $form->id; ?>/&formid=<?php echo $this->rowid; ?>" class="alert-link">Completar esse dado!</a></div>'
			console.log(fields[key]);
			fields[key].appendChild(alert);
		};
	});
</script>

<div class="fabrikActions form-actions">
	<?php if ($this->access == 2) : ?>

		<div class="row-fluid footer-btn">
			<div class="text-left <?php echo FabrikHelperHTML::getGridSpan(6); ?>">
				<div class="btn-group ">
					<a href="index.php?option=com_fabrik&view=form/<?php echo $form->id; ?>/&formid=<?php echo $this->rowid; ?>" title="Editar"><button class="btn btn_edu btn_edu_edit" name="edit" id="fabrikSubmit_19">Editar</button></a>
				</div>
			</div>
		<?php endif; ?>

		<div class="text-right <?php echo FabrikHelperHTML::getGridSpan(6); ?>">
			<div class="btn-group">
				<a href="index.php?option=com_fabrik&view=list&listid=<?php echo $form->id; ?>" title="Voltar"><button class="btn btn_edu btn_edu_back">Voltar</button></a>
			</div>
		</div>
		</div>
</div>

<?php
endforeach;

echo $this->pluginbottom;

echo $this->loadTemplate('actions');
echo '</div>';
echo $form->outro;
echo $this->pluginend;
