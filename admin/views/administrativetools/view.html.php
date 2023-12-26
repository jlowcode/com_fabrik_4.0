<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * View class for a list of administrativetools.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       1.6
 */
class FabrikAdminViewAdministrativetools extends JViewLegacy
{
    /**
     * Pagination
     * @var  JPagination
     */
    protected $pagination;

    /**
     * View state
     * @var object
     */
    protected $state;

    /**
     * Display the view
     * @param   string  $tpl  Template
     * @return  void
     */

    public function display($tpl = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $input = $app->input;

        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->list = $this->get('ListsProjectPITT');

        $exist_table = $this->getModel()->checkTableExists(JText::_('COM_FABRIK_TABLE_NAME_HARVESTING'));

        if ($exist_table === NULL) {
            try {
                $this->tb_harvest = $this->get('CreateTableHarvesting');
                $app->enqueueMessage(JText::_('COM_FABRIK_EXCEPTION_MESSAGE_SUCCESS0') . JText::_('COM_FABRIK_TABLE_NAME_HARVESTING'));
            } catch (Exception $e) {
                $message = FabrikAdminController::handlePossibleExceptions($e->getCode(), $e->getMessage());
                $app->enqueueMessage($message, 'warning');
            }
        }

        $this->dados_tb_harvest = $this->get('ListTableHarvesting');

        $this->text_message = $string_array = implode("|", array(JText::_('JYES'), JText::_('JNO'), JText::_('JMESSAGE'),
            JText::_('COM_FABRIK_EXCEPTION_MESSAGE0'), JText::_('JSUCCESS'), JText::_('COM_FABRIK_EXCEPTION_MESSAGE_SUCCESS2'),
            JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR1')));

        $tab = $input->getInt('tab', 1);
        $this->activateTab($tab);

        $this->jsScriptTranslation();

        $this->linksCssJs($doc);

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new RuntimeException(implode("\n", $errors), 500);
            return false;
        }

        FabrikAdminHelper::setViewLayout($this);
        $this->addToolbar();
        FabrikAdminHelper::addSubmenu($input->getWord('view', 'lists'));

        if (FabrikWorker::j3()) {
            $this->sidebar = JHtmlSidebar::render();
        }

        FabrikHelperHTML::iniRequireJS();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     * @since    1.6
     *
     * @return  void
     */

    protected function addToolbar()
    {
        require_once JPATH_COMPONENT . '/helpers/fabrik.php';

        $canDo = FabrikAdminHelper::getActions($this->state->get('filter.category_id'));
        JToolBarHelper::title(FText::_('COM_FABRIK_MANAGER_ADMINISTRATIVETOOLS'), 'box-add');

        if ($canDo->get('core.admin')) {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_fabrik');
        }

        JToolBarHelper::divider();
        JToolBarHelper::help('JHELP_COMPONENTS_FABRIK_ADMINISTRATIVETOOLS', false, FText::_('JHELP_COMPONENTS_FABRIK_ADMINISTRATIVETOOLS'));
    }

    /**
     * Function sends message texts to javascript file
     *
     * @since version
     */
    function jsScriptTranslation()
    {
        JText::script('COM_FABRIK_MESSAGE_TITLE_ALERT');
        JText::script('COM_FABRIK_MESSAGE_LABEL_ALERT_REQUIRED_FIELDS');
        JText::script('COM_FABRIK_MESSAGE_LABEL_ALERT_REQUIRED_FIELD');
        JText::script('COM_FABRIK_TRANSFORMATION_FIELD_VALUE0');
        JText::script('COM_FABRIK_MESSAGE_ALERT_ERRO_SELECT_LIST');
        JText::script('COM_FABRIK_HARVESTING_OPTION_REPOSITORY_1');
        JText::script('COM_MEDIA_PITT_OPTION_1');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_LABEL');
        JText::script('COM_FABRIK_EXCEPTION_MESSAGE_ERROR2');
        JText::script('COM_FABRIK_TRANSFORMATION_FIELD_ELEMENT_VALUE0');

        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION0');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION1');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION2');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION3');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION4');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION5');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION6');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION7');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION8');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION9');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION10');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION11');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION12');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION13');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION14');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION15');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION16');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION17');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION18');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION19');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION20');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION21');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION22');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION23');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION24');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION25');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION26');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION27');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION28');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION29');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION30');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION31');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION32');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION33');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION34');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION35');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION36');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION37');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION38');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION39');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION40');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION41');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION42');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION43');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION44');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION45');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION46');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION47');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION48');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION49');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION50');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION51');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION52');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION53');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION54');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION55');
        JText::script('COM_FABRIK_DUBLIN_CORE_TYPE_OPTION56');
    }

    /**
     * Function that checks the tabs to always open the one you are currently using.
     *
     * @param $id
     *
     * @since version
     */
    function activateTab($id)
    {
        $this->tab1 = "";
        $this->tab2 = "";

        if ($id === 1) {
            $this->tab1 = "active";
        } elseif ($id === 2) {
            $this->tab2 = "active";
        }
    }

    /**
     * Function that groups all link links with css and js in the system.
     *
     * @param $doc
     */
    function linksCssJs($doc)
    {
        $doc->addStyleSheet('components/com_fabrik/media/css/alertify.min.css');
        $doc->addStyleSheet('components/com_fabrik/media/css/bootstrap.min.css');
        $doc->addStyleSheet('components/com_fabrik/media/css/administrativetools.css');
        JHtml::_('jquery.framework');
        $doc->addScript('components/com_fabrik/media/js/alertify.min.js');
        $doc->addScript('components/com_fabrik/media/js/administrativetools.js');
    }
}