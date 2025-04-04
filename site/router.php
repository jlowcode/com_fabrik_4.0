<?php
/**
 * build route
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2020  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Fabrik\Helpers\Worker;

/**
 * if using file extensions sef and htaccess :
 * you need to edit your .htaccess file to:
 *
 * RewriteCond %{REQUEST_URI} (/|\.csv|\.php|\.html|\.htm|\.feed|\.pdf|\.raw|/[^.]*)$  [NC]
 *
 * otherwise the csv exporter will give you a 404 error
 *
 */

/**
 * build route
 *
 * @param   object  &$query  uri?
 *
 * @return  array url
 */
function fabrikBuildRoute(&$query)
{
	$segments = array();
	$app = Factory::getApplication();
	$menu = $app->getMenu();

	if (empty($query['Itemid']))
	{
		$menuItem = $menu->getActive();
		$menuItemGiven = false;
	}
	else
	{
		$menuItem = $menu->getItem($query['Itemid']);
		$menuItemGiven = true;
	}

	// Are we dealing with a view that is attached to a menu item https://github.com/Fabrik/fabrik/issues/498?
	$hasMenu = _fabrikRouteMatchesMenuItem($query, $menuItem);

	if ($hasMenu)
	{
		unset($query['view']);

		if (isset($query['catid']))
		{
			unset($query['catid']);
		}

		if (isset($query['layout']))
		{
			unset($query['layout']);
		}

		unset($query['id']);

		if (isset($query['listid']))
		{
			unset($query['listid']);
		}

		if (isset($query['rowid']))
		{
			unset($query['rowid']);
		}

		if (isset($query['formid']))
		{
			unset($query['formid']);
		}

		return $segments;
	}

	if (isset($query['c']))
	{
		// $segments[] = $query['c'];//remove from sef url
		unset($query['c']);
	}

	if (isset($query['task']))
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}

	if (isset($query['view']))
	{
		$view = $query['view'];
		$segments[] = $view;
		unset($query['view']);
	}
	else
	{
		$view = '';
	}

	if (isset($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}

	if (isset($query['layout']))
	{
		$segments[] = $query['layout'];
		unset($query['layout']);
	}

	if (isset($query['formid']))
	{
		$segments[] = $query['formid'];
		unset($query['formid']);
	}

	// $$$ hugh - looks like we still have some links using 'fabrik' instead of 'formid'
	if (isset($query['fabrik']))
	{
		$segments[] = $query['fabrik'];
		unset($query['fabrik']);
	}

	if (isset($query['listid']))
	{
		if ($view != 'form' && $view != 'details')
		{
			$segments[] = $query['listid'];
		}

		unset($query['listid']);
	}

	if (isset($query['rowid']))
	{
		$segments[] = $query['rowid'];
		unset($query['rowid']);
	}

	/* $$$trob: Don't use calculations as SEF segment (there may be old wrong Fabrik 2/3 menu links, it will break SEF list view)
	if (isset($query['calculations']))
	{
		$segments[] = $query['calculations'];
		unset($query['calculations']);
	}
	*/

	if (isset($query['filetype']))
	{
		$segments[] = $query['filetype'];
		unset($query['filetype']);
	}

	if (isset($query['format']))
	{
		if ($view == 'visualization' && Factory::getConfig()->get('sef_suffix', 0)) {

			// Was causing error when sef on, url rewrite on and suffix add to url on. (in J!3)
			// Causing error (in fullcalendar viz) if missing in J!4+
			$segments[] = $query['format'];

			/**
			 * Don't unset as with sef urls and extensions on - if we unset it
			 * the url's prefix is set to .html (J!3)
			 */
			 unset($query['format']);
		 }
		 
	}

	if (isset($query['type']))
	{
		$segments[] = $query['type'];
		unset($query['type']);
	}

	// Test
	if (isset($query['fabriklayout']))
	{
		$segments[] = $query['fabriklayout'];
		unset($query['fabriklayout']);
	}

	return $segments;
}

/**
 * Ascertain is the route that is being parsed is the same as the menu item desginated in
 * its Itemid value.
 *
 * @param $query
 * @param $menuItem
 *
 * @return bool
 */
function _fabrikRouteMatchesMenuItem($query, $menuItem)
{
	if (!$menuItem instanceof Joomla\CMS\Menu\MenuItem || !isset($query['view']))
	{
		return false;
	}
	$queryView = ArrayHelper::getValue($query, 'view');
	$menuView = ArrayHelper::getValue($menuItem->query, 'view');

	if ($queryView !== $menuView)
	{
		return false;
	}
	unset($query['Itemid']);
	if (isset($query['lang'])) unset($query['lang']);

	switch ($queryView)
	{
		case 'list':
			if (!isset($query['listid']))
			{
				$query['listid'] = $query['id'];
				unset($query['id']);
			}

			break;

		case 'details':
		case 'form':
			if (isset($query['rowid']) && !isset($menuItem->query['rowid']))
			{
				$menuItem->query['rowid'] = $query['rowid'];
			}

			break;
	}

	return $query === $menuItem->query;

	return true;
}

