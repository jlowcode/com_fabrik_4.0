<?php
/**
 * Fabrik Admin Packages Model
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       1.6
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

require_once 'fabmodellist.php';

/**
 * Fabrik Admin Packages Model
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.0
 */

class FabrikAdminModelAdministrativetools extends FabModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see		JController
	 * @since	1.6
	 */

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array('p.id', 'p.label', 'p.published');
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table. Always load fabrik packages - so no {package} placeholder
		$query->select($this->getState('list.select', 'p.*'));
		$query->from('#__fabrik_packages AS p');

		// Join over the users for the checked out user.
		$query->select(' u.name AS editor');
		$query->join('LEFT', '#__users AS u ON p.checked_out = u.id');

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('p.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(p.published IN (0, 1))');
		}

		$query->where('(p.external_ref <> 1 OR p.external_ref IS NULL)');

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(p.label LIKE ' . $search . ' OR p.component_name LIKE ' . $search . ')');
		}
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol == 'ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'category_title ' . $orderDirn . ', ordering';
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable	A database object
	 *
	 * @since	1.6
	 */
	public function getTable($type = 'Package', $prefix = 'FabrikTable', $config = array())
	{
		$config['dbo'] = FabrikWorker::getDbo();

		return FabTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @since	1.6
	 *
	 * @return  void
	 */

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the parameters.
		$params = JComponentHelper::getParams('com_fabrik');
		$this->setState('params', $params);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Load the published state
		$published = $app->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		// List state information.
		parent::populateState('u.name', 'asc');
	}

    /**
     * Method that searches the database and brings an object with all lists of the PITT project.
     *
     * @return mixed
     */
    public function getListsProjectPITT() {
        $db = $this->getDbo();
        $query = "SELECT
                    list.form_id AS `id`,
                    list.label
                    FROM
                    #__fabrik_lists AS list
                    WHERE
                    list.published = 1
                    GROUP BY
                    list.label;";

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    /**
     * Checks whether the table exists in the database.
     *
     * @return mixed
     *
     * @since version
     */
    public function checkTableExists($table){
        $db = $this->getDbo();

        $query = "SHOW TABLES LIKE '%{$table}%';";

        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Method that creates the table in the database when it does not exist.
     *
     * @return mixed
     */
    public function getCreateTableHarvesting(){
        $db = $this->getDbo();

        $db->transactionStart();

        $name = JText::_('COM_FABRIK_TABLE_NAME_HARVESTING');

        $query = "CREATE TABLE IF NOT EXISTS `#__fabrik_harvesting` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `repository` text DEFAULT NULL,
                  `list` varchar(255) DEFAULT NULL,
                  `dowload_file` varchar(255) DEFAULT NULL,
                  `extract` varchar(255) DEFAULT NULL,
                  `syncronism` tinyint(2) DEFAULT NULL,
                  `field1` varchar(255) DEFAULT NULL,
                  `field2` varchar(255) DEFAULT NULL,
                  `status` tinyint(1) DEFAULT 0,
                  `date_creation` datetime DEFAULT NULL,
                  `date_execution` datetime DEFAULT NULL,
                  `users_id` int(11) DEFAULT NULL,
                  `record_last` varchar(255) DEFAULT NULL,
                  `map_header` mediumtext DEFAULT NULL,
                  `map_metadata` mediumtext DEFAULT NULL,
                  `line_num` int(11) DEFAULT 0,
                  `page_xml` int(11) DEFAULT 0,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $db->setQuery($query);

        return $db->execute();
    }

    /**
     * Method that brings up a list of data from the main table of the harvesting tab.
     *
     * @return mixed
     */
    public function getListTableHarvesting(){
        $db = $this->getDbo();

        $query = "SELECT
                        harv.id, 
                        harv.repository, 
                        harv.list,
                        list.label,
                        harv.`status`, 
                        DATE_FORMAT(harv.date_execution, '%d/%m/%Y %H:%i:%s') AS date_exec,
                        harv.page_xml
                    FROM
                        #__fabrik_harvesting AS harv
                        LEFT JOIN
                        #__fabrik_lists AS list
                        ON 
                            harv.list = list.id                            
                    WHERE 
                        list.published = 1
                    ORDER BY
                        harv.id ASC;";

        $db->setQuery($query);

        return $db->loadObjectList();
    }
}