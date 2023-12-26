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

use Joomla\Utilities\ArrayHelper;

/**
 * View class for a list of packages.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       1.6
 */
class FabrikAdminViewMediaManager extends JViewLegacy {

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
     * Media manager template preview method.
     *
     * @param null $tpl
     * @return bool|mixed
     */
    public function display($tpl = null) {
        // Initialise variables.
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $this->lang = JFactory::getLanguage();
        $this->user = JFactory::getUser();
        $this->input = $app->input;
        $this->items = $this->get('Items');

        $this->arList = $this->get('ListsProjectPITT');

        $this->onClick = '';

        $ftp = !JClientHelper::hasCredentials('ftp');

        $this->session = JFactory::getSession();
        $this->config = JComponentHelper::getParams('com_fabrik');
        $this->params = JComponentHelper::getParams('com_media');
        $this->state = $this->get('state');
        $this->folderList = $this->get('folderList');
        $this->require_ftp = $ftp;

        $this->fieldInput = $this->state->get('field.id');
        $this->isMoo = $this->input->getInt('ismoo', 1);
        $this->author = $this->input->getCmd('author');
        $this->asset = $this->input->getCmd('asset');

        JHtml::_('formbehavior.chosen', 'select');
        // Load tooltip instance without HTML support because we have a HTML tag in the tip
        JHtml::_('bootstrap.tooltip', '.noHtmlTip', array('html' => false));
        // Include jQuery
        JHtml::_('behavior.core');
        JHtml::_('jquery.framework');
        JHtml::_('script', 'media/popup-imagemanager.min.js', array('version' => 'auto', 'relative' => true));
        JHtml::_('stylesheet', 'media/popup-imagemanager.css', array('version' => 'auto', 'relative' => true));

        if ($this->lang->isRtl()) {
            JHtml::_('stylesheet', 'media/popup-imagemanager_rtl.css', array('version' => 'auto', 'relative' => true));
        }

        $doc->addStyleSheet('components/com_fabrik/media/css/mediamanager.css');
        $doc->addScript('components/com_fabrik/media/js/mediamanager.js');

        $doc->addScriptOptions(
            'mediamanager', array(
                'base' => $this->params->get('image_path', 'images') . '/',
                'asset' => $this->asset,
                'author' => $this->author
            )
        );

        /**
         * Mootools compatibility
         *
         * There is an extra option passed in the URL for the iframe &ismoo=0 for the bootstrap fields.
         * By default the value will be 1 or defaults to mootools behaviour
         *
         * This should be removed when mootools won't be shipped by Joomla.
         */
        if (!empty($this->fieldInput)) { // Media Form Field
            if ($this->isMoo) {
                $this->onClick = "window.parent.jInsertFieldValue(document.getElementById('f_url').value, '" . $this->fieldInput . "');
                    window.parent.jModalClose();
                    window.parent.jQuery('.modal.in').modal('hide');";
            }
        } else { // XTD Image plugin
            $this->onClick = 'ImageManager.onok();
                window.parent.jModalClose();';
        }

        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new RuntimeException(implode("\n", $errors), 500);

            return false;
        }

        FabrikAdminHelper::setViewLayout($this);
        $this->addToolbar();
        FabrikAdminHelper::addSubmenu($this->input->getWord('view', 'lists'));

        if (FabrikWorker::j3()) {
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
    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/fabrik.php';

        $canDo = FabrikAdminHelper::getActions($this->state->get('filter.category_id'));
        JToolBarHelper::title(FText::_('COM_FABRIK_MANAGER_PACKAGES'), 'box-add');

        if ($canDo->get('core.admin')) {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_fabrik');
        }

        JToolBarHelper::divider();
        JToolBarHelper::help('JHELP_COMPONENTS_FABRIK_PACKAGES', false, FText::_('JHELP_COMPONENTS_FABRIK_PACKAGES'));
    }

}
