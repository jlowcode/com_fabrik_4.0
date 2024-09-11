<?php
/**
 * Bootstrap Table Aditional
 *
 * @package     Joomla
 * @subpackage  Fabrik.list.workflow
 * @copyright   Copyright (C) 2018-2018  Marcel Ferrante - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

// No direct access
defined('_JEXEC') or die('Restricted access');

$this->requestsTabs = $_REQUEST['workflow']['requests_tabs'];
$this->headings = $_REQUEST['workflow']['requests_headings'];
$this->colCount = $_REQUEST['workflow']['requests_colCount'];
$this->rows = $_REQUEST['workflow']['requests_list'];
//$this->isGrouped = $_REQUEST['workflow']['requests_isGrouped'];
//$this->grouptemplates = $_REQUEST['workflow']['requests_grouptemplates'];
////$this->group_by_show_count = $_REQUEST['workflow']['requests_group_by_show_count'];
$showContainer = $_REQUEST['wfl_action'] == 'list_requests' ? 'in' : 'hide';
?>
<div id="eventsContainer" class="table-aditional <?php echo $showContainer; ?>">
<ul style="display: flex; align-items: center;">
        <li style="margin-right: 5px"><label  for="requestTypeSelect"><?php echo Text::_('PLG_FORM_WORKFLOW_REQUEST_STATUS_LABEL'); ?>:</label></li>
        <li style="margin-right: 40px">
            <select style="height: 41px" id="requestTypeSelect">
            </select>
        </li>
        <li style="margin-right: 5px"><label  for="orderBySelect"><?php echo Text::_('PLG_FORM_WORKFLOW_REQUEST_ORDER_BY_LABEL'); ?>:</label></li>
        <li style="margin-right: 40px">
            <select style="height: 41px" id="orderBySelect">
            </select>
        </li>
        <li style="margin-right: 5px"><label  for="searchTable"><?php echo Text::_('PLG_FORM_WORKFLOW_REQUEST_SEARCH_LABEL'); ?>:</label></li>
        <li><input  name="searchTable" id="searchTable" type="text" placeholder="Search"></li>

    </ul>

    <table  style="min-height: 400px;" id="tblEntAttributes" class="<?php $this->list->class; ?>">
        <thead>
            <?php echo $this->loadTemplate('headings') ?>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
<div id="workflow-pagination" class="pagination">

</div>
