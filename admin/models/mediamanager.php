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

class FabrikAdminModelMediamanager extends FabModelList
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
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */

	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as &$i)
		{
			$n = $i->component_name . '_' . $i->version;
			$file = JPATH_ROOT . '/tmp/' . $i->component_name . '/pkg_' . $n . '.zip';
			$url = COM_FABRIK_LIVESITE . 'tmp/' . $i->component_name . '/pkg_' . $n . '.zip';

			if (JFile::exists($file))
			{
				$i->file = '<a href="' . $url . '"><span class="icon-download"></span> pkg_' . $n . '.zip</a>';
			}
			else
			{
				$i->file = FText::_('COM_FABRIK_EXPORT_PACKAGE_TO_CREATE_ZIP');
			}
		}

		return $items;
	}

    /**
     * Method for defining joomla paths.
     *
     * @param null $property
     * @param null $default
     * @return mixed
     */
    public function getState($property = null, $default = null)
    {
        static $set;

        if (!$set)
        {
            $input  = JFactory::getApplication()->input;
            $folder = $input->get('folder', '', 'path');
            $this->setState('folder', $folder);

            $parent = str_replace("\\", '/', dirname($folder));
            $parent = ($parent == '.') ? null : $parent;
            $this->setState('parent', $parent);
            $set = true;
        }

        return parent::getState($property, $default);
    }

    /**
     * Method that brings all images from joomla.
     *
     * @return mixed
     */
    public function getImages()
    {
        $list = $this->getList();

        return $list['images'];
    }

    /**
     * Method that brings all joomla folders.
     *
     * @return mixed
     */
    public function getFolders()
    {
        $list = $this->getList();

        return $list['folders'];
    }

    /**
     * Method that brings all joomla documents.
     *
     * @return mixed
     */
    public function getDocuments()
    {
        $list = $this->getList();

        return $list['docs'];
    }

    /**
     * Method that brings all joomla videos.
     *
     * @return mixed
     */
    public function getVideos()
    {
        $list = $this->getList();

        return $list['videos'];
    }

    /**
     * Method that organizes all joomla images.
     *
     * @param null $base
     * @return mixed
     */
    public function getFolderList($base = null)
    {
        $params = JComponentHelper::getParams('com_media');
        JLoader::register('MediaHelper', JPATH_ADMINISTRATOR . '/components/com_media/helpers/media.php');

        $input  = JFactory::getApplication()->input;

        $popup_upload = $input->get('pop_up', null);
        $path         = 'file_path';
        $view         = $input->get('view');

        if (substr(strtolower($view), 0, 6) == 'images' || $popup_upload == 1)
        {
            $path = 'image_path';
        }

        define('COM_MEDIA_BASE', JPATH_ROOT . '/' . $params->get($path, 'images'));
        define('COM_MEDIA_BASEURL', JUri::root() . $params->get($path, 'images'));

        JControllerLegacy::getInstance('Media', array('base_path' => JPATH_COMPONENT_ADMINISTRATOR));

        // Get some paths from the request
        if (empty($base))
        {
            $base = COM_MEDIA_BASE;
        }

        // Corrections for windows paths
        $base = str_replace(DIRECTORY_SEPARATOR, '/', $base);
        $com_media_base_uni = str_replace(DIRECTORY_SEPARATOR, '/', COM_MEDIA_BASE);

        // Get the list of folders
        jimport('joomla.filesystem.folder');
        $folders = JFolder::folders($base, '.', true, true);

        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_MEDIA_INSERT_IMAGE'));

        // Build the array of select options for the folder list
        $options[] = JHtml::_('select.option', '', '/');

        foreach ($folders as $folder)
        {
            $folder    = str_replace($com_media_base_uni, '', str_replace(DIRECTORY_SEPARATOR, '/', $folder));
            $value     = substr($folder, 1);
            $text      = str_replace(DIRECTORY_SEPARATOR, '/', $folder);
            $options[] = JHtml::_('select.option', $value, $text);
        }

        // Sort the folder list array
        if (is_array($options))
        {
            sort($options);
        }

        // Get asset and author id (use integer filter)
        $input = JFactory::getApplication()->input;
        $asset = $input->get('asset', 0, 'integer');

        // For new items the asset is a string. JAccess always checks type first
        // so both string and integer are supported.
        if ($asset == 0)
        {
            $asset = htmlspecialchars(json_encode(trim($input->get('asset', 0, 'cmd'))), ENT_COMPAT, 'UTF-8');
        }

        $author = $input->get('author', 0, 'integer');

        // Create the dropdown folder select list
        $attribs = 'size="1" onchange="ImageManager.setFolder(this.options[this.selectedIndex].value, ' . $asset . ', ' . $author . ')" ';
        $list = JHtml::_('select.genericlist', $options, 'folderlist', $attribs, 'value', 'text', $base);

        return $list;
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
                    GROUP BY
                    list.label;";

        $db->setQuery($query);

        return $db->loadObjectList();
    }
}