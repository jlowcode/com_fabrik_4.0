<?php
/**
 * Renders a list of fabrik lists or db tables
 *
 * @package     Joomla
 * @subpackage  Form
 * @copyright   Copyright (C) 2005-2020  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Form\FormHelper;
use Fabrik\Helpers\Html;
use Joomla\CMS\Language\Text;
use Fabrik\Helpers\Worker;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\ListField;

// Required for menus
//require_once JPATH_SITE . '/components/com_fabrik/helpers/html.php';
//require_once JPATH_SITE . '/components/com_fabrik/helpers/string.php';
//require_once JPATH_SITE . '/components/com_fabrik/helpers/parent.php';
require_once JPATH_ADMINISTRATOR . '/components/com_fabrik/helpers/element.php';

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
FormHelper::loadFieldClass('list');

/**
 * Renders a list of fabrik lists or db tables
 *
 * @package     Fabrik
 * @subpackage  Form
 * @since       3.0
 */
class JFormFieldFabrikTables extends ListField
{
	/**
	 * Element name
	 *
	 * @var        string
	 */
	protected $name = 'Fabriktables';

	/**
	 * Fabrik lists
	 *
	 * @var  array
	 */
	protected static $fabrikTables;

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */

	protected function getOptions()
	{
		if (!isset($fabrikTables))
		{
			$fabrikTables = array();
		}

		$connectionDd = $this->element['observe'];
		$db           = Worker::getDbo(true);

		if ($connectionDd == '')
		{
			// We are not monitoring a connection drop down so load in all tables
			$query = $db->getQuery(true);
			$query->select('id AS value, label AS text')->from('#__fabrik_lists')->where('published <> -2')->order('label ASC');
			$db->setQuery($query);
			$rows = $db->loadObjectList();
		}
		else
		{
			$rows = array(HTMLHelper::_('select.option', '', Text::_('COM_FABRIK_SELECT_A_CONNECTION_FIRST'), 'value', 'text'));
		}

		return $rows;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string    The field input markup.
	 */

	protected function getInput()
	{
		$c                  = isset($this->form->repeatCounter) ? (int) $this->form->repeatCounter : 0;
		$connectionDd       = $this->getAttribute('observe');
		$connectionInRepeat = Worker::toBoolean($this->getAttribute('connection_in_repeat', 'true'), true);
		$script             = array();

		if (!isset($fabrikTables))
		{
			$fabrikTables = array();
		}

		if ($connectionDd != '' && !array_key_exists($this->id, $fabrikTables))
		{
			$repeatCounter = empty($this->form->repeatCounter) ? 0 : $this->form->repeatCounter;

			$opts           = new stdClass;

			/* Check if we are part of a subform, if so we need to insert the subform id parts */
			if (strpos($this->form->getName(), 'subform.') === 0) {
				/* yes, we are in a subform, extract the subform id part, dropping the repeat counter. */
				$connectionDd = str_replace([$this->fieldname, 'jform_'], [$connectionDd, ''], $this->id);
				$opts->isTemplate = (strpos($this->id, 'datasourcesX') !== false);
			} else {
				if ($this->form->repeat)
				{
					// In repeat fieldset/group
					$connectionDd = $connectionDd . '-' . $repeatCounter;
				}
				else
				{
					$connectionDd = ($c === false || !$connectionInRepeat) ? $connectionDd : $connectionDd . '-' . $c;
				}
			}

			$opts->livesite = COM_FABRIK_LIVESITE;
			$opts->conn     = 'jform_' . $connectionDd;

			$opts->value         = $this->value;
			$opts->connInRepeat  = $connectionInRepeat;
			$opts->inRepeatGroup = $this->form->repeat;
			$opts->repeatCounter = $repeatCounter;
			$opts->container     = 'test';
			$opts                = json_encode($opts);
			$script[]            = "var p = new fabriktablesElement('$this->id', $opts);";
			$script[]            = "FabrikAdmin.model.fields.fabriktable['$this->id'] = p;";

			$fabrikTables[$this->id] = true;
			$src['Fabrik']           = 'media/com_fabrik/js/fabrik.js';
			$src['Namespace']        = 'administrator/components/com_fabrik/views/namespace.js';
			$src['FabrikTables']     = 'administrator/components/com_fabrik/models/fields/fabriktables.js';
			Html::script($src, $script);
		}

		$html = parent::getInput();
		$html .= '<img style="margin-left:10px;display:none" id="' . $this->id . '_loader" src="components/com_fabrik/images/ajax-loader.gif" alt="'
			. Text::_('LOADING') . '" />';
		Html::framework();
		Html::iniRequireJS();

		return $html;
	}
}
