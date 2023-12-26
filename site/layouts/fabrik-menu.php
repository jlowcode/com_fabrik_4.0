<?php
/**
 * Menu layout
 */

defined('JPATH_BASE') or die;

$d = $displayData;
$i = 0;

?>

<ul class="nav nav-pills" id="group" role="tablist" aria-orientation="vertical">
	<?php 
		foreach ($d->tabs as $tab) :
			$style = array();
			$style[] = isset($tab->class) && $tab->class !== '' ? 'class="nav-link ' . $tab->class . '"' : '"';
			$style[] = isset($tab->css) && $tab->css !== '' ? 'style="' . $tab->css . '"': '';
			$href = isset($tab->href) ? $tab->href : $tab->id;
	?>

	<li class="<?= isset($tab->class) && $tab->class !== '' ? $tab->class : '' ?>">

	<?php 
			if (isset($tab->js) && $tab->js === false) : 
	?>

				<a 	role="tab" 
					data-toggle="pill"
					<?php echo implode(' ', $style); ?>
					href="<?php echo $href; ?>"
					id="<?php echo $tab->id; ?>">
					<?php echo FText::_($tab->label); ?>
				</a>

	<?php 
			else : 
	?>

				<a 	role="tab" 
					data-toggle="pill" 
					class="nav-link"
					href="#<?php echo $href; ?>"
					id="<?php echo $tab->id; ?>"
					aria-controls="<?php echo $tab->id; ?>"
					aria-selected="<?= isset($tab->class) && $tab->class !== '' ? 'true' : 'false' ?>">
					<?php echo FText::_($tab->label); ?>
				</a>

		<?php 
			endif;
		?>
		
	</li>

	<?php 
			$i++; 
		endforeach; 
	?>
	

</ul>
