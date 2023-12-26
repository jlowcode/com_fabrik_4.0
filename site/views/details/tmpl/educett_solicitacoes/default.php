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

$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('etapa,status_fun')->from('edu_solicitacoes')->where('id = ' . $db->quote($this->data['edu_solicitacoes___id_raw']));
$db->setQuery($query, 1, 0);
$result = $db->loadAssoc();

if ($result['etapa'] == 'Concluido' && $result['status_fun'] == 'Concluido') {
	$link = '/protocolo/concluidas';
} else {
	$link = '/protocolo';
}

defined('_JEXEC') or die('Restricted access');

$form = $this->form;
$model = $this->getModel();

if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx') ?>">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</div>
<?php
endif;

if ($this->params->get('show-title', 1)) : ?>
	<div class="page-header">
		<h1><?php echo $form->label; ?></h1>
	</div>

	<div class="breadcum">
		<span class="h6">
			<a href="<?php echo $link; ?>"><i class="fa fa-angle-left" aria-hidden="true"></i> VOLTAR</a>
		</span>
	</div>
	<?php echo $this->loadTemplate('buttons'); ?>
	<br>

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
	</div>
<?php
endforeach;

echo $this->pluginbottom;
?>
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
				<a href="<?php echo $link; ?>" title="Voltar"><button class="btn btn_edu btn_edu_back">Voltar</button></a>
			</div>
		</div>
		</div>
</div>
<?php
echo '</div>';
echo $form->outro;