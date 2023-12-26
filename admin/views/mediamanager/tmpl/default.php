<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>

<div class="row">
    <div class="span12">
        <div id="messages" style="display: none;">
            <span id="message"></span>
            <img src="/dev_projeto_pitt/media/media/images/dots.gif" alt="..." width="22" height="12">
        </div>
    </div>
</div>

<form action="index.php?option=com_media&amp;asset=<?php echo $this->asset; ?>&amp;author=<?php echo $this->author; ?>"
      class="form-horizontal" id="imageForm" method="post" enctype="multipart/form-data">

    <div id="messages" style="display: none;">
        <span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true); ?>
    </div>

    <div class="well">
        <div class="row-fluid">
            <div class="span12 control-group">
                <div class="pull-right">
                    <button class="btn btn-success button-save-selected" form="imageForm" type="button"
                            <?php if (!empty($this->onClick)) :
                            // This is for Mootools compatibility
                            ?>onclick="<?php echo $this->onClick; ?>"<?php endif; ?>
                            data-dismiss="modal"><?php echo JText::_('COM_MEDIA_INSERT'); ?></button>
                    <button class="btn button-cancel" type="button" form="imageForm"
                            onclick="window.parent.jQuery('.modal.in').modal('hide');<?php if (!empty($this->onClick)) :
                                // This is for Mootools compatibility
                                ?>parent.jModalClose();<?php endif ?>"
                            data-dismiss="modal"><?php echo JText::_('JCANCEL'); ?></button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="accordion" id="accordion">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                <?php echo JText::_("COM_MEDIA_PANEL_TITLE_JOOMLA"); ?>
            </a>
        </div>
        <div id="collapse2" class="accordion-body collapse in">
            <div class="accordion-inner">
                <div class="container-popup">
                    <div class="well">
                        <div class="row-fluid">
                            <div class="span3 control-group">
                                <div class="control-label">
                                    <label for="folder"><?php echo JText::_('COM_MEDIA_DIRECTORY'); ?></label>
                                </div>
                            </div>

                            <div class="span9 control-group">
                                <div class="controls">
                                    <?php echo $this->folderList; ?>
                                    <button form="imageForm" class="btn" type="button" id="upbutton"
                                            title="<?php echo JText::_('COM_MEDIA_DIRECTORY_UP'); ?>"><?php echo JText::_('COM_MEDIA_UP'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <iframe id="imageframe" name="imageframe"
                            src="index.php?option=com_media&amp;view=imagesList&amp;tmpl=component&amp;folder=<?php echo rawurlencode($this->state->folder); ?>&amp;asset=<?php echo $this->asset; ?>&amp;author=<?php echo $this->author; ?>"></iframe>


                    <?php if ($this->user->authorise('core.create', 'com_media')) : ?>
                        <form action="<?php echo JUri::base(); ?>index.php?option=com_fabrik&amp;task=mediamanager.upload&amp;tmpl=component&amp;<?php echo $this->session->getName() . '=' . $this->session->getId(); ?>&amp;<?php echo JSession::getFormToken(); ?>=1&amp;asset=<?php echo $this->asset; ?>&amp;author=<?php echo $this->author; ?>&amp;view=images"
                              id="uploadForm" class="form-horizontal" name="uploadForm" method="post" enctype="multipart/form-data">
                            <div id="uploadform" class="well">
                                <fieldset id="upload-noflash" class="actions">
                                    <div class="control-group">
                                        <div class="control-label">
                                            <label for="upload-file" class="control-label"><?php echo JText::_('COM_MEDIA_UPLOAD_FILE'); ?></label>
                                        </div>
                                        <div class="controls">
                                            <input required type="file" id="upload-file" name="Filedata[]" multiple/>
                                            <button class="btn btn-primary" id="upload-submit"><span
                                                        class="icon-upload icon-white"></span> <?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?>
                                            </button>
                                            <p class="help-block">
                                                <?php $cMax = (int)$this->config->get('upload_maxsize'); ?>
                                                <?php $maxSize = JUtility::getMaxUploadSize($cMax . 'MB'); ?>
                                                <?php echo JText::sprintf('JGLOBAL_MAXIMUM_UPLOAD_SIZE_LIMIT', JHtml::_('number.bytes', $maxSize)); ?>
                                            </p>
                                        </div>
                                    </div>
                                </fieldset>
                                <?php JFactory::getSession()->set('com_media.return_url', 'index.php?option=com_media&view=images&tmpl=component&fieldid=' . $this->input->getCmd('fieldid', '') . '&e_name=' . $this->input->getCmd('e_name') . '&asset=' . $this->asset . '&author=' . $this->author); ?>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                <?php echo JText::_("COM_MEDIA_PANEL_TITLE_PITT"); ?>
            </a>
        </div>
        <div id="collapse1" class="accordion-body collapse">
            <div class="accordion-inner">
                <form action="index.php?option=com_media&amp;asset=<?php echo $this->asset; ?>&amp;author=<?php echo $this->author; ?>"
                      class="form-horizontal" id="listForm" method="post" enctype="multipart/form-data">

                    <div id="messages" style="display: none;">
                        <span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true); ?>
                    </div>

                    <div class="well">
                        <div class="row-fluid">
                            <div class="span12 control-group">
                                <div class="control-label">
                                    <label for="folder"><?php echo JText::_('COM_FABRIK_LISTS'); ?></label>
                                </div>
                                <div class="controls">
                                    <select form="listForm" id="list" name="list">
                                        <option value="0"><?php echo JText::_('COM_MEDIA_PITT_OPTION_0'); ?></option>
                                        <?php
                                        foreach ($this->arList as $valList) {
                                            ?>
                                            <option value="<?php echo $valList->id; ?>"><?php echo $valList->label; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="well">
                        <div class="row-fluid">
                            <div class="span8 control-group">
                                <div class="control-label">
                                    <label for="folder"><?php echo JText::_('COM_FABRIK_ELEMENTS'); ?></label>
                                </div>
                                <div class="controls" id="combo_element"></div>
                            </div>

                            <div class="span4 control-group">
                                <div class="pull-right">
                                    <button type="button" id="thumb"
                                            class="btn btn-default"><?php echo JText::_('COM_MEDIA_PITT_BTN_THUMB'); ?></button>
                                    <button type="button" id="crop"
                                            class="btn btn-default"><?php echo JText::_('COM_MEDIA_PITT_BTN_CROP'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="well" id="search_pitt">
                        <div class="row-fluid">
                            <div class="span8 control-group">
                                <div class="control-label">
                                    <label for="folder"><?php echo JText::_('COM_MEDIA_SEARCH_LABEL_PITT'); ?></label>
                                </div>
                                <div class="controls">
                                    <input type="text" id="search" name="search">
                                    <button type="button" id="btn_search" class="btn btn-default"><span class="icon-search"></span></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-fluid" id="zone_pitt">
                        <div class="span12">
                            <ul class="manager thumbnails thumbnails-media" id="ul_list_image"></ul>
                        </div>
                    </div>

                    <hr/>

                    <div class="row-fluid" id="area_paginationPitt">
                        <div class="span12 text-center">
                            <div class="pagination pagination-toolbar clearfix">
                                <nav role="navigation" aria-label="Paginação">
                                    <ul class="pagination-list" id="pagination_pitt"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form class="form-inline">
    <div class="well">
        <div class="row-fluid">
            <div class="span3 control-group">

                <label for="f_url"><?php echo JText::_('COM_MEDIA_IMAGE_URL'); ?></label>

            </div>

            <div class="span9 control-group">

                <input form="imageForm" type="text" id="f_url" value=""/>

            </div>
        </div>
    </div>

    <?php if (!$this->state->get('field.id')) : ?>
        <div class="well">
            <div class="row-fluid">
                <div class="span6 control-group">
                    <div class="control-label">
                        <label title="<?php echo JText::_('COM_MEDIA_ALIGN_DESC'); ?>" class="noHtmlTip"
                               for="f_align"><?php echo JText::_('COM_MEDIA_ALIGN'); ?></label>
                    </div>
                    <div class="controls">
                        <select form="listForm" size="1" id="f_align">
                            <option value="" selected="selected"><?php echo JText::_('COM_MEDIA_NOT_SET'); ?></option>
                            <option value="left"><?php echo JText::_('JGLOBAL_LEFT'); ?></option>
                            <option value="center"><?php echo JText::_('JGLOBAL_CENTER'); ?></option>
                            <option value="right"><?php echo JText::_('JGLOBAL_RIGHT'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6 control-group">
                    <div class="control-label">
                        <label for="f_alt"><?php echo JText::_('COM_MEDIA_IMAGE_DESCRIPTION'); ?></label>
                    </div>
                    <div class="controls">
                        <input form="listForm" type="text" id="f_alt" value=""/>
                    </div>
                </div>
                <div class="span6 control-group">
                    <div class="control-label">
                        <label for="f_title"><?php echo JText::_('COM_MEDIA_TITLE'); ?></label>
                    </div>
                    <div class="controls">
                        <input form="listForm" type="text" id="f_title" value=""/>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6 control-group">
                    <div class="control-label">
                        <label for="f_caption"><?php echo JText::_('COM_MEDIA_CAPTION'); ?></label>
                    </div>
                    <div class="controls">
                        <input form="listForm" type="text" id="f_caption" value=""/>
                    </div>
                </div>
                <div class="span6 control-group">
                    <div class="control-label">
                        <label title="<?php echo JText::_('COM_MEDIA_CAPTION_CLASS_DESC'); ?>" class="noHtmlTip"
                               for="f_caption_class"><?php echo JText::_('COM_MEDIA_CAPTION_CLASS_LABEL'); ?></label>
                    </div>
                    <div class="controls">
                        <input form="listForm" type="text" list="d_caption_class" id="f_caption_class" value=""/>
                        <datalist id="d_caption_class">
                            <option value="text-left">
                            <option value="text-center">
                            <option value="text-right">
                        </datalist>
                    </div>
                </div>
            </div>
            <input form="listForm" type="hidden" id="dirPath" name="dirPath"/>
            <input form="listForm" type="hidden" id="f_file" name="f_file"/>
            <input form="listForm" type="hidden" id="tmpl" name="component"/>
        </div>
    <?php endif; ?>
</form>