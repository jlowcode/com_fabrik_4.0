<?php
/**
 * Base Fabrik view class
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2020  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Session\Session;
use Joomla\CMS\User\User;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.view');

/**
 * Class FabrikView
 */
class FabrikView extends HtmlView
{
	/**
	 * @var JApplicationCMS
	 */
	protected $app;

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $package;

	/**
	 * @var Session
	 */
	protected $session;

	/**
	 * @var Document
	 */
	protected $doc;

	/**
	 * @var JDatabaseDriver
	 */
	protected $db;

	/**
	 * @var Registry
	 */
	protected $config;

	/**
	 * Constructor
	 *
	 * @param   array $config A named configuration array for object construction.
	 *
	 */
	public function __construct($config = array())
	{
		$this->app     = ArrayHelper::getValue($config, 'app', Factory::getApplication());
		$this->user    = ArrayHelper::getValue($config, 'user', Factory::getUser());
		$this->package = $this->app->getUserState('com_fabrik.package', 'fabrik');
		$this->session = ArrayHelper::getValue($config, 'session', Factory::getSession());
		$this->doc     = ArrayHelper::getValue($config, 'doc', Factory::getDocument());
		$this->db      = ArrayHelper::getValue($config, 'db', Factory::getContainer()->get('DatabaseDriver'));
		$this->config  = ArrayHelper::getValue($config, 'config', Factory::getApplication()->getConfig());
		parent::__construct($config);
	}
}