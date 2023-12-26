<?php
/**
 * Packages list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       1.6
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once 'fabcontrolleradmin.php';

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.registry.registry');
jimport('joomla.image.image');

use Joomla\CMS\Component\ComponentHelper;


/**
 * Packages list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.0
 */
class FabrikAdminControllerMediamanager extends FabControllerAdmin
{
    protected $folder = '';

    /**
     * Proxy for getModel.
     *
     * @param string $name model name
     * @param string $prefix model prefix
     *
     * @return  J model
     */
    public function &getModel($name = 'Mediamanager', $prefix = 'FabrikAdminModel')
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }

    /**
     * Upload one or more files
     *
     * @return  boolean
     *
     * @since   1.5
     */
    public function upload()
    {
        // Check for request forgeries
        $this->checkToken('request');

        $params = JComponentHelper::getParams('com_media');

        // Get some data from the request
        $files = $this->input->files->get('Filedata', array(), 'array');
        $return = JFactory::getSession()->get('com_media.return_url');
        $this->folder = $this->input->get('folder', '', 'path');

        $input = JFactory::getApplication()->input;

        $popup_upload = $input->get('pop_up', null);
        $path = 'file_path';
        $view = $input->get('view');

        if (substr(strtolower($view), 0, 6) == 'images' || $popup_upload == 1) {
            $path = 'image_path';
        }

        define('COM_MEDIA_BASE', JPATH_ROOT . '/' . $params->get($path, 'images'));
        define('COM_MEDIA_BASEURL', JUri::root() . $params->get($path, 'images'));

        // Don't redirect to an external URL.
        if (!JUri::isInternal($return)) {
            $return = '';
        }

        // Set the redirect
        if ($return) {
            $this->setRedirect($return . '&folder=' . $this->folder);
        } else {
            $this->setRedirect('index.php?option=com_media&folder=' . $this->folder);
        }

        if (!$files) {
            // If we could not get any data from the request we can not upload it.
            JFactory::getApplication()->enqueueMessage(JText::_('COM_MEDIA_ERROR_WARNFILENOTSAFE'), 'error');

            return false;
        }

        // Authorize the user
        if (!$this->authoriseUser('create')) {
            return false;
        }

        // If there are no files to upload - then bail
        if (empty($files)) {
            return false;
        }

        // Total length of post back data in bytes.
        $contentLength = (int)$_SERVER['CONTENT_LENGTH'];


        // Instantiate the media helper
        //$mediaHelper = new JHelperMedia;

        // Maximum allowed size of post back data in MB.
        $postMaxSize = $this->toBytes(ini_get('post_max_size'));

        // Maximum allowed size of script execution in MB.
        $memoryLimit = $this->toBytes(ini_get('memory_limit'));

        // Check for the total size of post back data.
        if (($postMaxSize > 0 && $contentLength > $postMaxSize)
            || ($memoryLimit != -1 && $contentLength > $memoryLimit)) {
            JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_WARNUPLOADTOOLARGE'));

            return false;
        }

        $uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
        $uploadMaxFileSize = $this->toBytes(ini_get('upload_max_filesize'));

        // Perform basic checks on file info before attempting anything
        foreach ($files as &$file) {
            // Make the filename safe
            $file['name'] = JFile::makeSafe($file['name']);

            // We need a url safe name
            $fileparts = pathinfo(COM_MEDIA_BASE . '/' . $this->folder . '/' . $file['name']);

            if (strpos(realpath($fileparts['dirname']), JPath::clean(realpath(COM_MEDIA_BASE))) !== 0) {
                JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_WARNINVALID_FOLDER'));

                return false;
            }

            // Transform filename to punycode, check extension and transform it to lowercase
            $fileparts['filename'] = JStringPunycode::toPunycode($fileparts['filename']);
            $tempExt = !empty($fileparts['extension']) ? strtolower($fileparts['extension']) : '';

            // Neglect other than non-alphanumeric characters, hyphens & underscores.
            $safeFileName = preg_replace(array("/[\\s]/", '/[^a-zA-Z0-9_\-]/'), array('_', ''), $fileparts['filename']) . '.' . $tempExt;

            $file['name'] = $safeFileName;

            $file['filepath'] = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $this->folder, $file['name'])));


            if (($file['error'] == 1)
                || ($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize)
                || ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize)) {
                // File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
                JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_WARNFILETOOLARGE'));

                return false;
            }

            if (JFile::exists($file['filepath'])) {
                // A file with this name already exists
                JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_FILE_EXISTS'));

                return false;
            }

            if (!isset($file['name'])) {
                // No filename (after the name was cleaned by JFile::makeSafe)
                $this->setRedirect('index.php', JText::_('COM_MEDIA_INVALID_REQUEST'), 'error');

                return false;
            }
        }


        // Set FTP credentials, if given
        JClientHelper::setCredentialsFromRequest('ftp');
        JPluginHelper::importPlugin('content');
        $dispatcher = JEventDispatcher::getInstance();

        foreach ($files as &$file) {
            // The request is valid
            $err = 'com_fabrik';

            if (!$this->canUpload($file, $err)) {
                // The file can't be uploaded
                return false;
            }

            // Trigger the onContentBeforeSave event.
            $object_file = new JObject($file);
            $result = $dispatcher->trigger('onContentBeforeSave', array('com_media.file', &$object_file, true));

            if (in_array(false, $result, true)) {
                // There are some errors in the plugins
                JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));

                return false;
            }

            if (!JFile::upload($object_file->tmp_name, $object_file->filepath)) {
                // Error in upload
                JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));

                return false;
            }

            // Trigger the onContentAfterSave event.
            $dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file, true));
            $this->setMessage(JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE))));
        }

        return true;
    }

    /**
     * Check that the user is authorized to perform this action
     *
     * @param string $action - the action to be peformed (create or delete)
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function authoriseUser($action)
    {
        if (!JFactory::getUser()->authorise('core.' . strtolower($action), 'com_media')) {
            // User is not authorised
            JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_' . strtoupper($action) . '_NOT_PERMITTED'));

            return false;
        }

        return true;
    }

    /**
     * Method that determines the amount of bytes will determine the upload.
     *
     * @param $val
     * @return float|int
     */
    public function toBytes($val)
    {
        switch ($val[strlen($val) - 1]) {
            case 'M':
            case 'm':
                return (int)$val * 1048576;
            case 'K':
            case 'k':
                return (int)$val * 1024;
            case 'G':
            case 'g':
                return (int)$val * 1073741824;
            default:
                return $val;
        }
    }

    /**
     * Checks if the file can be uploaded
     *
     * @param array $file File information
     * @param string $component The option name for the component storing the parameters
     *
     * @return  boolean
     *
     * @since   3.2
     */
    public function canUpload($file, $component)
    {
        $app = \JFactory::getApplication();
        $params = ComponentHelper::getParams($component);

        if (empty($file['name'])) {
            $app->enqueueMessage(\JText::_('JLIB_MEDIA_ERROR_UPLOAD_INPUT'), 'error');

            return false;
        }

        jimport('joomla.filesystem.file');

        if (str_replace(' ', '', $file['name']) !== $file['name'] || $file['name'] !== \JFile::makeSafe($file['name'])) {
            $app->enqueueMessage(\JText::_('JLIB_MEDIA_ERROR_WARNFILENAME'), 'error');

            return false;
        }

        $filetypes = explode('.', $file['name']);

        if (count($filetypes) < 2) {
            // There seems to be no extension
            $app->enqueueMessage(\JText::_('JLIB_MEDIA_ERROR_WARNFILETYPE'), 'error');

            return false;
        }


        array_shift($filetypes);

        // Media file names should never have executable extensions buried in them.
        $executable = array(
            'php', 'js', 'exe', 'phtml', 'java', 'perl', 'py', 'asp', 'dll', 'go', 'ade', 'adp', 'bat', 'chm', 'cmd', 'com', 'cpl', 'hta', 'ins', 'isp',
            'jse', 'lib', 'mde', 'msc', 'msp', 'mst', 'pif', 'scr', 'sct', 'shb', 'sys', 'vb', 'vbe', 'vbs', 'vxd', 'wsc', 'wsf', 'wsh',
        );

        $check = array_intersect($filetypes, $executable);

        if (!empty($check)) {
            $app->enqueueMessage(\JText::_('JLIB_MEDIA_ERROR_WARNFILETYPE'), 'error');

            return false;
        }

        $filetype = array_pop($filetypes);
        $allowable = array_map('trim', explode(',', $params->get('upload_extensions')));
        $ignored = array_map('trim', explode(',', $params->get('ignore_extensions')));

        if ($filetype == '' || $filetype == false || (!in_array($filetype, $allowable) && !in_array($filetype, $ignored))) {
            $app->enqueueMessage(\JText::_('JLIB_MEDIA_ERROR_WARNFILETYPE'), 'error');

            return false;
        }

        $maxSize = (int)($params->get('upload_maxsize', 0) * 1024 * 1024);

        if ($maxSize > 0 && (int)$file['size'] > $maxSize) {
            $app->enqueueMessage(\JText::_('JLIB_MEDIA_ERROR_WARNFILETOOLARGE'), 'error');

            return false;
        }

        if ($params->get('restrict_uploads', 1)) {
            $images = array_map('trim', explode(',', $params->get('image_extensions')));

            if (in_array($filetype, $images)) {
                // If tmp_name is empty, then the file was bigger than the PHP limit
                if (!empty($file['tmp_name'])) {
                    // Get the mime type this is an image file
                    $mime = $this->getMimeType($file['tmp_name'], true);

                    // Did we get anything useful?
                    if ($mime != false) {
                        $result = $this->checkMimeType($mime, $component);

                        // If the mime type is not allowed we don't upload it and show the mime code error to the user
                        if ($result === false) {
                            $app->enqueueMessage(\JText::sprintf('JLIB_MEDIA_ERROR_WARNINVALID_MIMETYPE', $mime), 'error');

                            return false;
                        }
                    } // We can't detect the mime type so it looks like an invalid image
                    else {
                        $app->enqueueMessage(\JText::_('JLIB_MEDIA_ERROR_WARNINVALID_IMG'), 'error');

                        return false;
                    }
                } else {
                    $app->enqueueMessage(\JText::_('JLIB_MEDIA_ERROR_WARNFILETOOLARGE'), 'error');

                    return false;
                }
            } elseif (!in_array($filetype, $ignored)) {
                // Get the mime type this is not an image file
                $mime = $this->getMimeType($file['tmp_name'], false);

                // Did we get anything useful?
                if ($mime != false) {
                    $result = $this->checkMimeType($mime, $component);

                    // If the mime type is not allowed we don't upload it and show the mime code error to the user
                    if ($result === false) {
                        $app->enqueueMessage(\JText::sprintf('JLIB_MEDIA_ERROR_WARNINVALID_MIMETYPE', $mime), 'error');

                        return false;
                    }
                } // We can't detect the mime type so it looks like an invalid file
                else {
                    $app->enqueueMessage(\JText::_('JLIB_MEDIA_ERROR_WARNINVALID_MIME'), 'error');

                    return false;
                }

                if (!\JFactory::getUser()->authorise('core.manage', $component)) {
                    $app->enqueueMessage(\JText::_('JLIB_MEDIA_ERROR_WARNNOTADMIN'), 'error');

                    return false;
                }
            }
        }

        $xss_check = file_get_contents($file['tmp_name'], false, null, -1, 256);

        $html_tags = array(
            'abbr', 'acronym', 'address', 'applet', 'area', 'audioscope', 'base', 'basefont', 'bdo', 'bgsound', 'big', 'blackface', 'blink',
            'blockquote', 'body', 'bq', 'br', 'button', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'comment', 'custom', 'dd', 'del',
            'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'fn', 'font', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'head', 'hr', 'html', 'iframe', 'ilayer', 'img', 'input', 'ins', 'isindex', 'keygen', 'kbd', 'label', 'layer', 'legend', 'li', 'limittext',
            'link', 'listing', 'map', 'marquee', 'menu', 'meta', 'multicol', 'nobr', 'noembed', 'noframes', 'noscript', 'nosmartquotes', 'object',
            'ol', 'optgroup', 'option', 'param', 'plaintext', 'pre', 'rt', 'ruby', 's', 'samp', 'script', 'select', 'server', 'shadow', 'sidebar',
            'small', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'title',
            'tr', 'tt', 'ul', 'var', 'wbr', 'xml', 'xmp', '!DOCTYPE', '!--',
        );

        foreach ($html_tags as $tag) {
            // A tag is '<tagname ', so we need to add < and a space or '<tagname>'
            if (stripos($xss_check, '<' . $tag . ' ') !== false || stripos($xss_check, '<' . $tag . '>') !== false) {
                $app->enqueueMessage(\JText::_('JLIB_MEDIA_ERROR_WARNIEXSS'), 'error');

                return false;
            }
        }

        return true;
    }

    /**
     * Get the Mime type
     *
     * @param string $file The link to the file to be checked
     * @param boolean $isImage True if the passed file is an image else false
     *
     * @return  mixed    the mime type detected false on error
     *
     * @since   3.7.2
     */
    private function getMimeType($file, $isImage = false)
    {
        // If we can't detect anything mime is false
        $mime = false;

        try {
            if ($isImage && function_exists('exif_imagetype')) {
                $mime = image_type_to_mime_type(exif_imagetype($file));
            } elseif ($isImage && function_exists('getimagesize')) {
                $imagesize = getimagesize($file);
                $mime = isset($imagesize['mime']) ? $imagesize['mime'] : false;
            } elseif (function_exists('mime_content_type')) {
                // We have mime magic.
                $mime = mime_content_type($file);
            } elseif (function_exists('finfo_open')) {
                // We have fileinfo
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file);
                finfo_close($finfo);
            }
        } catch (\Exception $e) {
            // If we have any kind of error here => false;
            return false;
        }

        // If we can't detect the mime try it again
        if ($mime === 'application/octet-stream' && $isImage === true) {
            $mime = $this->getMimeType($file, false);
        }

        // We have a mime here
        return $mime;
    }

    /**
     * Checks the Mime type
     *
     * @param string $mime The mime to be checked
     * @param string $component The optional name for the component storing the parameters
     *
     * @return  boolean  true if mime type checking is disabled or it passes the checks else false
     *
     * @since   3.7
     */
    private function checkMimeType($mime, $component = 'com_media')
    {
        $params = ComponentHelper::getParams($component);

        if ($params->get('check_mime', 1)) {
            // Get the mime type configuration
            $allowedMime = array_map('trim', explode(',', $params->get('upload_mime')));

            // Mime should be available and in the whitelist
            return !empty($mime) && in_array($mime, $allowedMime);
        }

        // We don't check mime at all or it passes the checks
        return true;
    }

    /**
     * Method performed to fetch a json of information to create the element combo.
     */
    public function listElementosImage()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $id = $app->input->getInt("idList");

        $sql = "SELECT
                element.id,
                element.label,
                element.name,
                list.db_table_name AS `table`,
                element.`plugin`,
                element.params,
                list.params as paramList
                FROM
                #__fabrik_elements AS element
                LEFT JOIN #__fabrik_formgroup AS fgroup ON element.group_id = fgroup.group_id
                LEFT JOIN #__fabrik_lists AS list ON fgroup.form_id = list.form_id
                WHERE
                element.`plugin` IN ('image', 'fileupload') AND
                fgroup.form_id = {$id}
                ORDER BY
                element.label ASC;";

        $db->setQuery($sql);

        $list = $db->loadObjectList();

        if (count($list) > 0) {
            foreach ($list as $key => $value) {
                $list[$key]->params = json_decode($value->params);
                $list[$key]->paramList = json_decode($value->paramList);
            }

            echo json_encode($list);
        } else {
            echo '0';
        }

        $app->close();
    }

    /**
     * Method performed to receive the list and element and send an information packet in json and display all images and pass the total value of
     * these images to create the pagination.
     */
    public function listImage()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $name = $app->input->getString("name");
        $table = $app->input->getString("table");
        $type = $app->input->getString("type");
        $search_field = $app->input->getString("search_field");
        $search = $app->input->getString("search", null);
        $ajax_upload = $app->input->getInt("ajax_upload");
        $start = $app->input->getInt("start");
        $stop = $app->input->getInt("stop");

        if (($search_field !== 'null') && ($search !== NULL)) {
            $sql2 = "SELECT
                        element.`name` as nm_element
                        FROM
                        #__fabrik_elements AS element
                        WHERE
                        element.id IN ($search_field);";

            $db->setQuery($sql2);
            $db->execute();

            $ar_element_nm = $db->loadObjectList();

            $where = " WHERE ";

            foreach ($ar_element_nm as $key => $value) {
                if ($key === 0) {
                    $where .= " tab.{$value->nm_element} LIKE '%{$search}%' ";
                }else{
                    $where .= " OR tab.{$value->nm_element} LIKE '%{$search}%' ";
                }
            }
        }

        if (($type === 'fileupload') && ($ajax_upload == 1)) {
            $name_table = $table . '_repeat_' . $name;

            $sql = "SELECT count(repet.id) AS total FROM {$name_table} AS repet ";
            $sql1 = "SELECT repet.* FROM {$name_table} AS repet ";

            if (($search_field !== 'null') && ($search !== NULL)) {
                $sql .= " LEFT JOIN {$table} AS tab ON tab.id = repet.parent_id " . $where;
                $sql1 .= " LEFT JOIN {$table} AS tab ON tab.id = repet.parent_id " . $where;
            }

            $sql1 .= " ORDER BY repet.id ASC LIMIT {$start}, {$stop};";

        } elseif (($type === 'image') || (($type === 'fileupload') && ($ajax_upload == 0))) {
            $sql = "SELECT count(tab.id) AS total FROM {$table} AS tab";

            $sql1 = "SELECT tab.* FROM {$table} AS tab";

            if (($search_field !== 'null') && ($search !== NULL)) {
                $sql .= $where;
                $sql1 .= $where;
            }

            $sql1 .= " ORDER BY tab.id ASC LIMIT {$start}, {$stop};";
        }

        $db->setQuery($sql);
        $db->execute();

        $number_list = $db->loadObject();
        $data['total'] = $number_list->total;

        $db->setQuery($sql1);
        $db->execute();

        $list = $db->loadAssocList();

        $data['text'] = '';

        if (count($list) > 0) {
            foreach ($list as $key => $value) {
                if ($type === 'fileupload') {
                    $file = file_exists(JPATH_ROOT . $value[$name]);
                } elseif ($type === 'image') {
                    $file = file_exists(JPATH_ROOT . '/' . $value[$name]);
                }

                if ($file) {
                    $ar_img = explode('/', $value[$name]);
                    $ar_length = count($ar_img) - 1;

                    $str_route = $ar_img[$ar_length - 1] . '/' . $ar_img[$ar_length];

                    $tex_trunc = JHtml::_('string.truncate', $db->escape($ar_img[$ar_length]), 10, false);

                    $data['text'] .= '<li class="imgOutline thumbnail height-80 width-80 center">';
                    $data['text'] .= '<a class="img-preview" title="' . $tex_trunc . '" href="javascript:ImageManager.populateFields(\'' . $str_route . '\')"
                    onClick="javascript:layoutMediaPitt(' . $key . ');" id="link_' . $key . '">';
                    $data['text'] .= '<div class="imgThumb">';
                    $data['text'] .= '<div class="imgThumbInside">';
                    $data['text'] .= '<img src="' . JUri::root() . $value[$name] . '" width="100%" alt="' . $tex_trunc . '" 
                style="vertical-align: middle; max-width: 100%; height: auto;">';
                    $data['text'] .= '</div>';
                    $data['text'] .= '</div>';
                    $data['text'] .= '<div class="imgDetails small">' . $tex_trunc . '</div>';
                    $data['text'] .= '</a>';
                    $data['text'] .= '</li>';
                }
            }

            echo json_encode($data);
        } else {
            echo '0';
        }

        $app->close();
    }

    /**
     * Method executed to receive the list and the element and send a packet of information in json and it shows all the images and passes the total
     * value of these images to create the pagination and is referenced to the thumbs and crop buttons.
     */
    public function btnElementImage()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $name = $app->input->getString("name");
        $table = $app->input->getString("table");
        $ajax_upload = $app->input->getInt("ajax_upload");
        $drive = $app->input->getString("drive");
        $start = $app->input->getInt("start");
        $stop = $app->input->getInt("stop");
        $search_field = $app->input->getString("search_field");
        $search = $app->input->getString("search", null);

        if (($search_field !== 'null') && ($search !== NULL)) {
            $sql2 = "SELECT
                        element.`name` as nm_element
                        FROM
                        #__fabrik_elements AS element
                        WHERE
                        element.id IN ($search_field);";

            $db->setQuery($sql2);
            $db->execute();

            $ar_element_nm = $db->loadObjectList();

            $where = " WHERE ";

            foreach ($ar_element_nm as $key => $value) {
                if ($key === 0) {
                    $where .= " tab.{$value->nm_element} LIKE '%{$search}%' ";
                }else{
                    $where .= " OR tab.{$value->nm_element} LIKE '%{$search}%' ";
                }
            }
        }

        if ($ajax_upload == 1) {
            $name_table = $table . '_repeat_' . $name;

            $sql = "SELECT count(repet.id) AS total FROM {$name_table} AS repet ";
            $sql1 = "SELECT repet.* FROM {$name_table} AS repet ";

            if (($search_field !== 'null') && ($search !== NULL)) {
                $sql .= " LEFT JOIN {$table} AS tab ON tab.id = repet.parent_id " . $where;
                $sql1 .= " LEFT JOIN {$table} AS tab ON tab.id = repet.parent_id " . $where;
            }

            $sql1 .= " ORDER BY repet.id ASC LIMIT {$start}, {$stop};";
        } elseif ($ajax_upload == 0) {
            $sql = "SELECT count(tab.id) AS total FROM {$table} AS tab";

            $sql1 = "SELECT tab.* FROM {$table} AS tab";

            if (($search_field !== 'null') && ($search !== NULL)) {
                $sql .= $where;
                $sql1 .= $where;
            }

            $sql1 .= " ORDER BY tab.id ASC LIMIT {$start}, {$stop};";
        }

        $db->setQuery($sql);
        $db->execute();

        $number_list = $db->loadObject();
        $data['total'] = $number_list->total;

        $db->setQuery($sql1);
        $db->execute();

        $list = $db->loadAssocList();

        $data['text'] = '';

        if (count($list) > 0) {
            foreach ($list as $key => $value) {
                $ar_img = explode('/', $value[$name]);
                $ar_length = count($ar_img) - 1;

                $str_route_image = $drive . '/' . $ar_img[$ar_length];
                $str_route = str_replace('images/', '', $str_route_image);

                if (file_exists(JPATH_ROOT . '/' . $str_route_image)) {
                    $tex_trunc = JHtml::_('string.truncate', $db->escape($ar_img[$ar_length]), 10, false);

                    $data['text'] .= '<li class="imgOutline thumbnail height-80 width-80 center">';
                    $data['text'] .= '<a class="img-preview" title="' . $tex_trunc . '" href="javascript:ImageManager.populateFields(\'' . $str_route . '\')"
                    onClick="javascript:layoutMediaPitt(' . $key . ');" id="link_' . $key . '">';
                    $data['text'] .= '<div class="imgThumb">';
                    $data['text'] .= '<div class="imgThumbInside">';
                    $data['text'] .= '<img src="' . JUri::root() . $str_route_image . '" width="100%" alt="' . $tex_trunc . '" 
                style="vertical-align: middle; max-width: 100%; height: auto;">';
                    $data['text'] .= '</div>';
                    $data['text'] .= '</div>';
                    $data['text'] .= '<div class="imgDetails small">' . $tex_trunc . '</div>';
                    $data['text'] .= '</a>';
                    $data['text'] .= '</li>';
                }
            }

            echo json_encode($data);
        } else {
            echo '0';
        }

        $app->close();
    }
}