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
