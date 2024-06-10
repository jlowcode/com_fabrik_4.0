<?php
/**
 * Bootstrap Table Aditional
 *
 * @package     Joomla
 * @subpackage  Fabrik.list.workflow
 * @copyright   Copyright (C) 2018-2018  Marcel Ferrante - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

$this->requestsTabsWorkflow = $_REQUEST['workflow']['requests_tabs'];
$this->headingsWorkflow = $_REQUEST['workflow']['requests_headings'];
$this->colCountWorkflow = $_REQUEST['workflow']['requests_colCount'];
$this->rowsWorkflow = $_REQUEST['workflow']['requests_list'];
//$this->isGrouped = $_REQUEST['workflow']['requests_isGrouped'];
//$this->grouptemplates = $_REQUEST['workflow']['requests_grouptemplates'];
////$this->group_by_show_count = $_REQUEST['workflow']['requests_group_by_show_count'];
$showContainer = $_REQUEST['wfl_action'] == 'list_requests' ? 'in' : 'hide';
?>
<div id="eventsContainer" class="table-aditional <?php echo $showContainer; ?>">
    <ul style="display: flex;">
<!--        <li class="span2">-->
<!--            <div class="dropdown" id="orderBy">-->
<!--                <a href="#" class="dropdown-toggle orderBy" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">-->
<!--                    <i data-isicon="true" class="icon-upload"></i>		Order By		<b class="caret"></b>-->
<!--                </a>-->
<!--                <ul class="dropdown-menu" id="orderByUl">-->
<!--                    <li><a href="javascript:void(0);"><i data-isicon="true" class="icon-upload"></i> Export to CSV</a></li>-->
<!--                </ul>-->
<!--            </div>-->
<!--        </li>-->

<!--        orderBySelect-->
        <li class="span1"><label  for="requestTypeSelect"><?php echo Text::_('PLG_FORM_WORKFLOW_REQUEST_STATUS_LABEL'); ?></label></li>
        <li class="span3">
            <select id="requestTypeSelect">
            </select>
        </li>
        <li class="span2"><label  for="orderBySelect"><?php echo Text::_('PLG_FORM_WORKFLOW_REQUEST_ORDER_BY_LABEL'); ?></label></li>
        <li class="span3">
            <select id="orderBySelect">
            </select>
        </li>
        <li class="span1"><label  for="searchTable"><?php echo Text::_('PLG_FORM_WORKFLOW_REQUEST_SEARCH_LABEL'); ?></label></li>
        <li class="span3"><input  name="searchTable" id="searchTable" type="text" placeholder="Search"></li>

    </ul>

    <table  style="min-height: 400px;" id="tblEntAttributes" class="<?php $this->list->class; ?>">
        <thead>
            <?php echo $this->loadTemplate('headings_workflow') ?>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
<div id="workflow-pagination" class="pagination">

</div>
