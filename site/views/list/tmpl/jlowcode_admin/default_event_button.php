<?php
/**
 * Button to open events
 *
 * @package     Joomla
 * @subpackage  Fabrik.list.workflow
 * @copyright   Copyright (C) 2018-2018  Marcel Ferrante - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="title">Modo de exibição: </div>
<div class="radio-group">
    <input type="radio" id="list-view" name="view" onclick="handleRadioClick(this)">
    <label for="list-view" class="icon-list"></label>

    <input type="radio" id="grid-view" name="view" onclick="handleRadioClick(this)">
    <label for="grid-view" class="icon-grid"></label>

    <input type="radio" id="tree-view" name="view" onclick="handleRadioClick(this)">
    <label for="tree-view" class="icon-tree"></label>
</div>

<?php
if ($_REQUEST['workflow']['showEventsButton'] == true):
    $url = ($_REQUEST['wfl_action'] == 'list_requests') ? $_REQUEST['workflow']['list_link'] : $_REQUEST['workflow']['requests_link'];
    $active = ($_REQUEST['wfl_action'] == 'list_requests') ? 'active' : '';
    ?>
    <li class="<?php echo $active ?>">
        <!--<a class="showRequests" href="javascript://" onclick="showRequests();">-->
        <a id="showRequests" class="showRequests" href="<?php echo $url ?>">
            <?php //echo $this->buttons->requests; ?>
            <?php echo $_REQUEST['workflow']['eventsButton'] ?>
            <span class="badge bg-primary rounded-pill">
                <?php echo $_REQUEST['workflow']['requests_count'] ?>
            </span>
        </a>
    </li>
<?php endif; ?>
