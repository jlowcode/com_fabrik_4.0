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
    <script>
        function showRequests() {
            var form = document.createElement('form');
            document.body.appendChild(form);
            form.method = 'post';
            form.action = "<?php echo $url?>";
            // for (var name in data) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'layout';
            input.value = 'bootstrap';
            form.appendChild(input);
            // }
            form.submit();
        }
    </script>
    <li class="<?php echo $active ?>">
        <a class="showRequests" href="javascript://" onclick="showRequests();">
        <!-- <a id="showRequests" class="showRequests" href="<?php echo $url ?>"> -->
            <?php //echo $this->buttons->requests; ?>
            <?php echo $_REQUEST['workflow']['eventsButton'] ?>(<?php echo $_REQUEST['workflow']['requests_count'] ?>)
        </a>
    </li>
<?php endif; ?>
