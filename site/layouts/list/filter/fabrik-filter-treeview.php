<?php
defined('JPATH_BASE') or die;

$d    = $displayData;
$inputDataAttribs = array('data-filter-name="' . $d->elementName . '"');
FabrikHelperHTML::iniRequireJS();

$urlbase = JUri::base();
?>
<!-- Loading treeview javascript  -->
<script src="<?php echo $urlbase; ?>/components/com_fabrik/js/multiSelectTreeViewFilter.js"></script>
<div class="fabrikListFilterCheckbox">

	<div class="tree-view-filter">
		<?php echo $d->data; ?>
		<?php echo($d->filterBuild == 'count') ? $d->countArray : "" ?>
		<div class="selected-checkbox">
		</div>
		<div id="tree_simples_<?php echo $d->elementName?>">
		</div>
	</div>

</div>