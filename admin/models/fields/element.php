<?php
/**
 * Renders a fabrik element drop down
 *
 * @package     Joomla
 * @subpackage  Form
 * @copyright   Copyright (C) 2005-2020  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;

require_once JPATH_ADMINISTRATOR . '/components/com_fabrik/helpers/element.php';

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
FormHelper::loadFieldClass('list');

/**
 * Renders a fabrik element drop down
 *
 * @package     Joomla
 * @subpackage  Form
 * @since       1.6
 */
class JFormFieldElement extends ListField
{
	/**
	 * Element name
	 *
	 * @var        string
	 */
	protected $name = 'Element';

	/**
	 * Method to get the field options.
	 *
	 * @return  array    The field option objects.
	 */

	protected function getOptions()
	{
		return array();
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string    The field input markup.
	 */

	protected function getInput()
	{
		static $fabrikElements;

		if (!isset($fabrikElements))
		{
			$fabrikElements = array();
		}

		$src['Namespace'] = 'administrator/components/com_fabrik/views/namespace.js';
		$c                = (int) @$this->form->repeatCounter;
		$table            = $this->getAttribute('table');

		if ($table == '')
		{
			$table = $this->form->getValue('params.list_id');
		}

		$includeCalculations = (int) $this->getAttribute('include_calculations');
		$published           = (int) $this->getAttribute('published');
		$showInTable         = (int) $this->getAttribute('showintable');
		$highlightPk         = FabrikWorker::toBoolean($this->getAttribute('highlightpk', false), false);
		$mode                = $this->getAttribute('mode');
		$connection          = $this->getAttribute('connection');
		$connectionInRepeat  = FabrikWorker::toBoolean($this->getAttribute('connection_in_repeat', true), true);
		$excludeJoined       = (int) $this->getAttribute('excludejoined');

		if ($includeCalculations != 1)
		{
			$includeCalculations = 0;
		}

		$opts = new stdClass;

		/* Check if we are part of a subform, if so we need to insert the subform id parts */
		if (strpos($this->form->getName(), 'subform.') === 0) {
			/* yes, we are in a subform, extract the subform id part, dropping the repeat counter. */
			$connectionParts = explode("_", $connection);
			array_shift($connectionParts);
			$observe = implode('_', $connectionParts);
			$conn = str_replace([$this->fieldname, 'jform_'], [$observe, ''], $this->id);
			array_pop($connectionParts);
			array_push($connectionParts, 'table');
			$table = implode('_', $connectionParts);
			$opts->table = str_replace($this->fieldname, $table, $this->id);;
			$opts->isTemplate = (strpos($this->id, 'datasourcesX') !== false);
		} else {
			if ($this->form->repeat)
			{
				// In repeat fieldset/group
				$conn        = $connection . '-' . $this->form->repeatCounter;
				$opts->table = 'jform_' . $table . '-' . $this->form->repeatCounter;
			}
			else
			{
				$conn        = ($c === false || !$connectionInRepeat) ? $connection : $connection . '-' . $c;
				$opts->table = ($c === false || !$connectionInRepeat) ? 'jform_' . $table : 'jform_' . $table . '-' . $c;
			}
		}
		
		$opts->published            = $published;
		$opts->showintable          = $showInTable;
		$opts->excludejoined        = $excludeJoined;
		$opts->livesite             = COM_FABRIK_LIVESITE;
		$opts->conn                 = 'jform_' . $conn;
		$opts->value                = $this->value;
		$opts->include_calculations = $includeCalculations;
		$opts->highlightpk          = (int) $highlightPk;
		$opts                       = json_encode($opts);
		$script                     = array();
		$script[]                   = "var p = new elementElement('$this->id', $opts);";
		$script[]                   = "FabrikAdmin.model.fields.element['$this->id'] = p;";
		$script                     = implode("\n", $script);
		$fabrikElements[$this->id]  = true;
		$src['AdmininElelent']      = 'administrator/components/com_fabrik/models/fields/element.js';
		FabrikHelperHTML::script($src, $script);

		if ($mode === 'gui')
		{
			$return = $this->gui();
		}
		else
		{
			$return = parent::getInput();
			$return .= '<img style="margin-left:10px;display:none" id="' . $this->id
				. '_loader" src="components/com_fabrik/images/ajax-loader.gif" alt="' . Text::_('COM_FABRIK_LOADING') . '" />';
		}

		FabrikHelperHTML::framework();
		FabrikHelperHTML::iniRequireJS();

		return $return;
	}

	/**
	 * Build GUI for adding in elements
	 *
	 * @return  string  Textarea GUI
	 */

	private function gui()
	{
		$str   = array();
		$str[] = '<textarea cols="20" row="3" id="' . $this->id . '" name="' . $this->name . '">' . $this->value . '</textarea>';
		$str[] = '<button class="button btn">' . Text::_('COM_FABRIK_ADD') . '</button>';
		$str[] = '<select class="elements"></select>';

		return implode("\n", $str);
	}
}
