<?php
/**
 * Fabrik Component HTML Helper
 *
 * @package     Joomla
 * @subpackage  Fabrik.helpers
 * @copyright   Copyright (C) 2005-2020  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Fabrik\Helpers;

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Layout\LayoutInterface;
use Joomla\CMS\Version;
use Joomla\CMS\Environment\Browser;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\HTML\Helpers\Bootstrap;
use Joomla\CMS\Filesystem\File;
use \stdClass;

jimport('joomla.filesystem.file');

if (!defined('COM_FABRIK_FRONTEND'))
{
	throw new RuntimeException(Text::_('COM_FABRIK_SYSTEM_PLUGIN_NOT_ACTIVE'), 400);
}

/**
 * Fabrik Component HTML Helper
 *
 * @static
 * @package     Joomla
 * @subpackage  Fabrik.helpers
 * @since       1.5
 */
class Html
{
	/**
	 * Add rangeslider JS code to head
	 *
	 * @param   int		$max     
	 * @param   int    	$min   
	 * @param   string  $elementName
	 * 
	 * @return  void
	 */
	public static function rangeSlider($max, $min, $elementName)
	{
		$jsFile = '';
		$className = '';
		
		Html::stylesheet('media/com_fabrik/css/rangeslider.css'); // CSS changed from jquery-ui

		$jsFile = 'rangeslider';
		$className = 'RangeSlider';
		
		$needed   = array();
		$needed[] = 'fab/' . $jsFile;
		$needed[] = 'lib/Event.mock';
		$needed   = implode("', '", $needed);
		
		self::addScriptDeclaration(
			"require(['$needed'], function ($className) {
				new $className($max, $min, '$elementName');
			});"
		);
	}

	/**
	 * Add tagscloud JS code to head
	 *  
	 * @param	string	$elementName
	 * 
	 * @return	void
	 */
	public static function tagCloud($elementName)
	{
		$jsFile = 'tagcloud';
		$className = 'TagCloud';
		
		$needed   = array();
		$needed[] = 'fab/' . $jsFile;
		$needed[] = 'lib/Event.mock';
		$needed   = implode("', '", $needed);
		
		self::addScriptDeclaration(
			"require(['$needed'], function ($className) {
				new $className('$elementName');
			});"
		);
	}

	/**
	 * Add treeview JS code to head
	 *
	 * @param   string $htmlId      Of element to turn into autocomplete
	 * @param   int    $elementId   Element id
	 * @param   int    $formId      Form id
	 * @param   string $plugin      Plugin name
	 * @param   array  $opts        * onSelection - function to run when option selected
	 *                              * max - max number of items to show in selection list
	 * @param	string $type		The element type: both (auto-complete and tree view), only treeview or only auto-complete
	 *
	 * @return  void
	 */
	public static function treeView($htmlId, $htmlIdDivSelected, $htmlIdDivTree, $elementId, $formId, $plugin = 'field', $opts = array(), $type=false)
	{
		$str  = '';
		$jsFile = '';
		$className = '';

		Html::stylesheet('plugins/fabrik_element/databasejoin/tags.css');
		Html::stylesheet('plugins/fabrik_element/databasejoin/jqtree.css');

		if ($type == 'both-treeview-autocomplete') {
			Html::stylesheet('plugins/fabrik_element/databasejoin/autocompletetreeview.css');

			$json = self::treeviewAutocompleteOptions($htmlId, $elementId, $formId, $plugin, $opts);
			$str  = json_encode($json);
			$jsFile = 'treeview-autocomplete';
			$className = 'TreeViewAutoComplete';
		} else {
			$json = self::treeviewOptions($htmlId, $elementId, $formId, $plugin, $opts);
			$str  = json_encode($json);
			$jsFile = 'treeview';
			$className = 'TreeView';
		}

		$needed   = array();
		$needed[] = 'fab/' . $jsFile;
		$needed[] = 'lib/Event.mock';
		$needed   = implode("', '", $needed);
		self::addScriptDeclaration(
			"require(['$needed'], function ($className) {
	new $className('$htmlId', '$htmlIdDivSelected', '$htmlIdDivTree', $str);
});"
		);
	}

	/**
	 * Gets auto complete js options (needed separate from autoComplete as db js class needs these values for repeat
	 * group duplication)
	 *
	 * @param   string $htmlId      Element to turn into autocomplete
	 * @param   int    $elementId   Element id
	 * @param   int    $formId      Form id
	 * @param   string $plugin      Plugin type
	 * @param   array  $opts        * onSelection - function to run when option selected
	 *                              * max - max number of items to show in selection list
	 *
	 * @return  array    Autocomplete options (needed for elements so when duplicated we can create a new
	 *                   FabAutocomplete object
	 */
	public static function treeviewOptions($htmlId, $elementId, $formId, $plugin = 'field', $opts = array())
	{
		$json = new stdClass;

		$app       = Factory::getApplication();
		$package   = $app->getUserState('com_fabrik.package', 'fabrik');
		$json->url = COM_FABRIK_LIVESITE . 'index.php?option=com_' . $package . '&format=raw';
		$json->url .= $app->isClient('administrator') ? '&task=plugin.pluginAjax' : '&view=plugin&task=pluginAjax';
		$json->url .= '&g=element&element_id=' . $elementId
			. '&formid=' . $formId . '&plugin=' . $plugin . '&package=' . $package;
		$c = ArrayHelper::getValue($opts, 'onSelection');

		if ($c != '') {
			$json->onSelections = $c;
		}

		foreach ($opts as $k => $v) {
			$json->$k = $v;
		}

		$json->formRef   = ArrayHelper::getValue($opts, 'formRef', 'form_' . $formId);

		return $json;
	}

	/**
	 * Gets treeview autocomplete js options (needed separate from autoComplete as db js class needs these values for repeat
	 * group duplication)
	 *
	 * @param   string $htmlId      Element to turn into autocomplete
	 * @param   int    $elementId   Element id
	 * @param   int    $formId      Form id
	 * @param   string $plugin      Plugin type
	 * @param   array  $opts        * onSelection - function to run when option selected
	 *                              * max - max number of items to show in selection list
	 *
	 * @return  array    Autocomplete options (needed for elements so when duplicated we can create a new
	 *                   FabAutocomplete object
	 */
	public static function treeviewAutocompleteOptions($htmlId, $elementId, $formId, $plugin = 'field', $opts = array())
	{
		$json = new stdClass;

		if (!array_key_exists('minTriggerChars', $opts)) {
			$usersConfig           = ComponentHelper::getParams('com_fabrik');
			$json->minTriggerChars = (int) $usersConfig->get('autocomplete_min_trigger_chars', '3');
		}

		if (!array_key_exists('max', $opts)) {
			$usersConfig = ComponentHelper::getParams('com_fabrik');
			$json->max   = (int) $usersConfig->get('autocomplete_max_rows', '10');
		}

		if (!array_key_exists('autoLoadSingleResult', $opts)) {
			$usersConfig           = ComponentHelper::getParams('com_fabrik');
			$json->autoLoadSingleResult = (int) $usersConfig->get('autocomplete_autoload_single', '0');
		}

		$app       = Factory::getApplication();
		$package   = $app->getUserState('com_fabrik.package', 'fabrik');
		$json->url = COM_FABRIK_LIVESITE . 'index.php?option=com_' . $package . '&format=raw';
		$json->url .= $app->isClient('administrator') ? '&task=plugin.pluginAjax' : '&view=plugin&task=pluginAjax';
		$json->url .= '&g=element&element_id=' . $elementId
			. '&formid=' . $formId . '&plugin=' . $plugin . '&package=' . $package;
		$c = ArrayHelper::getValue($opts, 'onSelection');

		if ($c != '') {
			$json->onSelections = $c;
		}

		foreach ($opts as $k => $v) {
			$json->$k = $v;
		}

		$json->formRef   = ArrayHelper::getValue($opts, 'formRef', 'form_' . $formId);
		$json->container = ArrayHelper::getValue($opts, 'container', 'fabrikElementContainer');
		$json->menuclass = ArrayHelper::getValue($opts, 'menuclass', 'auto-complete-container');

		return $json;
	}
}
