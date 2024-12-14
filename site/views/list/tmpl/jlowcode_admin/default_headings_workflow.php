<?php
/**
 * Bootstrap List Template: Default headings
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

require_once JPATH_PLUGINS . '/fabrik_element/field/field.php';

$btnLayout  = $this->getModel()->getLayout('fabrik-button');
$layoutData = (object) array(
	'class' => 'btn-info fabrik_filter_submit button',
	'name' => 'filter',
	'label' => FabrikHelperHTML::icon('icon-filter', Text::_('COM_FABRIK_GO'))
);
// Workflow code
$params = new Registry(json_encode(Array(
	'can_order' => true
)));

$container = JFactory::getContainer();
$subject = $container->get(\Joomla\Event\DispatcherInterface::class);
$classField = new PlgFabrik_ElementField($subject);
$classField->setParams($params, 0);

$req_status = $_REQUEST['wfl_status'];
$req_order = $_REQUEST['wfl_order'];
$l = explode('_', $req_order)[count(explode('_', $req_order)) - 1];

switch ($l) {
	case 'desc':
		$orderDir = 'desc';
		break;
	
	case '-':
		$orderDir = '-';
		break;

	default:
		$orderDir = 'asc';
		break;
}
$order = $orderDir == 'asc' ? $req_order : preg_replace('/(_desc|-)+$/', '', $req_order);
$order = $orderDir == '-' ? 'req_created_date' : $order;

$layoutHeadings = $this->getModel()->getLayout('list.fabrik-order-heading');
// Workflow code end
?>
	<tr class="fabrik___heading">
		<?php foreach ($this->headingsWorkflow as $key => $heading) :
		// Workflow code
			if ($key == 'req_approval') {
				$heading = $req_status == 'verify' ? $_REQUEST['workflow']['label_request_aproval'] : $_REQUEST['workflow']['label_request_view'];
			}

			switch ($key) {
				case 'req_request_type_name':
				case 'req_created_date':
					$width = 13;
					break;
				
				case 'req_approval':
				case 'view':
					$width = 7;
					break;

				default:
					$width = 10;
					break;
			}

			$displayData = new stdClass;
			$displayData->tmpl = $this->getModel()->getTmpl();
			$displayData->orderDir = $key == $order ? $orderDir : '';
			$displayData->class = '';
			$displayData->workflow = '-wfl';
			$displayData->orderBys = $key == $order ? $order : '';
			$displayData->elementParams = $classField->getParams();
			$displayData->key = $heading;
			$displayData->label = $heading;
		?>
			<th title="<?php echo $heading ?>" style="width: <?php echo $width; ?>%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 1px;" class="heading fabrik_ordercell <?php echo $key ?>_order">
				<span>
					<?php
						if($key != 'view') {
							echo $layoutHeadings->render($displayData); 
						} else {
							echo $heading;
						}
					?>
				</span>
			</th>
		<?php endforeach; ?>
	</tr>

<?php if (($this->filterMode === 3 || $this->filterMode === 4) && count($this->filters) <> 0) : ?>
	<tr class="fabrikFilterContainer">
		<?php foreach ($this->headings as $key => $heading) :
			$h = $this->headingClass[$key];
			$style = empty($h['style']) ? '' : 'style="' . $h['style'] . '"';
			?>
			<th class="<?php echo $h['class'] ?>" <?php echo $style ?>>
				<?php
				if (array_key_exists($key, $this->filters)) :

					$filter = $this->filters[$key];
					$required = $filter->required == 1 ? ' notempty' : '';
					?>
					<div class="listfilter<?php echo $required; ?> pull-left">
						<?php echo $filter->element; ?>
					</div>
				<?php elseif ($key == 'fabrik_actions' && $this->filter_action != 'onchange') :
					?>
					<div style="text-align:center">
						<?php echo $btnLayout->render($layoutData); ?>
					</div>
				<?php endif; ?>
			</th>
		<?php endforeach; ?>
	</tr>
<?php endif; ?>