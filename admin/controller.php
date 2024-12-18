<?php
/**
 * Main Fabrik administrator controller
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2020  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Filter\InputFilter;

jimport('joomla.application.component.controller');

/**
 * Fabrik master display controller.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.0
 */

class FabrikAdminController extends BaseController
{
	/**
	 * Display the view
	 *
	 * @param   bool   $cachable   If true, the view output will be cached
	 * @param   array  $urlparams  An array of safe url parameters and their variable types, for valid values see {@link InputFilter::clean()}.
	 *
	 * @return  void
	 */

	public function display($cachable = false, $urlparams = false)
	{
		$this->default_view = 'home';
		require_once JPATH_COMPONENT . '/helpers/fabrik.php';
		parent::display();
	}

	/**
	 * Method to load and return a model object.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  Optional model prefix.
	 * @param   array   $config  Configuration array for the model. Optional.
	 *
	 * @return	mixed	Model object on success; otherwise null failure.
	 */

	protected function createModel($name, $prefix = '', $config = array())
	{
		/*
		 * Use true so that we always use the Joomla db when in admin.
		 * otherwise if alt cnn set to default that is loaded and the fabrik tables are not found
		 */
		$db = FabrikWorker::getDbo(true);
		$config['dbo'] = $db;
		$r = parent::createModel($name, $prefix, $config);

        return $r;
    }

    /**
     * Function that will take exception messages and return them to the client already treated.
     *
     * @param $code
     * @param $message
     * @return mixed
     */
    public static function handlePossibleExceptions($code, $message)
    {
        switch ($code) {
            case 1064:
                $text = FText::_('COM_FABRIK_EXCEPTION_MESSAGE_1064');
                break;
            default:
                $text = $message;
                break;
        }

        return $text;
    }

    public function removeAccentsSpecialCharacters($str) {
        $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú',
            'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
        $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u',
            'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U');

        return str_replace($comAcentos, $semAcentos, $str);
    }
}