/**
 * parse route
 *
 * @param   array  $segments  url
 *
 * @return  array vars
 */

function fabrikParseRoute(&$segments)
{
	// $vars are what Joomla then uses for its $_REQUEST array
	$vars = array();
	$view = $segments[0];

	if (strstr($view, '.'))
	{
		$view = explode('.', $view);
		$view = array_shift($view);
	}

	/**
	 * View (controller not passed into segments)
	 *
	 * $$$ hugh - don't use FArrayHelper::getValue() here, use original ArrayHelper.  Don't ask.
	 * Well, since you asked, some users are reporting issues with the helper not having been
	 * loaded (some bizarre 3rd party system plugin doing funky things), and since we don't need
	 * what our wrapper does for this simple usage ... yes, we could specifically load our helper here,
	 * and (dear reader) if you wanna do that be my guest.
	 * J!4: unset processed segments
	 */

	$viewFound = true;

	switch ($view)
	{
		case 'form':
		case 'details':
		case 'emailform':
			$vars['view'] = $segments[0];
			$vars['formid'] = ArrayHelper::getValue($segments, 1, 0);
			$vars['rowid'] = ArrayHelper::getValue($segments, 2, '');
			$vars['format'] = ArrayHelper::getValue($segments, 3, 'html');
			unset($segments[0]);
			unset($segments[1]);
			unset($segments[2]);
			unset($segments[3]);
			break;
		case 'table':
		case 'list':
			$vars['view'] = ArrayHelper::getValue($segments, 0, '');
			$vars['listid'] = ArrayHelper::getValue($segments, 1, 0);
			unset($segments[0]);
			unset($segments[1]);
			break;
		case 'import':
			$vars['view'] = 'import';
			$vars['listid'] = ArrayHelper::getValue($segments, 1, 0);
			$vars['filetype'] = ArrayHelper::getValue($segments, 2, 0);
			unset($segments[0]);
			unset($segments[1]);
			unset($segments[2]);
			break;
		case 'visualization':
			$vars['view'] = 'visualization';
			$vars['id'] = ArrayHelper::getValue($segments, 1, 0);
			$vars['format'] = ArrayHelper::getValue($segments, 2, 'html');
			unset($segments[0]);
			unset($segments[1]);
			unset($segments[2]);
			break;
		default:
			$viewFound = false;
			break;
	}

	 /*
	 * if a Fabrik view is home page, and this is a 404, no segments, but J! will still try and route com_fabrik
	 * So have a peek at the active menu, and break down the link
	  *
	  * 7/25/2017, made this behavior an option, as can cause SEF issues with duplicate pages
	 */
/* //Henk removed this for J!4
    $config = ComponentHelper::getParams('com_fabrik');
    $home404 = $config->get('fabrik_home_404', '0') === '1';

	if (!$home404 && !$viewFound)
	{
		$app   = Factory::getApplication();
		$app->enqueueMessage(Text::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		$menus = AbstractMenu::getInstance('site');
		$menu  = $menus->getActive();
		$link  = parse_url($menu->link);
		$qs    = array();
		if (array_key_exists('query', $link))
		{
			parse_str($link['query'], $qs);
			$option = ArrayHelper::getValue($qs, 'option', '');
			if ($option == 'com_fabrik')
			{
				switch ($qs['view'])
				{
					case 'form':
					case 'details':
					case 'emailform':
						$vars['view']   = $qs['view'];
						$vars['formid'] = ArrayHelper::getValue($qs, 'formid', '');
						$vars['rowid']  = ArrayHelper::getValue($qs, 'rowid', '');
						$vars['format'] = ArrayHelper::getValue($qs, 'format', 'html');
						$viewFound = true;
						break;
					case 'table':
					case 'list':
						$vars['view']   = $qs['view'];
						$vars['listid'] = ArrayHelper::getValue($qs, 'listid', '');
						$viewFound = true;
						break;
					case 'import':
						$vars['view']     = 'import';
						$vars['listid']   = ArrayHelper::getValue($qs, 'listid', '');
						$vars['filetype'] = ArrayHelper::getValue($qs, 'filetype', '');
						$viewFound = true;
						break;
					case 'visualization':
						$vars['view']   = 'visualization';
						$vars['id']     = ArrayHelper::getValue($qs, 'id', '');
						$vars['format'] = ArrayHelper::getValue($qs, 'format', 'html');
						$viewFound = true;
						break;
					default:
						break;
				}
			}
		}
	}
*/
	if (!$viewFound)
	{
		//JError::raiseError(404, Text::_('JGLOBAL_RESOURCE_NOT_FOUND'));
        throw new \Exception(Text::_('JGLOBAL_RESOURCE_NOT_FOUND'), 404);
	}
	
	//J!4 Router will fail if there are unprocessed segments. So create warning/log and reset segments
	if (count($segments) >0)
	{
		FabrikWorker::logError('Incomplete Fabrik routing. vars: ' . implode(',',$vars) . '; ignored segments: '. implode(',',$segments), 'warning');
		$segments = [];
	}

	return $vars;
}
