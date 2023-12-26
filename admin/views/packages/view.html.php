<?php
/**
 * View class for a list of packages.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * View class for a list of packages.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       1.6
 */
class FabrikAdminViewPackages extends JViewLegacy
{
	/**
	 * Package items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * Pagination
	 *
	 * @var  JPagination
	 */
	protected $pagination;

	/**
	 * View state
	 *
	 * @var object
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template
	 *
	 * @return  void
	 */

	public function display($tpl = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
        $doc = JFactory::getDocument();
		$input = $app->input;
		$this->items = $this->get('Items');

		$exist_table = $this->get('ExistTablePkgs');

		if($exist_table->pkgs === '0'){
            $this->get('CreateTablePackages');
        }

		$this->text_message = $string_array = implode("|", array(JText::_('JYES'),JText::_('JNO'),JText::_('JMESSAGE'),
            JText::_('COM_FABRIK_PACKAGES_CREATE_MESSAGE_QUESTION_FILE'), JText::_('COM_FABRIK_PACKAGES_CREATE_MESSAGE_QUESTION_PACKAGE'),
            JText::_('JSUCCESS'), JText::_('COM_FABRIK_PACKAGES_CREATE_MESSAGE_SUCCESS'), JText::_('COM_FABRIK_PACKAGES_LIST_TABLE_MESSAGE_SUCCESS')));

        $folder_path = pathinfo($_SERVER['SCRIPT_FILENAME']);

        $this->folder = $folder_path['dirname']. '/components/com_fabrik/packagesupload';

        $this->files = scandir($this->folder);

        $this->list_packages = $this->get('ListPackages');

        $doc->addStyleSheet('components/com_fabrik/media/css/alertify.min.css');
        $doc->addStyleSheet('components/com_fabrik/media/css/bootstrap.min.css');
        $doc->addStyleSheet('components/com_fabrik/media/css/packages.css');
        $doc->addScript('../media/jui/js/jquery.min.js');
        $doc->addScript('components/com_fabrik/media/js/alertify.min.js');
        $doc->addScript('components/com_fabrik/media/js/packages.js');

        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new RuntimeException(implode("\n", $errors), 500);

			return false;
		}

		FabrikAdminHelper::setViewLayout($this);
		$this->addToolbar();
		FabrikAdminHelper::addSubmenu($input->getWord('view', 'lists'));

		if (FabrikWorker::j3())
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		FabrikHelperHTML::iniRequireJS();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 *
	 * @return  void
	 */

	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/fabrik.php';

		$canDo = FabrikAdminHelper::getActions($this->state->get('filter.category_id'));
		JToolBarHelper::title(FText::_('COM_FABRIK_MANAGER_PACKAGES'), 'box-add');

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_fabrik');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_FABRIK_PACKAGES', false, FText::_('JHELP_COMPONENTS_FABRIK_PACKAGES'));
	}
}
