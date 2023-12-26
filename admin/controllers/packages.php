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

/**
 * Packages list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.0
 */
class FabrikAdminControllerPackages extends FabControllerAdmin
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     */
    protected $text_prefix = 'COM_FABRIK_PACKAGES';

    /**
     * View item name
     *
     * @var string
     */
    protected $view_item = 'packages';

    /**
     * Proxy for getModel.
     *
     * @param   string $name model name
     * @param   string $prefix model prefix
     *
     * @return  J model
     */
    public function &getModel($name = 'Package', $prefix = 'FabrikAdminModel')
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }

    /**
     * Method that will upload files into a folder on the server, named packagesupload.
     *
     * @throws Exception
     */
    public function uploadFile()
    {
        $app = JFactory::getApplication();

        $folder_path = pathinfo($_SERVER['SCRIPT_FILENAME']);

        $folder = $folder_path['dirname'] . '/components/com_fabrik/packagesupload';

        if (!is_dir($folder)) {
            mkdir($folder, 0775, true);
        }

        if (isset($_FILES['fileUpload']) && !empty($_FILES['fileUpload']['name'])) {
            $data = $_FILES["fileUpload"];

            foreach ($data["name"] as $key => $value) {
                if (!move_uploaded_file($data['tmp_name'][$key], $folder . '/' . $value)) {
                    $app->enqueueMessage(FText::_('COM_FABRIK_PACKAGES_UPLOAD_ERROR') . ' - ' . $value, 'error');
                } else {
                    $app->enqueueMessage(FText::_('COM_FABRIK_PACKAGES_UPLOAD_SUCCESS') . ' - ' . $value, 'message');
                }
            }
        } else {
            $app->enqueueMessage(FText::_('COM_FABRIK_PACKAGES_UPLOAD_FILE_ERROR'), 'error');
        }

        $this->setRedirect(JRoute::_('index.php?option=com_fabrik&view=packages', false)); //JUri::base() .
    }

    /**
     * Ajax method that will delete the file from the packagesupload folder.
     *
     * @throws Exception
     */
    public function deleteFile()
    {
        $app = JFactory::getApplication();

        $file = $app->input->getString('name');

        $folder_path = pathinfo($_SERVER['SCRIPT_FILENAME']);

        $path = $folder_path['dirname'] . '/components/com_fabrik/packagesupload/' . $file;

        if (unlink($path)) {
            echo '1';
        } else {
            echo '0';
        }

        $app->close();
    }

    /**
     * Management method for creating package files (zip, php, xml and sql).
     *
     * @throws Exception
     */
    public function generatePackage()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $app = JFactory::getApplication();

        $nm_text = str_replace(' ', '', strtolower($app->input->getString('name', 'pitt')));
        $data['name'] = $this->utf8_strtr($nm_text);

        $data['record'] = $app->input->getInt('recordDB');
        $data['file'] = $app->input->get('file', null, 'ARRAY');

        $folder_path = pathinfo($_SERVER['SCRIPT_FILENAME']);
        $folder = $folder_path['dirname'] . '/components/com_fabrik/generatepackages';
        $nm_package = 'pkg_' . $data['name'] . '-' . date("Y-m-d_H-i-s");
        $folder_package = $folder_path['dirname'] . '/components/com_fabrik/generatepackages/' . $nm_package;
        $folder_file = $folder_path['dirname'] . '/components/com_fabrik/packagesupload';

        if (!is_dir($folder)) {
            mkdir($folder, 0775, true);
        }

        if (count($data['file']) >= 2) {
            $zip = new ZipArchive;

            $zip->open($folder_package . '.zip', ZipArchive::CREATE);

            foreach ($data['file'] as $value) {
                $zip->addFile($folder_file . '/' . $value, $value);
                $files['files'][] = $value;
            }

            $file_php = $this->createFileScriptPhp($data['name'], $folder);

            $nm_sql = 'install.mysql.utf8.sql';
            $nm_sql_list = 'install.mysql1.utf8.sql';

            $this->createFileSqlDefault($nm_sql, $folder);

            $this->createFileSqlListJoin($nm_sql_list, $data['record'], $folder);

            $this->createXML($data, $folder);

            $zip->addFile($file_php, 'pkg_' . $data['name'] . '.php');
            $zip->addFile($folder . '/' . $nm_sql, $nm_sql);
            $zip->addFile($folder . '/' . $nm_sql_list, $nm_sql_list);
            $zip->addFile($folder . '/pkg_' . $data['name'] . '.xml', 'pkg_' . $data['name'] . '.xml');

            $zip->close();

            unlink($file_php);
            unlink($folder . '/' . $nm_sql);
            unlink($folder . '/' . $nm_sql_list);
            unlink($folder . '/pkg_' . $data['name'] . '.xml');

            $this->insertPackagesDB($data['name'], $nm_package . '.zip', $data['record'], $files);

            $app->enqueueMessage(FText::_('COM_FABRIK_PACKAGES_CONTROLLER_GENERATEPACKATE_SUCCESS') . ' - ' . $nm_package, 'message');
        } else {
            $app->enqueueMessage(FText::_('COM_FABRIK_PACKAGES_CONTROLLER_GENERATEPACKATE_ERROR_0'), 'error');
        }

        $this->setRedirect(JRoute::_('index.php?option=com_fabrik&view=packages', false));
    }

    /**
     * Method that removes all special characters and accentuation.
     *
     * @param $str
     * @return mixed
     */
    public function utf8_strtr($str) {
        $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú',
            'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú', '/',
            '-', '_', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
        $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u',
            'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U', '',
            '', '', '', '', '', '', '', '', '', '', '', '');

        return str_replace($comAcentos, $semAcentos, $str);
    }

    /**
     * Method for creating the joomla package installation configuration xml file.
     *
     * @param $data
     * @param $folder
     */
    private function createXML($data, $folder)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $user = JFactory::getUser();
        $xml = new DOMDocument("1.0", "UTF-8");

        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;

        $extension = $xml->createElement('extension');

        $ar_att = array('version' => '3.9', 'type' => 'package', 'method' => 'upgrade');
        $ar_type = array('com' => 'component', 'mod' => 'module', 'plg' => 'plugin');

        foreach ($ar_att as $key => $value) {
            $extension_att = $xml->createAttribute($key);
            $extension_att->value = $value;
            $extension->appendChild($extension_att);
        }

        $extension->appendChild($xml->createElement('name', ucfirst($data['name'] . FText::_('COM_FABRIK_PACKAGES_XML_NAME'))));
        $extension->appendChild($xml->createElement('author', $user->name));
        $extension->appendChild($xml->createElement('creationDate', date('Y-m-d')));
        $extension->appendChild($xml->createElement('packagename', $data['name']));
        $extension->appendChild($xml->createElement('version', '3.9'));
        $extension->appendChild($xml->createElement('url', FText::_('COM_FABRIK_PACKAGES_XML_URL')));
        $extension->appendChild($xml->createElement('packager', FText::_('COM_FABRIK_PACKAGES_XML_PACKAGER')));
        $extension->appendChild($xml->createElement('packagerurl', FText::_('COM_FABRIK_PACKAGES_XML_PACKAGER_URL')));
        $extension->appendChild($xml->createElement('copyright', FText::_('COM_FABRIK_PACKAGES_XML_COPYRIGHT')));
        $extension->appendChild($xml->createElement('license', FText::_('COM_FABRIK_PACKAGES_XML_LICENSE')));
        $extension->appendChild($xml->createElement('description', FText::_('COM_FABRIK_PACKAGES_XML_DESCRIPTION')));

        $files = $xml->createElement('files');

        foreach ($data['file'] as $value) {
            $ar_file = explode('_', $value);

            $file = $xml->createElement('file', $value);

            $file_type = $xml->createAttribute('type');
            $file_type->value = $ar_type[$ar_file[0]];
            $file->appendChild($file_type);

            $file_id = $xml->createAttribute('id');

            if ($ar_file[0] === 'plg') {
                if ($ar_file[2] === 'system') {
                    $file_group = $xml->createAttribute('group');
                    $file_group->value = 'system';
                    $file->appendChild($file_group);

                    $file_id->value = $ar_file[1];
                } elseif ($ar_file[2] === 'schedule') {
                    $file_group = $xml->createAttribute('group');
                    $file_group->value = 'system';
                    $file->appendChild($file_group);

                    $file_id->value = 'fabrikcron';
                } elseif ($ar_file[2] === 'content') {
                    $file_group = $xml->createAttribute('group');
                    $file_group->value = 'content';
                    $file->appendChild($file_group);

                    $file_id->value = $ar_file[1];
                } else {
                    $file_group = $xml->createAttribute('group');
                    $file_group->value = $ar_file[1] . '_' . $ar_file[2];
                    $file->appendChild($file_group);

                    $file_id->value = $ar_file[3];
                }

            } elseif ($ar_file[0] === 'mod') {
                $file_group = $xml->createAttribute('client');

                if ($ar_file[2] === 'admin') {
                    $file_group->value = $ar_file[2];
                    $file_id->value = $ar_file[0] . '_' . $ar_file[1] . '_' . $ar_file[3];
                } else {
                    $file_group->value = 'site';
                    $file_id->value = $ar_file[0] . '_' . $ar_file[1] . '_' . $ar_file[2];
                }

                $file->appendChild($file_group);
            } elseif ($ar_file[0] === 'com') {
                $file_id->value = $ar_file[0] . '_' . $ar_file[1];
            }

            $file->appendChild($file_id);

            $files->appendChild($file);
        }

        $extension->appendChild($files);
        $install = $xml->createElement('install');
        $sql = $xml->createElement('sql');

        $sqli_file = $xml->createElement('file', 'install.mysql.utf8.sql');
        $sqli_file1 = $xml->createAttribute('charset');
        $sqli_file1->value = 'utf8';
        $sqli_file2 = $xml->createAttribute('driver');
        $sqli_file2->value = 'mysqli';
        $sqli_file->appendChild($sqli_file1);
        $sqli_file->appendChild($sqli_file2);

        $sql_file = $xml->createElement('file', 'install.mysql1.utf8.sql');
        $sql_file1 = $xml->createAttribute('charset');
        $sql_file1->value = 'utf8';
        $sql_file2 = $xml->createAttribute('driver');
        $sql_file2->value = 'mysqli';
        $sql_file->appendChild($sql_file1);
        $sql_file->appendChild($sql_file2);

        $sql->appendChild($sqli_file);
        $sql->appendChild($sql_file);

        $install->appendChild($sql);
        $extension->appendChild($install);
        $extension->appendChild($xml->createElement('scriptfile', 'pkg_' . $data['name'] . '.php'));

        $xml->appendChild($extension);
        $xml->save($folder . '/pkg_' . $data['name'] . '.xml');
    }

    /**
     * Method that enters package information in the database
     *
     * @param $name
     * @param $file
     * @param $record
     * @param $params
     */
    private function insertPackagesDB($name, $file, $record, $params)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $db = JFactory::getDbo();
        $user = JFactory::getUser();

        $columns = array('name', 'file', 'record', 'date_time', 'users_id', 'params');
        $values = array($db->quote($name), $db->quote($file), $record, $db->quote(date("Y-m-d H:i:s")),
            $user->id, $db->quote(json_encode($params)));

        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__fabrik_pkgs'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Ajax method that will delete the file from the generatepackage folder and the fabrik_pkgs database information.
     *
     * @throws Exception
     */
    public function deletePackage()
    {
        $folder_path = pathinfo($_SERVER['SCRIPT_FILENAME']);
        $folder = $folder_path['dirname'] . '/components/com_fabrik/generatepackages';

        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $id = $app->input->getInt('id');
        $file = $app->input->getString('file');

        try {
            $db->transactionStart();

            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__fabrik_pkgs'))
                ->where($db->quoteName('id') . ' = ' . $id);

            $db->setQuery($query);
            $db->execute();

            $db->transactionCommit();

            unlink($folder . '/' . $file);

            echo '1';
        } catch (Exception $exc) {
            $db->transactionRollback();

            echo '0';
        }

        $app->close();
    }

    /**
     * Method of creating SQL file with all structures and information from the Factory defalult database and changing table names to the default
     * joomla that is dbprefix extensions.
     *
     * @param $nm_sql
     * @param $folder
     */
    private function createFileSqlDefault($nm_sql, $folder)
    {
        $mysql_paht = $_SERVER['MYSQL_HOME'];
        $config = JFactory::getConfig();

        $user = $config->get('user');
        $pass = (string)$config->get('password');
        $host = $config->get('host');
        $database = $config->get('db');
        $dbprefix = $config->get('dbprefix');

        $dir = $folder . '/' . $nm_sql;

        $table = $this->tableBDFabrikDefault();

        if($mysql_paht === NULL){
            exec("mysqldump --user={$user} --password={$pass} --host={$host} {$database} {$table} --skip-comments --result-file={$dir} 2>&1", $output);
        }else{
            exec("{$mysql_paht}/mysqldump --user={$user} --password={$pass} --host={$host} {$database} {$table} --skip-comments --result-file={$dir} 2>&1", $output);
        }

        $text_file = file_get_contents($dir);

        $copy_text = str_replace($dbprefix, "#__", $text_file);

        $file = fopen($dir, 'w');
        fwrite($file, $copy_text);
        fclose($file);
    }

    /**
     * Method to take the names of Fabrik's default tables and string them so you can create the SQL file of those tables.
     *
     * @return string
     */
    private function tableBDFabrikDefault()
    {
        $db = JFactory::getDbo();
        $config = JFactory::getConfig();

        $dbprefix = $config->get('dbprefix');
        $database = $config->get('db');

        $sql_show = "SHOW TABLES FROM {$database} LIKE '%fabrik%'";

        $db->setQuery($sql_show);

        $ar_show = $db->loadRowList();

        $text = '';

        foreach ($ar_show as $key => $value) {
            if (($value[0] !== $dbprefix . 'fabrik_pkgs') && ($value[0] !== $dbprefix . 'fabrik_connections')) {
                if ($key === 0) {
                    $text .= $value[0];
                } else {
                    $text .= ' ' . $value[0];
                }
            }
        }

        return $text;
    }

    /**
     * Method for creating SQL file from Fabrik's lists and join, depending on the user's choice if it will be only with structures or
     * structures / information.
     *
     * @param $nm_sql
     * @param $record
     * @param $folder
     */
    private function createFileSqlListJoin($nm_sql, $record, $folder)
    {
        $mysql_paht = $_SERVER['MYSQL_HOME'];
        $config = JFactory::getConfig();

        $user = $config->get('user');
        $pass = $config->get('password');
        $host = $config->get('host');
        $database = $config->get('db');
        $dbprefix = $config->get('dbprefix');

        $dir = $folder . '/' . $nm_sql;

        $table = $this->tableBDFabrikListJoin();

        if($mysql_paht === NULL){
            if ($record === 1) {
                exec("mysqldump --user={$user} --password={$pass} --host={$host} {$database} {$table} --skip-comments --result-file={$dir} 2>&1", $output);
            } else {
                exec("mysqldump --user={$user} --password={$pass} --host={$host} {$database} {$table} -d --skip-comments --result-file={$dir} 2>&1", $output);
            }
        }else{
            if ($record === 1) {
                exec("{$mysql_paht}/mysqldump --user={$user} --password={$pass} --host={$host} {$database} {$table} --skip-comments --result-file={$dir} 2>&1", $output);
            } else {
                exec("{$mysql_paht}/mysqldump --user={$user} --password={$pass} --host={$host} {$database} {$table} -d --skip-comments --result-file={$dir} 2>&1", $output);
            }
        }

        $text_file = file_get_contents($dir);

        $copy_text = str_replace($dbprefix, "#__", $text_file);

        $file = fopen($dir, 'w');
        fwrite($file, $copy_text);
        fclose($file);
    }

    /**
     * Method for taking Fabrik list and join names and sequencing them so you can create the SQL file for those tables.
     *
     * @return string
     */
    private function tableBDFabrikListJoin()
    {
        $db = JFactory::getDbo();

        $sql = "SELECT DISTINCT list.db_table_name
                FROM
                #__fabrik_lists AS list ;";

        $db->setQuery($sql);

        $result = $db->loadObjectList();

        $text = '';
        $arTable = '';

        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                if ($key == 0) {
                    $text .= $value->db_table_name;
                    $arTable .= "'{$value->db_table_name}'";
                } else {
                    $text .= ' ' . $value->db_table_name;
                    $arTable .= ", '{$value->db_table_name}'";
                }
            }

            $sql1 = "SELECT DISTINCT joins.table_join
                    FROM
                    #__fabrik_joins AS joins
                    WHERE
                    joins.list_id <> 0 AND
                    joins.list_id IS NOT NULL AND
                    joins.table_join NOT IN ({$arTable}) ;";

            $db->setQuery($sql1);

            $result1 = $db->loadObjectList();

            if (count($result1) > 0) {
                foreach ($result1 as $value1) {
                    $text .= ' ' . $value1->table_join;
                }
            }
        }

        return $text;
    }

    /**
     * Method for creating extra script file with business rules to execute when the package is installed in joomla.
     *
     * @param $name
     * @param $folder
     * @return string
     */
    private function createFileScriptPhp($name, $folder)
    {
        $file_php = 'pkg_' . $name . '.php';

        $nm_file = ucfirst($name);

        $script_php = "<?php \n";

        $script_php .= "defined('_JEXEC') or die(); \n\n";

        $script_php .= "class Pkg_{$nm_file}InstallerScript { \n";

        $script_php .= "protected \$name = '{$name}'; \n";
        $script_php .= "protected \$packageName = 'pkg_{$name}'; \n";
        $script_php .= "protected \$componentName = 'com_fabrik'; \n";
        $script_php .= "protected \$minimumPHPVersion = '5.6.0'; \n";
        $script_php .= "protected \$minimumJoomlaVersion = '3.8.0'; \n";
        $script_php .= "protected \$maximumJoomlaVersion = '3.9.99'; \n\n";

        $script_php .= "public function preflight(\$type, \$parent){ \n";
        $script_php .= "\$sourcePath = \$parent->getParent()->getPath('source'); \n\n";
        $script_php .= "\$folder_path = pathinfo(\$_SERVER['SCRIPT_FILENAME']); \n";
        $script_php .= "\$folder = \$folder_path['dirname'] . '/manifests/packages/' . \$this->name; \n\n";
        $script_php .= "if (!is_dir(\$folder)) { \n";
        $script_php .= "mkdir(\$folder, 0775, true); \n";
        $script_php .= "} \n\n";
        $script_php .= "copy(\$sourcePath. '/install.mysql.utf8.sql', \$folder . '/install.mysql.utf8.sql'); \n\n";
        $script_php .= "copy(\$sourcePath. '/install.mysql1.utf8.sql', \$folder . '/install.mysql1.utf8.sql'); \n\n";
        $script_php .= "return true; \n";
        $script_php .= "} \n\n";

        $script_php .= "public function postflight(\$type, \$parent){ \n";
        $script_php .= "\$db = JFactory::getDbo(); \n";
        $script_php .= "\$query = \$db->getQuery(true); \n\n";
        $script_php .= "\$query->clear(); \n";
        $script_php .= "\$query->update('#__extensions')->set('enabled = 1') \n";
        $script_php .= "->where('type = ' . \$db->q('plugin') . ' AND (folder LIKE ' . \$db->q('fabrik_%'), 'OR') \n";
        $script_php .= "->where('(folder=' . \$db->q('system') . ' AND element = ' . \$db->q('fabrik') . ')', 'OR') \n";
        $script_php .= "->where('(folder=' . \$db->q('system') . ' AND element LIKE ' . \$db->q('fabrik%') . ')', 'OR') \n";
        $script_php .= "->where('(folder=' . \$db->q('content') . ' AND element = ' . \$db->q('fabrik') . '))', 'OR'); \n";
        $script_php .= "\$db->setQuery(\$query)->execute(); \n\n";
        $script_php .= "\$folder_path = pathinfo(\$_SERVER['SCRIPT_FILENAME']); \n";
        $script_php .= "\$folder = \$folder_path['dirname'] . '/manifests/packages/' . \$this->name; \n\n";
        $script_php .= "\$folder_pack = \$folder_path['dirname'] . '/manifests/packages/'; \n\n";
        $script_php .= "\$files = array_diff(scandir(\$folder), array('.','..')); \n\n";
        $script_php .= "foreach (\$files as \$file) { \n";
        $script_php .= "(is_dir(\$folder . '/' . \$file)) ? delTree(\$folder . '/' . \$file) : unlink(\$folder . '/' . \$file); \n";
        $script_php .= "} \n\n";
        $script_php .= "unlink(\$folder_pack . \$this->packageName . '.php'); \n\n";
        $script_php .= "rmdir(\$folder); \n\n";
        $script_php .= "return true; \n";
        $script_php .= "} \n\n";

        $script_php .= "public function install(\$parent){ return true;} \n\n";

        $script_php .= "public function uninstall(\$parent){ return true;} \n";

        $script_php .= '}';

        $arquivo = fopen($folder . '/' . $file_php, 'w');
        fwrite($arquivo, $script_php);
        fclose($arquivo);

        return $folder . '/' . $file_php;
    }
}