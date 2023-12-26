<?php
/**
 * Admin Package List Tmpl
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

if (false):
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
    JHtml::_('behavior.tooltip');
    JHTML::_('script', 'system/multiselect.js', false, true);
    $user = JFactory::getUser();
    $userId = $user->get('id');
    $listOrder = $this->state->get('list.ordering');
    $listDirn = $this->state->get('list.direction');
    ?>
    <form action="<?php echo JRoute::_('index.php?option=com_fabrik&view=packages'); ?>" method="post" name="adminForm" id="adminForm">
        <?php if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
            <?php else : ?>
            <div id="j-main-container">
                <?php endif;
                ?>
                <div id="filter-bar" class="btn-toolbar">
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="filter-search btn-group pull-left">
                                <label class="element-invisible" for="filter_search"><?php echo FText::_('JSEARCH_FILTER_LABEL'); ?></label>
                                <input type="text" name="filter_search" placeholder="<?php echo FText::_('JSEARCH_FILTER_LABEL'); ?>"
                                       id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>"
                                       title="<?php echo FText::_('COM_FABRIK_SEARCH_IN_TITLE'); ?>"/>&nbsp;
                            </div>
                            <div class="btn-group pull-left hidden-phone">
                                <button class="btn tip" type="submit" rel="tooltip" title="<?php echo FText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i
                                            class="icon-search"></i></button>
                                <button class="btn tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();"
                                        rel="tooltip" title="<?php echo FText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i>
                                </button>
                            </div>
                            <div class="btn-group pull-right hidden-phone">
                                <label for="limit"
                                       class="element-invisible"><?php echo FText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                                <?php echo $this->pagination->getLimitBox(); ?>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="clearfix"></div>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="2%">
                            <?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ID', 'p.id', $listDirn, $listOrder); ?>
                        </th>
                        <th width="1%">
                            <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)"/>
                        </th>
                        <th width="42%">
                            <?php echo JHTML::_('grid.sort', 'COM_FABRIK_LABEL', 'p.label', $listDirn, $listOrder); ?>
                        </th>
                        <th width="50%">
                            <?php echo FText::_('COM_FABRIK_FILE') ?>
                        </th>
                        <th width="5%">
                            <?php echo JHTML::_('grid.sort', 'JPUBLISHED', 'p.published', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <td colspan="5">
                            <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php foreach ($this->items as $i => $item) :
                        $ordering = ($listOrder == 'ordering');
                        $link = JRoute::_('index.php?option=com_fabrik&task=package.edit&id=' . (int)$item->id);
                        $canCreate = $user->authorise('core.create', 'com_fabrik.package.' . $item->id);
                        $canEdit = $user->authorise('core.edit', 'com_fabrik.package.' . $item->id);
                        $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
                        $canChange = $user->authorise('core.edit.state', 'com_fabrik.package.' . $item->id) && $canCheckin;
                        ?>

                        <tr class="row<?php echo $i % 2; ?>">
                            <td>
                                <?php echo $item->id; ?>
                            </td>
                            <td>
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td>
                                <?php if ($item->checked_out) : ?>
                                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'packages.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php
                                if ($item->checked_out && ($item->checked_out != $user->get('id'))) :
                                    echo $item->label;
                                else:
                                    ?>
                                    <a href="<?php echo $link; ?>">
                                        <?php echo $item->label; ?>
                                    </a>
                                <?php
                                endif;
                                ?>
                            </td>
                            <td>
                                <?php echo $item->file ?>
                            </td>
                            <td class="center">
                                <?php echo JHtml::_('jgrid.published', $item->published, $i, 'packages.', $canChange); ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>

                <input type="hidden" name="task" value=""/>
                <input type="hidden" name="boxchecked" value="0"/>
                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
                <?php echo JHtml::_('form.token'); ?>
            </div>
    </form>
<?php else:
    if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif; ?>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo FText::_('COM_FABRIK_PACKAGES_UPLOAD_TITLE'); ?></h3>
        </div>
        <div class="panel-body">
            <form action="<?php echo JRoute::_('index.php?option=com_fabrik&task=packages.uploadFile'); ?>" method="post"
                  class="form-inline" id="formUpload" name="formUpload" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="fileUpload"><strong><?php echo FText::_('COM_FABRIK_PACKAGES_UPLOAD_FILE_FORM_LABEL'); ?> </strong></label>
                    <input id="fileUpload" required type="file" name="fileUpload[]" multiple form="formUpload">
                    <button form="formUpload" type="submit" class="btn btn-primary">
                        <span class="icon-upload icon-white"></span> <?php echo FText::_('COM_FABRIK_PACKAGES_UPLOAD_TITLE_BTN'); ?>
                    </button>
                </div>


            </form>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo FText::_('COM_FABRIK_PACKAGES_CREATE_TITLE'); ?></h3>
        </div>
        <div class="panel-body">
            <form action="<?php echo JRoute::_('index.php?option=com_fabrik&task=packages.generatePackage');
            ?>" method="post" class="" id="formPackage" name="formPackage" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="exampleInputEmail1"><strong><?php echo FText::_('COM_FABRIK_PACKAGES_CREATE_NAME_FORM_LABEL'); ?></strong></label>
                    <input form="formPackage" type="text" class="form-control" id="name" name="name" required
                           placeholder="<?php echo FText::_('COM_FABRIK_PACKAGES_CREATE_NAME_FORM_LABEL'); ?>">
                </div>
                <br/>

                <div class="form-group">
                    <label for="exampleInputEmail1"><strong><?php echo FText::_('COM_FABRIK_PACKAGES_CREATE_RECORD_FORM_LABEL'); ?></strong></label>
                    <fieldset class="btn-group radio">
                        <label class="btn active btn-danger" id="btnN">
                            <input form="formPackage" type="radio" name="record" id="opRecord0" value="0">
                            <?php echo FText::_('JNO'); ?>
                        </label>

                        <label class="btn" id="btnS">
                            <input form="formPackage" type="radio" name="record" id="opRecord1" value="1">
                            <?php echo FText::_('JYES'); ?>
                        </label>
                    </fieldset>
                </div>
                <br/>

                <div class="form-group">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="span4">
                                <label><strong><?php echo FText::_('COM_FABRIK_PACKAGES_CREATE_FILE_FORM_LABEL'); ?></strong></label>
                            </div>
                            <div class="span8">
                                <label class="checkbox">
                                    <input form="formPackage" type="checkbox" id="all">
                                    <?php echo FText::_('COM_FABRIK_PACKAGES_CREATE_FILE_FORM_SELECT_ALL_LABEL'); ?>
                                </label>
                            </div>
                        </div>
                        <div class="panel-body" id="pbGroupFile">
                            <?php

                            foreach ($this->files as $key => $value) {
                                if (($value !== '.') && ($value !== '..')) {
                                    ?>
                                    <div class="span4 pakFile" id="colFile<?php echo $key; ?>">
                                        <input form="formPackage" data-num="<?php echo $key; ?>" id="file_a<?php echo $key; ?>" type="checkbox"
                                               name="file[]" value="<?php echo $value; ?>"> <?php echo $value; ?>

                                        <button title="<?php echo FText::_('COM_FABRIK_PACKAGES_CREATE_FILE_FORM_DELETE_TITLE_BTN'); ?>"
                                                type="button"
                                                class="btn btn-default btn-micro"
                                                onclick="deleteFile('<?php echo $value; ?>', <?php echo $key; ?>, '<?php echo $this->text_message; ?>');">
                                            <span class="icon icon-remove"></span>
                                        </button>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <br/>

                <input type="hidden" value="0" form="formPackage" id="recordDB" name="recordDB">
                <button form="formPackage" type="submit" class="btn btn-success">
                    <i class="icon-archive"></i> <?php echo FText::_('COM_FABRIK_PACKAGES_CREATE_FILE_FORM_TITLE_BTN'); ?>
                </button>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo FText::_('COM_FABRIK_PACKAGES_PACKAGE_LIST_TITLE'); ?></h3>
        </div>
        <div class="panel-body">
            <table class="table">
                <thead>
                <tr>
                    <th width="22%"><?php echo FText::_('COM_FABRIK_PACKAGES_LIST_TABLE_COL_NAME'); ?></th>
                    <th width="30%"><?php echo FText::_('COM_FABRIK_PACKAGES_LIST_TABLE_COL_FILE'); ?></th>
                    <th width="3%"><?php echo FText::_('COM_FABRIK_PACKAGES_LIST_TABLE_COL_RECORD'); ?></th>
                    <th width="8%"><?php echo FText::_('COM_FABRIK_PACKAGES_LIST_TABLE_COL_DATETIME'); ?></th>
                    <th width="20%"><?php echo FText::_('COM_FABRIK_PACKAGES_LIST_TABLE_COL_USER'); ?></th>
                    <th width="7%"><?php echo FText::_('COM_FABRIK_PACKAGES_LIST_TABLE_COL_OPTION'); ?></th>
                </tr>
                </thead>
            </table>
                <?php
                if (count($this->list_packages) !== 0) {
                    ?>
                    <div class="accordion" id="accordionList">
                        <?php
                        foreach ($this->list_packages as $value) {
                            if ($value->record === "1") {
                                $btn = 'btn';
                                $btni = 'icon-ok';
                            } else {
                                $btn = 'btn btn-danger';
                                $btni = 'icon-remove';
                            }
                            ?>
                            <div class="accordion-group" id="listRow<?php echo $value->id; ?>">
                                <div class="accordion-heading">
                                    <table class="table table-hover list-table">
                                    <tr width="22%">
                                        <td width="22%"><?php echo $value->name; ?></td>
                                        <td width="30%">
                                            <a class="accordion-toggle linktd" data-toggle="collapse" data-parent="#accordionList"
                                               href="#collapse<?php echo $value->id; ?>">
                                                <?php echo $value->file; ?>
                                            </a>
                                        </td>
                                        <td width="3%" class="center">
                                            <button type="button" class="<?php echo $btn; ?> btn-small">
                                                <i class="icon icon-white <?php echo $btni; ?>"></i>
                                            </button>
                                        </td>
                                        <td width="8%"><?php echo $value->date_time; ?></td>
                                        <td width="20%"><?php echo $value->usuario; ?></td>
                                        <td width="7%">
                                            <a href="<?php echo JRoute::_('components/com_fabrik/generatepackages/' . $value->file, false); ?>"
                                               class="btn btn-small" title="Download"><i class="icon icon-download"></i></a>
                                            <button onclick="deletePackage(<?php echo $value->id; ?>, '<?php echo $value->file; ?>', '<?php echo $this->text_message; ?>');" class="btn btn-danger btn-small"
                                                    title="<?php echo FText::_('COM_FABRIK_PACKAGES_CREATE_FILE_FORM_DELETE_TITLE_BTN'); ?>">
                                                <i class="icon icon-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </table>
                                </div>
                                <div id="collapse<?php echo $value->id; ?>" class="accordion-body collapse">
                                    <div class="accordion-inner row-fluid">
                                        <?php
                                        $params = json_decode($value->params);

                                        foreach ($params->files as $value1) {
                                            ?>
                                            <div class="span4 pakFile">
                                                <?php echo $value1;?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    </div>
<?php endif; ?>