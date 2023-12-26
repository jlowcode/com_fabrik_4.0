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

use Joomla\CMS\Component\ComponentHelper;

/**
 * Packages list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @since       3.0
 */
class FabrikAdminControllerAdministrativetools extends FabControllerAdmin {

    protected $folder = '';

    /**
     * Proxy for getModel.
     *
     * @param string $name model name
     * @param string $prefix model prefix
     *
     * @return  J model
     */
    public function &getModel($name = 'Administrativetools', $prefix = 'FabrikAdminModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }

    /**
     * ListElement method responsible for listing all list elements that have been chosen in the template.
     *
     * @throws Exception
     * @since version
     */
    public function listElement() {
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
                list.published = 1 AND
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
     * Method that will perform the transformation of elements for each business rule.
     *
     * @since version
     */
    public function rumTransformationTool() {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $id_form = $app->input->getInt("listTrans", 0);
        $id_source = $app->input->getInt("elementSourceTrans", 0);
        $id_target = $app->input->getInt("elementDestTrans", 0);
        $id_type = $app->input->getInt("typeTrans", 0);
        $delimiter = $app->input->getString("delimiterTransf", null);

        $joinModelSource = JModelLegacy::getInstance('Join', 'FabrikFEModel');
        $joinModelTarget = JModelLegacy::getInstance('Join', 'FabrikFEModel');

        $tableSource = $this->tableSource($id_form);

        $data_source = $this->elementSourceTarget($id_form, $id_source, $id_target);

        foreach ($data_source as $value) {
            if ($value->id == $id_source) {
                $source['data'] = $value;
                $source['params'] = json_decode($value->params);
            } elseif ($value->id == $id_target) {
                $target['data'] = $value;
                $target['params'] = json_decode($value->params);
            }
        }

        $ar_danial = array(5 => 5, 6 => 6);

        if (!array_key_exists($id_type, $ar_danial)) {
            $data_tabela = $this->dataTableSource($tableSource->db_table_name, $source['data']->name, $target['data']->name);
        }

        switch ($id_type) {
            case 1:
                $tableTaget = $this->tableTaget($target['params']->join_db_name);

                $data_target = $this->elementTableTarget($target['params']->join_db_name, $target['params']->join_val_col_synchronism, $tableTaget->id);
                $param_elem_tb_target = json_decode($data_target->params);

                if (($target['data']->plugin === 'databasejoin') && (strlen($target['params']->join_val_col_synchronism) === 0) && ($target['params']->database_join_display_type === 'dropdown')) {
                    if (count($data_tabela) !== 0) {
                        foreach ($data_tabela as $value) {
                            $exist_data_target = $this->existTargetTableData($target['params']->join_db_name, $target['params']->join_val_column, $value[$source['data']->name]);

                            if (count($exist_data_target) === 0) {
                                $result_sql = $this->insertIntoTargetAndChargeSourceTable($target['params']->join_db_name, $target['params']->join_val_column, $value[$source['data']->name], $tableSource->db_table_name, $value['id'], $target['data']->name);
                            } else {
                                if ($exist_data_target[$target['params']->join_key_column] !== $value[$target['data']->name]) {
                                    $result_sql = $this->updateDataTableSource($tableSource->db_table_name, $exist_data_target[$target['params']->join_key_column], $value['id'], $target['data']->name);
                                }
                            }
                        }
                    }
                } elseif (($target['data']->plugin === 'databasejoin') && (strlen($target['params']->join_val_col_synchronism) === 0) &&
                        (($target['params']->database_join_display_type === 'multilist') || ($target['params']->database_join_display_type === 'checkbox'))) {
                    $this->join_source = $joinModelSource->getJoinFromKey('element_id', $target['data']->id);

                    if (count($data_tabela) !== 0) {
                        foreach ($data_tabela as $value) {
                            $exist_data_target = $this->existTargetTableData($target['params']->join_db_name, $target['params']->join_val_column, $value[$source['data']->name]);

                            if (count($exist_data_target) === 0) {
                                $result_sql = $this->insertInTargetAndSourceTable($target['params']->join_db_name, $target['params']->join_val_column, $value[$source['data']->name], $this->join_source, $value['id']);
                            } else {
                                $result_sql = $this->insertMultipleSourceTable($this->join_source, $value['id'], $exist_data_target[$target['params']->join_key_column]);
                            }
                        }
                    }
                } elseif (($target['data']->plugin === 'databasejoin') && (strlen($target['params']->join_val_col_synchronism) !== 0) &&
                        (($target['params']->database_join_display_type === 'multilist') || ($target['params']->database_join_display_type === 'checkbox')) &&
                        ($param_elem_tb_target->database_join_display_type === 'dropdown')) {
                    $this->join_source = $joinModelSource->getJoinFromKey('element_id', $target['data']->id);

                    if (count($data_tabela) !== 0) {
                        foreach ($data_tabela as $value) {
                            $exist_data_target = $this->existTargetTableData($target['params']->join_db_name, $target['params']->join_val_column, $value[$source['data']->name]);

                            if (count($exist_data_target) === 0) {
                                $result_sql = $this->insertInTargetDropAndSourceMultTable($target['params']->join_db_name, $target['params']->join_val_column, $value[$source['data']->name], $this->join_source, $value['id'], $target['params']->join_val_col_synchronism);
                            } else {
                                $result_sql = $this->insertMultipleSourceTable($this->join_source, $value['id'], $exist_data_target[$target['params']->join_key_column]);
                            }
                        }
                    }
                } elseif (($target['data']->plugin === 'databasejoin') && (strlen($target['params']->join_val_col_synchronism) !== 0) && ($target['params']->database_join_display_type === 'dropdown') &&
                        (($param_elem_tb_target->database_join_display_type === 'multilist') || ($param_elem_tb_target->database_join_display_type === 'checkbox'))) {
                    $this->join_target = $joinModelTarget->getJoinFromKey('element_id', $data_target->id);

                    if (count($data_tabela) !== 0) {
                        foreach ($data_tabela as $value) {
                            $exist_data_target = $this->existTargetTableData($target['params']->join_db_name, $target['params']->join_val_column, $value[$source['data']->name]);

                            if (count($exist_data_target) === 0) {
                                $result_sql = $this->insertTargetChangesSourceInsertTargetRepeatTable($target['params']->join_db_name, $target['params']->join_val_column, $value[$source['data']->name], $tableSource->db_table_name, $value['id'], $target['data']->name, $this->join_target);
                            } else {
                                $result_sql = $this->updateTableSourceDropdownInsertTableTargetRepeat($tableSource->db_table_name, $value['id'], $target['data']->name, $this->join_target, $exist_data_target[$target['params']->join_key_column]);
                            }
                        }
                    }
                } elseif (($target['data']->plugin === 'databasejoin') && (strlen($target['params']->join_val_col_synchronism) !== 0) &&
                        (($target['params']->database_join_display_type === 'multilist') || ($target['params']->database_join_display_type === 'checkbox')) &&
                        (($param_elem_tb_target->database_join_display_type === 'multilist') || ($param_elem_tb_target->database_join_display_type === 'checkbox'))) {
                    $this->join_source = $joinModelSource->getJoinFromKey('element_id', $target['data']->id);

                    $this->join_target = $joinModelTarget->getJoinFromKey('element_id', $data_target->id);

                    if (count($data_tabela) !== 0) {
                        foreach ($data_tabela as $value) {
                            $exist_data_target = $this->existTargetTableData($target['params']->join_db_name, $target['params']->join_val_column, $value[$source['data']->name]);

                            if (count($exist_data_target) === 0) {
                                $result_sql = $this->insertTargetTableInsertRepeatTargetTableInsertRepeatSourceTable($target['params']->join_db_name, $target['params']->join_val_column, $value[$source['data']->name], $this->join_source, $value['id'], $this->join_target);
                            } else {
                                $result_sql = $this->insertSourceRepeatTableInsertTargetRepeatTable($this->join_source, $value['id'], $exist_data_target[$target['params']->join_key_column], $this->join_target);
                            }
                        }
                    }
                }
                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_0', 'COM_FABRIK_MESSAGE_CONTROLLER_1');

                break;
            case 2:
                $sub_opt_source = $target['params']->sub_options;

                if ($target['data']->plugin === 'dropdown') {
                    if (count($data_tabela) !== 0) {
                        foreach ($data_tabela as $value) {
                            $ar_string = explode($delimiter, $value[$source['data']->name]);

                            $text_dropdown = '';

                            $total = count($ar_string) - 1;

                            foreach ($ar_string as $key1 => $ar_value) {
                                if (!(in_array($ar_value, $sub_opt_source->sub_values)) && !(in_array($ar_value, $sub_opt_source->sub_labels))) {
                                    $sub_opt_source->sub_values[] = ucwords(strtolower($ar_value));

                                    $sub_opt_source->sub_labels[] = ucwords(strtolower($ar_value));
                                }

                                if (($total >= 1) && ($key1 < $total)) {
                                    $text_dropdown .= ucwords(strtolower($ar_value)) . "\",\"";
                                } elseif ($total === $key1) {
                                    $text_dropdown .= ucwords(strtolower($ar_value));
                                } elseif ($total == 0) {
                                    $text_dropdown = ucwords(strtolower($ar_value));
                                }
                            }

                            $num_exp = explode('","', $text_dropdown);

                            if (count($num_exp) !== 1) {
                                $result_text = "[\"" . $text_dropdown . "\"]";
                            } else {
                                $result_text = $text_dropdown;
                            }
                            $result_sql = $this->updateDataTableSourceUpdateTableElement($tableSource->db_table_name, $target['data']->name, $value['id'], $target['data']->id, $result_text, $target['params']);
                        }
                    }
                }
                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_0', 'COM_FABRIK_MESSAGE_CONTROLLER_1');

                break;
            case 3:
                $tableTaget = $this->tableTaget($target['params']->join_db_name);

                $data_target = $this->elementTableTarget($target['params']->join_db_name, $target['params']->join_val_col_synchronism, $tableTaget->id);

                $param_elem_tb_target = json_decode($data_target->params);

                if (($target['data']->plugin === 'databasejoin') && (strlen($target['params']->join_val_col_synchronism) === 0) &&
                        (($target['params']->database_join_display_type === 'multilist') || ($target['params']->database_join_display_type === 'checkbox'))) {
                    $this->join_source = $joinModelSource->getJoinFromKey('element_id', $target['data']->id);

                    if (count($data_tabela) !== 0) {
                        foreach ($data_tabela as $value) {
                            $ar_exp = explode($delimiter, $value[$source['data']->name]);

                            foreach ($ar_exp as $ar_value) {
                                $exist_data_target = $this->existTargetTableData($target['params']->join_db_name, $target['params']->join_val_column, $ar_value);

                                if (count($exist_data_target) === 0) {
                                    $result_sql = $this->insertInTargetAndSourceTable($target['params']->join_db_name, $target['params']->join_val_column, $ar_value, $this->join_source, $value['id']);
                                } else {
                                    $result_sql = $this->insertMultipleSourceTable($this->join_source, $value['id'], $exist_data_target[$target['params']->join_key_column]);
                                }
                            }
                        }
                    }
                } elseif (($target['data']->plugin === 'databasejoin') && (strlen($target['params']->join_val_col_synchronism) !== 0) &&
                        (($target['params']->database_join_display_type === 'multilist') || ($target['params']->database_join_display_type === 'checkbox')) &&
                        (($param_elem_tb_target->database_join_display_type === 'multilist') || ($param_elem_tb_target->database_join_display_type === 'checkbox'))) {
                    $this->join_source = $joinModelSource->getJoinFromKey('element_id', $target['data']->id);

                    $this->join_target = $joinModelTarget->getJoinFromKey('element_id', $data_target->id);

                    if (count($data_tabela) !== 0) {
                        foreach ($data_tabela as $value) {
                            $ar_exp = explode($delimiter, $value[$source['data']->name]);

                            foreach ($ar_exp as $ar_value) {
                                $exist_data_target = $this->existTargetTableData($target['params']->join_db_name, $target['params']->join_val_column, $ar_value);

                                if (count($exist_data_target) === 0) {
                                    $result_sql = $this->insertTargetTableInsertRepeatTargetTableInsertRepeatSourceTable($target['params']->join_db_name, $target['params']->join_val_column, $ar_value, $this->join_source, $value['id'], $this->join_target);
                                } else {
                                    $result_sql = $this->insertSourceRepeatTableInsertTargetRepeatTable($this->join_source, $value['id'], $exist_data_target[$target['params']->join_key_column], $this->join_target);
                                }
                            }
                        }
                    }
                }

                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_0', 'COM_FABRIK_MESSAGE_CONTROLLER_1');

                break;
            case 4:
                $tableTaget = $this->tableTaget($target['params']->join_db_name);

                $data_target = $this->elementTableTarget($target['params']->join_db_name, $target['params']->join_val_col_synchronism, $tableTaget->id);

                $param_elem_tb_target = json_decode($data_target->params);

                $ar_simbol = Array('["', '"]');

                if (($target['data']->plugin === 'databasejoin') && (strlen($target['params']->join_val_col_synchronism) === 0) &&
                        (($target['params']->database_join_display_type === 'multilist') || ($target['params']->database_join_display_type === 'checkbox'))) {
                    $this->join_source = $joinModelSource->getJoinFromKey('element_id', $target['data']->id);

                    if (count($data_tabela) !== 0) {
                        foreach ($data_tabela as $value) {
                            $ar_exp = explode('","', str_replace($ar_simbol, "", $value[$source['data']->name]));

                            foreach ($ar_exp as $ar_value) {
                                $exist_data_target = $this->existTargetTableData($target['params']->join_db_name, $target['params']->join_val_column, $ar_value);

                                if (count($exist_data_target) === 0) {
                                    $result_sql = $this->insertInTargetAndSourceTable($target['params']->join_db_name, $target['params']->join_val_column, $ar_value, $this->join_source, $value['id']);
                                } else {
                                    $result_sql = $this->insertMultipleSourceTable($this->join_source, $value['id'], $exist_data_target[$target['params']->join_key_column]);
                                }
                            }
                        }
                    }
                } elseif (($target['data']->plugin === 'databasejoin') && (strlen($target['params']->join_val_col_synchronism) !== 0) &&
                        (($target['params']->database_join_display_type === 'multilist') || ($target['params']->database_join_display_type === 'checkbox')) &&
                        (($param_elem_tb_target->database_join_display_type === 'multilist') || ($param_elem_tb_target->database_join_display_type === 'checkbox'))) {
                    $this->join_source = $joinModelSource->getJoinFromKey('element_id', $target['data']->id);

                    $this->join_target = $joinModelTarget->getJoinFromKey('element_id', $data_target->id);

                    if (count($data_tabela) !== 0) {
                        foreach ($data_tabela as $value) {
                            $ar_exp = explode('","', str_replace($ar_simbol, "", $value[$source['data']->name]));

                            foreach ($ar_exp as $ar_value) {
                                $exist_data_target = $this->existTargetTableData($target['params']->join_db_name, $target['params']->join_val_column, $ar_value);

                                if (count($exist_data_target) === 0) {
                                    $result_sql = $this->insertTargetTableInsertRepeatTargetTableInsertRepeatSourceTable($target['params']->join_db_name, $target['params']->join_val_column, $ar_value, $this->join_source, $value['id'], $this->join_target);
                                } else {
                                    $result_sql = $this->insertSourceRepeatTableInsertTargetRepeatTable($this->join_source, $value['id'], $exist_data_target[$target['params']->join_key_column], $this->join_target);
                                }
                            }
                        }
                    }
                }
                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_0', 'COM_FABRIK_MESSAGE_CONTROLLER_1');

                break;
            case 5:
                $update = $app->input->getString("updateDB", 'SET NULL');

                $delete = $app->input->getString("deleteDB", 'SET NULL');

                $tableTaget = $this->tableTaget($source['params']->join_db_name);

                $data_target = $this->elementTableTarget($source['params']->join_db_name, $source['params']->join_val_col_synchronism, $tableTaget->id);

                $param_elem_tb_target = json_decode($data_target->params);

                if (($source['data']->plugin === 'databasejoin') && (strlen($source['params']->join_val_col_synchronism) === 0) &&
                        (($source['params']->database_join_display_type === 'multilist') || ($source['params']->database_join_display_type === 'checkbox'))) {
                    $engine_source_bool = $this->sourceTableStructure($tableSource->db_table_name);

                    $this->join_source = $joinModelSource->getJoinFromKey('element_id', $source['data']->id);

                    $engine_join_bool = $this->joinTableStructure($this->join_source->table_join);

                    $engine_target_bool = $this->targetTableStructure($source['params']->join_db_name);

                    if ($engine_source_bool && $engine_join_bool && $engine_target_bool) {
                        $join_type_bool = $this->estructureChecksEqualityBetweenFields($source['params']->join_db_name, $this->join_source->table_join, $this->join_source->table_key);

                        if ($join_type_bool) {
                            $fk_exist1 = $this->checkIfForeignKeyExists($this->join_source->table_join, $tableSource->db_table_name, $this->join_source->table_join_key);

                            $fk_exist2 = $this->checkIfForeignKeyExists($this->join_source->table_join, $source['params']->join_db_name, $this->join_source->table_key);

                            if (($fk_exist1->forkey === '0') && ($fk_exist2->forkey === '0')) {
                                $result_sql = $this->alterTableCreateForeignKeyRelatedFields2($this->join_source->table_join, $tableSource->db_table_name, $this->join_source->table_join_key, $source['params']->join_db_name, $this->join_source->table_key, $update, $delete);

                                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_2', 'COM_FABRIK_MESSAGE_CONTROLLER_1');
                            } else {
                                $result_sql = true;

                                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_3', 'COM_FABRIK_MESSAGE_CONTROLLER_1');
                            }
                        }
                    }
                } elseif (($source['data']->plugin === 'databasejoin') && (strlen($source['params']->join_val_col_synchronism) === 0) && ($source['params']->database_join_display_type === 'dropdown')) {
                    $engine_source_bool = $this->sourceTableStructure($tableSource->db_table_name);

                    $engine_target_bool = $this->targetTableStructure($source['params']->join_db_name);

                    if ($engine_source_bool && $engine_target_bool) {
                        $fk_exist = $this->checkIfForeignKeyExists($tableSource->db_table_name, $source['params']->join_db_name, $source['data']->name);

                        if ($fk_exist->forkey === '0') {
                            $result_sql = $this->alterTableCreateForeignKeyRelatedFields1($tableSource->db_table_name, $source['params']->join_db_name, $source['data']->name, $update, $delete);

                            $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_2', 'COM_FABRIK_MESSAGE_CONTROLLER_1');
                        } else {
                            $result_sql = true;

                            $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_3', 'COM_FABRIK_MESSAGE_CONTROLLER_1');
                        }
                    }
                } elseif (($source['data']->plugin === 'databasejoin') && (strlen($source['params']->join_val_col_synchronism) !== 0) && ($source['params']->database_join_display_type === 'dropdown') &&
                        (($param_elem_tb_target->database_join_display_type === 'multilist') || ($param_elem_tb_target->database_join_display_type === 'checkbox'))) {

                    $engine_source_bool = $this->sourceTableStructure($tableSource->db_table_name);

                    $engine_target_bool = $this->targetTableStructure($source['params']->join_db_name);

                    $this->join_target = $joinModelTarget->getJoinFromKey('element_id', $data_target->id);

                    $engine_join_bool = $this->joinTableStructure($this->join_target->table_join);

                    if ($engine_source_bool && $engine_target_bool && $engine_join_bool) {
                        $join_type_bool = $this->estructureChecksEqualityBetweenFields($tableSource->db_table_name, $this->join_target->table_join, $this->join_target->table_key);

                        $type_bool = $this->estructureChecksEqualityBetweenFields($source['params']->join_db_name, $tableSource->db_table_name, $source['data']->name);

                        if ($join_type_bool & $type_bool) {
                            $fk_exist = $this->checkIfForeignKeyExists($tableSource->db_table_name, $source['params']->join_db_name, $source['data']->name);

                            $fk_exist1 = $this->checkIfForeignKeyExists($this->join_target->table_join, $tableSource->db_table_name, $this->join_target->table_join_key);

                            $fk_exist2 = $this->checkIfForeignKeyExists($this->join_target->table_join, $source['params']->join_db_name, $this->join_target->table_key);

                            if (($fk_exist->forkey === '0') && ($fk_exist1->forkey === '0') && ($fk_exist2->forkey === '0')) {
                                $result_sql = $this->alterTableCreateForeignKeyRelatedFields3($this->join_target->table_join, $source['params']->join_db_name, $this->join_target->table_key, $this->join_target->table_join_key, $tableSource->db_table_name, $source['data']->name, $update, $delete);

                                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_2', 'COM_FABRIK_MESSAGE_CONTROLLER_1');
                            } else {
                                $result_sql = true;

                                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_3', 'COM_FABRIK_MESSAGE_CONTROLLER_1');
                            }
                        }
                    }
                } elseif (($source['data']->plugin === 'databasejoin') && (strlen($source['params']->join_val_col_synchronism) !== 0) &&
                        (($source['params']->database_join_display_type === 'multilist') || ($source['params']->database_join_display_type === 'checkbox')) &&
                        ($param_elem_tb_target->database_join_display_type === 'dropdown')) {
                    $engine_source_bool = $this->sourceTableStructure($tableSource->db_table_name);

                    $this->join_source = $joinModelSource->getJoinFromKey('element_id', $source['data']->id);

                    $engine_join_bool = $this->joinTableStructure($this->join_source->table_join);

                    $engine_target_bool = $this->targetTableStructure($source['params']->join_db_name);

                    if ($engine_source_bool && $engine_target_bool && $engine_join_bool) {
                        $join_type_bool = $this->estructureChecksEqualityBetweenFields($source['params']->join_db_name, $this->join_source->table_join, $this->join_source->table_key);

                        $type_bool = $this->estructureChecksEqualityBetweenFields($tableSource->db_table_name, $source['params']->join_db_name, $source['params']->join_val_col_synchronism);

                        if ($join_type_bool && $type_bool) {
                            $fk_exist1 = $this->checkIfForeignKeyExists($this->join_source->table_join, $tableSource->db_table_name, $this->join_source->table_join_key);

                            $fk_exist2 = $this->checkIfForeignKeyExists($this->join_source->table_join, $source['params']->join_db_name, $this->join_source->table_key);

                            $fk_exist3 = $this->checkIfForeignKeyExists($source['params']->join_db_name, $tableSource->db_table_name, $source['params']->join_val_col_synchronism);

                            if (($fk_exist1->forkey === '0') && ($fk_exist2->forkey === '0') && ($fk_exist3->forkey === '0')) {
                                $result_sql = $this->alterTableCreateForeignKeyRelatedFields3($this->join_source->table_join, $tableSource->db_table_name, $source['data']->name, $this->join_source->table_join_key, $source['params']->join_db_name, $source['params']->join_val_col_synchronism, $update, $delete);

                                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_2', 'COM_FABRIK_MESSAGE_CONTROLLER_1');
                            } else {
                                $result_sql = true;

                                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_3', 'COM_FABRIK_MESSAGE_CONTROLLER_1');
                            }
                        }
                    }
                } elseif (($source['data']->plugin === 'databasejoin') && (strlen($source['params']->join_val_col_synchronism) !== 0) &&
                        (($source['params']->database_join_display_type === 'multilist') || ($source['params']->database_join_display_type === 'checkbox')) &&
                        (($param_elem_tb_target->database_join_display_type === 'multilist') || ($param_elem_tb_target->database_join_display_type === 'checkbox'))) {
                    $engine_source_bool = $this->sourceTableStructure($tableSource->db_table_name);

                    $this->join_source = $joinModelSource->getJoinFromKey('element_id', $source['data']->id);

                    $engine_join_bool = $this->joinTableStructure($this->join_source->table_join);

                    $engine_target_bool = $this->targetTableStructure($source['params']->join_db_name);

                    $this->join_target = $joinModelTarget->getJoinFromKey('element_id', $data_target->id);

                    $engine_join_target_bool = $this->joinTableStructure($this->join_target->table_join);

                    if ($engine_source_bool && $engine_join_bool && $engine_target_bool && $engine_join_target_bool) {
                        $join_type_source_bool = $this->estructureChecksEqualityBetweenFields($tableSource->db_table_name, $this->join_source->table_join, $this->join_source->table_key);

                        $join_type_target_bool = $this->estructureChecksEqualityBetweenFields($source['params']->join_db_name, $this->join_target->table_join, $this->join_target->table_key);

                        if ($join_type_source_bool && $join_type_target_bool) {
                            $fk_exist1 = $this->checkIfForeignKeyExists($this->join_source->table_join, $tableSource->db_table_name, $this->join_source->table_join_key);

                            $fk_exist2 = $this->checkIfForeignKeyExists($this->join_source->table_join, $source['params']->join_db_name, $this->join_source->table_key);

                            $fk_exist3 = $this->checkIfForeignKeyExists($this->join_target->table_join, $tableSource->db_table_name, $this->join_target->table_join_key);

                            $fk_exist4 = $this->checkIfForeignKeyExists($this->join_target->table_join, $source['params']->join_db_name, $this->join_target->table_key);

                            if (($fk_exist1->forkey === '0') && ($fk_exist2->forkey === '0') && ($fk_exist3->forkey === '0') && ($fk_exist4->forkey === '0')) {
                                $result_sql = $this->alterTableCreateForeignKeyRelatedFields4($this->join_source->table_join, $this->join_target->table_join, $tableSource->db_table_name, $source['params']->join_db_name, $this->join_target->table_key, $this->join_source->table_key, $update, $delete);

                                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_2', 'COM_FABRIK_MESSAGE_CONTROLLER_1');
                            } else {
                                $result_sql = true;

                                $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_3', 'COM_FABRIK_MESSAGE_CONTROLLER_1');
                            }
                        }
                    }
                }
                break;
            case 6:
                $table_repeat = $app->input->getInt("tableRepeat", 0);

                $thumbs_crops = $app->input->getInt("thumbsCrops", 0);

                if (($table_repeat === 1) && ($thumbs_crops === 0)) {
                    if ($source['params']->ajax_upload === '1') {
                        $data_tabela = $this->dataTableSourceFieldSingle($tableSource->db_table_name, $source['data']->name);

                        $this->join_source = $joinModelSource->getJoinFromKey('element_id', $source['data']->id);

                        foreach ($data_tabela as $value) {
                            $text = str_replace('/', "\\", $value[$source['data']->name]);

                            $result_sql = $this->insertMultipleSourceTableFileupload($this->join_source, $value['id'], $text);

                            $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_6', 'COM_FABRIK_MESSAGE_CONTROLLER_7');
                        }
                    }
                } elseif (($table_repeat === 0) && ($thumbs_crops === 1)) {
                    if ($source['params']->ajax_upload === '0') {
                        $data_tabela = $this->dataTableSourceFieldSingle($tableSource->db_table_name, $source['data']->name);

                        foreach ($data_tabela as $value) {
                            $this->performChangeThumbsCrops($source['params'], $value[$source['data']->name]);
                        }
                    } elseif ($source['params']->ajax_upload === '1') {
                        $this->join_source = $joinModelSource->getJoinFromKey('element_id', $source['data']->id);

                        $data_tabela = $this->dataTableSourceFieldSingle($this->join_source->table_join, $this->join_source->table_key);

                        foreach ($data_tabela as $key => $value) {
                            $this->performChangeThumbsCrops($source['params'], $value[$source['data']->name], $key);
                        }
                    }
                    $message = $this->displayMessages(true, 'COM_FABRIK_MESSAGE_CONTROLLER_4', 'COM_FABRIK_MESSAGE_CONTROLLER_5');
                } elseif (($table_repeat === 1) && ($thumbs_crops === 1)) {
                    if ($source['params']->ajax_upload === '1') {
                        $data_tabela = $this->dataTableSourceFieldSingle($tableSource->db_table_name, $source['data']->name);

                        $this->join_source = $joinModelSource->getJoinFromKey('element_id', $source['data']->id);

                        foreach ($data_tabela as $value) {
                            $text = str_replace('/', "\\", $value[$source['data']->name]);

                            $result_sql = $this->insertMultipleSourceTableFileupload($this->join_source, $value['id'], $text);

                            $this->performChangeThumbsCrops($source['params'], $value[$source['data']->name]);

                            $message = $this->displayMessages($result_sql, 'COM_FABRIK_MESSAGE_CONTROLLER_8', 'COM_FABRIK_MESSAGE_CONTROLLER_9');
                        }
                    }
                }
                break;
        }
        $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools&tab=1';

        $this->setRedirect($site_message, $message['message'], $message['type']);
    }

    /**
     * Method that validates to show error alert when it has no element type
     *
     * @param $sourse
     * @param $target
     *
     * @return string
     *
     * @since version
     */
    public function validateErro($sourse, $target, $type) {
        $data = '';

        $data->validate = 0;

        if ($type === 0) {
            $data->message = FText::_('COM_FABRIK_MESSAGE_ALERT_ERRO_NO_FIELD_FOR_EXECUTION') . ' ' .
                    FText::_('COM_FABRIK_ADMINISTRATIVETOOLS_SOURCE') . ' ' . $sourse . ' / ' .
                    FText::_('COM_FABRIK_ADMINISTRATIVETOOLS_TARGET') . ' ' . $target;
        } else {
            $data->message = FText::_('COM_FABRIK_MESSAGE_ALERT_ERRO_NO_FIELD_FOR_EXECUTION_TYPE') . ' ' .
                    FText::_('COM_FABRIK_ADMINISTRATIVETOOLS_SOURCE') . ' ' . $sourse . ' / ' .
                    FText::_('COM_FABRIK_ADMINISTRATIVETOOLS_TARGET') . ' ' . $target;

            if (($sourse === 'field') && ($target === 'databasejoin')) {
                $data->field = 1;
            } elseif (($sourse === 'field') && ($target === 'dropdown')) {
                $data->field = 2;
            } elseif (($sourse === 'dropdown') && ($target === 'databasejoin')) {
                $data->field = 4;
            }
        }
        return $data;
    }

    /**
     * Method that creates an object with all data from the source table.
     *
     * @param $table
     * @param $field_source
     * @param $field_target
     *
     * @return mixed
     *
     * @since version
     */
    public function dataTableSource($table, $field_source, $field_target) {
        $db = JFactory::getDbo();

        try {
            $query = "SELECT
                `table`.id,
                `table`.{$field_source},
                `table`.{$field_target}
                FROM {$table} AS `table`;";

            $db->setQuery($query);

            return $db->loadAssocList();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools&tab=2';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Method that will bring the data from the source list.
     *
     * @param $id_form
     *
     * @return mixed
     *
     * @since version
     */
    public function tableSource($id_form) {
        $db = JFactory::getDbo();

        try {
            $query = "SELECT * FROM #__fabrik_lists AS `table` WHERE table.published = 1 AND `table`.id = {$id_form};";

            $db->setQuery($query);

            return $db->loadObject();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools&tab=2';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Method that will bring all data regarding the source and destination elements.
     *
     * @param $id_form
     * @param $id_source
     * @param $id_target
     *
     * @return mixed
     *
     * @since version
     */
    public function elementSourceTarget($id_form, $id_source, $id_target) {
        $db = JFactory::getDbo();

        try {
            $sql = "SELECT element.* FROM
                    #__fabrik_elements AS element
                    LEFT JOIN #__fabrik_formgroup AS fg ON element.group_id = fg.group_id
                    LEFT JOIN #__fabrik_lists AS list ON fg.form_id = list.form_id
                WHERE
                    list.published = 1 AND
                    list.form_id = {$id_form}  AND
                    element.id IN ({$id_source}, {$id_target})";

            $db->setQuery($sql);

            return $db->loadObjectList();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools&tab=2';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Method that will check if the information already exists in the destination table.
     *
     * @param $table
     * @param $field
     * @param $search
     *
     * @return mixed
     *
     * @since version
     */
    public function existTargetTableData($table, $field, $search) {
        $db = JFactory::getDbo();

        try {
            $query = "SELECT
                `table`.id,
                `table`.{$field}
                FROM {$table} AS `table`
                WHERE
                `table`.{$field} LIKE '{$db->escape($search)}';";

            $db->setQuery($query);

            return $db->loadAssoc();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools&tab=2';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Method executed when the information in the destination database exists then it changes the source.
     *
     * @param $table
     * @param $id_target
     * @param $id_source
     * @param $field_target
     *
     * @return bool
     *
     * @since version
     */
    public function updateDataTableSource($table, $id_target, $id_source, $field_target) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "UPDATE {$table} SET {$field_target} = {$id_target} WHERE id = {$id_source}";

            $db->setQuery($query);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Method executed when there is no information in the destination database then it enters in destination and changes the source.
     *
     * @param $table_taget
     * @param $field_target
     * @param $data_target
     * @param $table_source
     * @param $id_source
     * @param $field_source
     *
     * @return bool
     *
     * @since version
     */
    public function insertIntoTargetAndChargeSourceTable($table_taget, $field_target, $data_target, $table_source, $id_source, $field_source) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "INSERT INTO `{$table_taget}` (`{$field_target}`) VALUES ('{$db->escape($data_target)}');";

            $db->setQuery($query);

            $db->execute();

            $id = $db->insertid();

            $query1 = "UPDATE {$table_source} SET {$field_source} = {$id} WHERE id = {$id_source}";

            $db->setQuery($query1);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Method to insert into source table that is databasejoin multSelect without sync.
     *
     * @param $join_source
     * @param $id_source
     * @param $id_target
     *
     * @return bool
     *
     * @since version
     */
    public function insertMultipleSourceTable($join_source, $id_source, $id_target) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "INSERT INTO `{$join_source->table_join}` (`{$join_source->table_join_key}`, `{$join_source->table_key}`) VALUES ({$id_source},{$id_target})";

            $db->setQuery($query);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Method for inserting into target table and source table.
     *
     * @param $table_taget
     * @param $field_target
     * @param $data_target
     * @param $join_source
     * @param $id_source
     *
     * @return bool
     *
     * @since version
     */
    public function insertInTargetAndSourceTable($table_taget, $field_target, $data_target, $join_source, $id_source) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "INSERT INTO `{$table_taget}` (`{$field_target}`) VALUES ('{$db->escape($data_target)}');";

            $db->setQuery($query);

            $db->execute();

            $id = $db->insertid();

            $query1 = "INSERT INTO `{$join_source->table_join}` (`{$join_source->table_join_key}`, `{$join_source->table_key}`) VALUES ({$id_source},{$id})";

            $db->setQuery($query1);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Method for inserting into a dropdown target table and databasejoin multSelect source table.
     *
     * @param $table_taget
     * @param $field_target
     * @param $data_target
     * @param $join_source
     * @param $id_source
     * @param $field_sychr_source
     *
     * @return bool
     *
     * @since version
     */
    public function insertInTargetDropAndSourceMultTable($table_taget, $field_target, $data_target, $join_source, $id_source, $field_sychr_source) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "INSERT INTO `{$table_taget}` (`{$field_target}`, `{$field_sychr_source}`) VALUES ('{$db->escape($data_target)}', {$id_source});";

            $db->setQuery($query);

            $db->execute();

            $id = $db->insertid();

            $query1 = "INSERT INTO `{$join_source->table_join}` (`{$join_source->table_join_key}`, `{$join_source->table_key}`) VALUES ({$id_source},{$id})";

            $db->setQuery($query1);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Method takes destination list information
     *
     * @param $table_name_taget
     *
     * @return mixed
     *
     * @since version
     */
    public function tableTaget($table_name_taget) {
        $db = JFactory::getDbo();

        try {
            $query = "SELECT * FROM #__fabrik_lists AS `table` WHERE table.published = 1 AND `table`.db_table_name = '{$table_name_taget}';";

            $db->setQuery($query);

            return $db->loadObject();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools&tab=2';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Method takes the information from the target table element that is synchronized with the source table.
     *
     * @param $table_target
     * @param $synchronism
     * @param $id_list_target
     *
     * @return mixed
     *
     * @since version
     */
    public function elementTableTarget($table_target, $synchronism, $id_list_target) {
        $db = JFactory::getDbo();

        try {
            $sql = "SELECT
                    element.id,
                    element.group_id,
                    element.`plugin`,
                    element.label,
                    element.name,
                    element.params
                    FROM
                    #__fabrik_elements AS element
                    LEFT JOIN #__fabrik_formgroup AS `group` ON element.group_id = `group`.group_id
                    LEFT JOIN #__fabrik_lists AS list ON `group`.form_id = list.form_id
                    WHERE
                    list.published = 1 AND
                    element.`name` = '{$synchronism}' AND
                    list.id = {$id_list_target} AND
                    list.db_table_name = '{$table_target}'";

            $db->setQuery($sql);

            return $db->loadObject();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools&tab=2';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Method that inserts target, changes source, and inserts into the repeating target table.
     *
     * @param $table_taget
     * @param $field_target
     * @param $data_target
     * @param $table_source
     * @param $id_source
     * @param $field_source
     * @param $join_target
     *
     * @return bool
     *
     * @since version
     */
    public function insertTargetChangesSourceInsertTargetRepeatTable($table_taget, $field_target, $data_target, $table_source, $id_source, $field_source, $join_target) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "INSERT INTO `{$table_taget}` (`{$field_target}`) VALUES ('{$db->escape($data_target)}');";

            $db->setQuery($query);

            $db->execute();

            $id = $db->insertid();

            $query1 = "UPDATE {$table_source} SET {$field_source} = {$id} WHERE id = {$id_source}";

            $db->setQuery($query1);

            $db->execute();

            $query2 = "INSERT INTO `{$join_target->table_join}` (`{$join_target->table_join_key}`, `{$join_target->table_key}`) VALUES ({$id},{$id_source})";

            $db->setQuery($query2);

            $db->execute();

            $db->transactionCommit();


            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Method that alters dropdown source table and inserts into target table in repetition.
     *
     * @param $table_source
     * @param $id_source
     * @param $field_source
     * @param $join_target
     * @param $id_target
     *
     * @return bool
     *
     * @since version
     */
    public function updateTableSourceDropdownInsertTableTargetRepeat($table_source, $id_source, $field_source, $join_target, $id_target) {
        $db = JFactory::getDbo();

        $query = "SELECT repit.id
                    FROM {$join_target->table_join} AS repit
                    WHERE repit.{$join_target->table_join_key} = {$id_target} AND repit.{$join_target->table_key} = {$id_source};";

        $db->setQuery($query);

        $result = $db->loadObject();

        try {
            $db->transactionStart();

            $query1 = "UPDATE {$table_source} SET {$field_source} = {$id_target} WHERE id = {$id_source}";

            $db->setQuery($query1);

            $db->execute();

            if (count($result) === 0) {
                $query2 = "INSERT INTO `{$join_target->table_join}` (`{$join_target->table_join_key}`, `{$join_target->table_key}`) VALUES ({$id_target},{$id_source})";

                $db->setQuery($query2);

                $db->execute();
            }
            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Function that will insert into source table, insert into source repeating table and insert into target repeating table.
     *
     * @param $table_taget
     * @param $field_target
     * @param $data_target
     * @param $join_source
     * @param $id_source
     * @param $join_target
     *
     * @return bool
     *
     * @since version
     */
    public function insertTargetTableInsertRepeatTargetTableInsertRepeatSourceTable($table_taget, $field_target, $data_target, $join_source, $id_source, $join_target) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "INSERT INTO `{$table_taget}` (`{$field_target}`) VALUES ('{$db->escape($data_target)}');";

            $db->setQuery($query);

            $db->execute();

            $id = $db->insertid();

            $query1 = "INSERT INTO `{$join_source->table_join}` (`{$join_source->table_join_key}`, `{$join_source->table_key}`) VALUES ({$id_source},{$id})";

            $db->setQuery($query1);

            $db->execute();

            $query2 = "INSERT INTO `{$join_target->table_join}` (`{$join_target->table_join_key}`, `{$join_target->table_key}`) VALUES ({$id},{$id_source})";

            $db->setQuery($query2);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Function that will insert into source repeating table and insert into target repeating table.
     *
     * @param $join_source
     * @param $id_source
     * @param $id_target
     * @param $join_target
     *
     * @return bool
     *
     * @since version
     */
    public function insertSourceRepeatTableInsertTargetRepeatTable($join_source, $id_source, $id_target, $join_target) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "INSERT INTO `{$join_source->table_join}` (`{$join_source->table_join_key}`, `{$join_source->table_key}`) VALUES ({$id_source},{$id_target})";

            $db->setQuery($query);

            $db->execute();

            $query1 = "INSERT INTO `{$join_target->table_join}` (`{$join_target->table_join_key}`, `{$join_target->table_key}`) VALUES ({$id_target},{$id_source})";

            $db->setQuery($query1);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Function that will update the data of the source table and modify the parameters of the element table.
     *
     * @param $table
     * @param $field
     * @param $id
     * @param $id_element
     * @param $data
     * @param $element_params
     *
     * @return bool
     *
     * @since version
     */
    public function updateDataTableSourceUpdateTableElement($table, $field, $id, $id_element, $data, $element_params) {
        $db = JFactory::getDbo();

        $paramsDB = json_encode($element_params);

        try {
            $db->transactionStart();

            $query = "UPDATE `{$table}`
                    SET
                    `{$field}` = '{$db->escape($data)}'
                    WHERE `id` = {$id};";

            $db->setQuery($query);

            $db->execute();

            $query1 = "UPDATE `#__fabrik_elements`
                    SET
                    `params` = '{$db->escape($paramsDB)}'
                    WHERE `id` = {$id_element};";

            $db->setQuery($query1);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * list of element types.
     *
     * @throws Exception
     */
    public function listElementType() {
        $app = JFactory::getApplication();

        $db = JFactory::getDbo();

        $id = $app->input->getInt("idList");

        $type_plugin = $app->input->getString("typePlugin");

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
                list.published = 1 AND
                fgroup.form_id = {$id} AND element.`plugin` = '{$type_plugin}'
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
     * Function to get the engine type information from a possible table, to know if it is InnoDB or MyISAM.
     *
     * @param $table
     *
     * @return mixed
     *
     * @since version
     */
    public function checkEngineTypeTable($table) {
        $db = JFactory::getDbo();

        $sql = "select `engine` from information_schema.tables where table_name = '{$table}';";

        $db->setQuery($sql);

        return $db->loadObject();
    }

    /**
     * Method that changes a table in the database to InnoDB.
     *
     * @param $table
     * @return bool
     */
    public function alterTableEngineType($table) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "ALTER TABLE {$table} ENGINE = InnoDB;";

            $db->setQuery($query);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Function that takes information from the join table field that is related to the target table to check if the data type is equal.
     *
     * @param $table
     * @param $field
     *
     * @return mixed
     *
     * @since version
     */
    public function joinTableFieldType($table, $field) {
        $db = JFactory::getDbo();

        try {
            $sql = "SHOW COLUMNS FROM `{$table}` WHERE field = '{$field}';";

            $db->setQuery($sql);

            return $db->loadObject();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Function that changes the field data type of a table to make the field integer
     *
     * @param $table
     * @param $field
     *
     * @return bool
     *
     * @since version
     */
    public function alterTableColummDataType($table, $field) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "ALTER TABLE `{$table}` modify `{$field}` INT(11);";

            $db->setQuery($query);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Function that will create the foreign key in the table that references the source and destination table.
     *
     * @param $table
     * @param $table_source
     * @param $field_source
     * @param $table_target
     * @param $field_target
     * @param $update
     * @param $delete
     *
     * @return bool
     *
     * @since version
     */
    public function alterTableCreateForeignKeyRelatedFields2($table, $table_source, $field_source, $table_target, $field_target, $update, $delete) {
        $db = JFactory::getDbo();

        $name_fk1 = 'fk_' . $table . '_' . $field_source . '_' . $table_source;

        $name_fk2 = 'fk_' . $table . '_' . $field_target . '_' . $table_target;

        try {
            $db->transactionStart();

            $query = "ALTER TABLE `{$table}` ADD CONSTRAINT `{$name_fk1}` FOREIGN KEY ( `{$field_source}` ) REFERENCES `{$table_source}` ( `id` ) ON UPDATE {$update} ON DELETE {$delete} ;";

            $db->setQuery($query);

            $db->execute();

            $query1 = "ALTER TABLE `{$table}` ADD CONSTRAINT `{$name_fk2}` FOREIGN KEY ( `{$field_target}` ) REFERENCES `{$table_target}` ( `id` ) ON UPDATE {$update} ON DELETE {$delete} ;";

            $db->setQuery($query1);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Function that checks if the foreign key already exists in the table.
     *
     * @param $table
     *
     * @return mixed
     *
     * @since version
     */
    public function checkIfForeignKeyExists($table, $table_taget, $field) {
        $db = JFactory::getDbo();

        try {
            $sql = "SELECT COUNT(column_name) as forkey
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE REFERENCED_TABLE_SCHEMA IS NOT NULL 
                AND TABLE_NAME = '{$table}'
                AND COLUMN_NAME = '{$field}'
                AND REFERENCED_TABLE_NAME = '{$table_taget}';";

            $db->setQuery($sql);

            return $db->loadObject();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Function of source table structure
     *
     * @param $tableSource
     *
     *
     * @since version
     */
    public function sourceTableStructure($tableSource) {
        $engine_source = $this->checkEngineTypeTable($tableSource);

        if ($engine_source->engine === 'MyISAM') {
            $engine_source_bool = $this->alterTableEngineType($tableSource);
        } elseif ($engine_source->engine === 'InnoDB') {
            $engine_source_bool = true;
        }
        return $engine_source_bool;
    }

    /**
     * Join Table Structure Function
     *
     * @param $join_source
     *
     * @return bool
     *
     * @since version
     */
    public function joinTableStructure($join_source) {
        $engine_join = $this->checkEngineTypeTable($join_source);

        if ($engine_join->engine === 'MyISAM') {
            $engine_join_bool = $this->alterTableEngineType($join_source);
        } elseif ($engine_join->engine === 'InnoDB') {
            $engine_join_bool = true;
        }
        return $engine_join_bool;
    }

    /**
     * Function of target table structure
     *
     * @param $table_target
     *
     * @return bool
     *
     * @since version
     */
    public function targetTableStructure($table_target) {
        $engine_target = $this->checkEngineTypeTable($table_target);

        if ($engine_target->engine === 'MyISAM') {
            $engine_target_bool = $this->alterTableEngineType($table_target);
        } elseif ($engine_target->engine === 'InnoDB') {
            $engine_target_bool = true;
        }
        return $engine_target_bool;
    }

    /**
     * Function that creates the foreign key in the source table that references the target table.
     *
     * @param $table
     * @param $field_source
     * @param $table_target
     * @param $update
     * @param $delete
     *
     * @return bool
     *
     * @since version
     */
    public function alterTableCreateForeignKeyRelatedFields1($table, $table_target, $field_source, $update, $delete) {
        $db = JFactory::getDbo();

        $name_fk1 = 'fk_' . $table . '_' . $field_source . '_' . $table_target;

        try {
            $db->transactionStart();

            $query = "ALTER TABLE `{$table}` ADD CONSTRAINT `{$name_fk1}` FOREIGN KEY ( `{$field_source}` ) REFERENCES `{$table_target}` ( `id` ) ON UPDATE {$update} ON DELETE {$delete} ;";

            $db->setQuery($query);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Structure that checks for equality between related fields.
     *
     * @param $join_db_name
     * @param $table_join
     * @param $table_key
     *
     * @return bool
     *
     * @since version
     */
    public function estructureChecksEqualityBetweenFields($table_name, $table_join, $table_key) {
        $join_type_field = $this->joinTableFieldType($table_join, $table_key);

        $type_field = $this->joinTableFieldType($table_name, 'id');

        if ($join_type_field->Type !== $type_field->Type) {
            $join_type_bool = $this->alterTableColummDataType($table_join, $table_key);
        } elseif ($join_type_field->Type === $type_field->Type) {
            $join_type_bool = true;
        }
        return $join_type_bool;
    }

    /**
     * Function that creates the foreign key in the source table that references the target table and in the join table that references the source and destination table.
     *
     * @param $table
     * @param $table_source
     * @param $field_source
     * @param $field_source_parent
     * @param $table_target
     * @param $field_target
     * @param $update
     * @param $delete
     *
     * @return bool
     *
     * @since version
     */
    public function alterTableCreateForeignKeyRelatedFields3($table, $table_source, $field_source, $field_source_parent, $table_target, $field_target, $update, $delete) {
        $db = JFactory::getDbo();

        $name_fk1 = 'fk_' . $table_target . '_' . $field_target . '_' . $table_source;
        $name_fk2 = 'fk_' . $table . '_' . $field_source_parent . '_' . $table_source;
        $name_fk3 = 'fk_' . $table . '_' . $field_source . '_' . $table_target;

        try {
            $db->transactionStart();

            $query = "ALTER TABLE `{$table_target}` ADD CONSTRAINT `{$name_fk1}` FOREIGN KEY ( `{$field_target}` ) REFERENCES `{$table_source}` ( `id` ) ON UPDATE {$update} ON DELETE {$delete} ;";

            $db->setQuery($query);

            $db->execute();

            $query1 = "ALTER TABLE `{$table}` ADD CONSTRAINT `{$name_fk2}` FOREIGN KEY ( `{$field_source_parent}` ) REFERENCES `{$table_source}` ( `id` ) ON UPDATE {$update} ON DELETE {$delete} ;";

            $db->setQuery($query1);

            $db->execute();

            $query2 = "ALTER TABLE `{$table}` ADD CONSTRAINT `{$name_fk3}` FOREIGN KEY ( `{$field_source}` ) REFERENCES `{$table_target}` ( `id` ) ON UPDATE {$update} ON DELETE {$delete} ;";

            $db->setQuery($query2);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Function that creates the foreign key in synchronous repeating tables that are related to their source and destination tables.
     *
     * @param $table_join_source
     * @param $table_join_target
     * @param $table_source
     * @param $table_target
     * @param $field_source
     * @param $field_target
     * @param $update
     * @param $delete
     *
     * @return bool
     *
     * @since version
     */
    public function alterTableCreateForeignKeyRelatedFields4($table_join_source, $table_join_target, $table_source, $table_target, $field_source, $field_target, $update, $delete) {
        $db = JFactory::getDbo();

        $parent_id = 'parent_id';

        $name_fk1 = 'fk_' . $table_join_source . '_' . $parent_id . '_' . $table_source;
        $name_fk2 = 'fk_' . $table_join_source . '_' . $field_target . '_' . $table_target;
        $name_fk3 = 'fk_' . $table_join_target . '_' . $parent_id . '_' . $table_target;
        $name_fk4 = 'fk_' . $table_join_target . '_' . $field_source . '_' . $table_source;

        try {
            $db->transactionStart();

            $query1 = "ALTER TABLE `{$table_join_source}` ADD CONSTRAINT `{$name_fk1}` FOREIGN KEY ( `{$parent_id}` ) REFERENCES `{$table_source}` ( `id` ) ON UPDATE {$update} ON DELETE {$delete} ;";

            $db->setQuery($query1);

            $db->execute();

            $query2 = "ALTER TABLE `{$table_join_source}` ADD CONSTRAINT `{$name_fk2}` FOREIGN KEY ( `{$field_target}` ) REFERENCES `{$table_target}` ( `id` ) ON UPDATE {$update} ON DELETE {$delete} ;";

            $db->setQuery($query2);

            $db->execute();

            $query3 = "ALTER TABLE `{$table_join_target}` ADD CONSTRAINT `{$name_fk3}` FOREIGN KEY ( `{$parent_id}` ) REFERENCES `{$table_target}` ( `id` ) ON UPDATE {$update} ON DELETE {$delete} ;";

            $db->setQuery($query3);

            $db->execute();

            $query4 = "ALTER TABLE `{$table_join_target}` ADD CONSTRAINT `{$name_fk4}` FOREIGN KEY ( `{$field_source}` ) REFERENCES `{$table_source}` ( `id` ) ON UPDATE {$update} ON DELETE {$delete} ;";

            $db->setQuery($query4);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * display messages for different use administration tool.
     *
     * @param $result_sql
     * @param $message_true
     * @param $message_false
     *
     *
     * @since version
     */
    public function displayMessages($result_sql, $message_true, $message_false) {
        if ($result_sql) {
            $ar_message['message'] = JText::_($message_true);

            $ar_message['type'] = 'Success';
        } else {
            $ar_message['message'] = JText::_($message_false);

            $ar_message['type'] = 'info';
        }
        return $ar_message;
    }

    /**
     * Resizes the original images to thumbs that are sized in the parameters.
     *
     * @param $file
     * @param $path_thumbs
     * @param $width
     * @param $height
     */
    public function imageTransformationThumbsCrops($file, $path, $width, $height, $type = 0, $mult = 0) {
        $file = str_replace('\\', "/", $file);

        $ext = explode('.', $file);

        $type2 = strtolower(substr(strrchr($file, "."), 1));

        if ($mult === 0) {
            $ar_file = explode('/', $ext[count($ext) - 2]);

            $nm_file = $ar_file[count($ar_file) - 1] . '.' . $ext[count($ext) - 1];

            $path .= '/' . $nm_file;
        } else {
            $ar_file = explode('\\', $ext[count($ext) - 2]);

            $nm_file = $ar_file[count($ar_file) - 1] . '.' . $ext[count($ext) - 1];

            $path .= '\\' . $nm_file;
        }

        if ($type2 == 'jpeg')
            $type2 = 'jpg';

        $mine_type = mime_content_type($file);

        if ($type2 === 'jpg') {
            if ($mine_type === 'image/gif') {
                $image_original = imagecreatefromgif($file);
            } elseif ($mine_type === 'image/png') {
                $image_original = imagecreatefrompng($file);
            } else {
                $image_original = imagecreatefromjpeg($file);
            }
        } elseif ($type2 === 'png') {
            if ($mine_type === 'image/jpeg') {
                $image_original = imagecreatefromjpeg($file);
            } elseif ($mine_type === 'image/gif') {
                $image_original = imagecreatefromgif($file);
            } else {
                $image_original = imagecreatefrompng($file);
            }
        } elseif ($type2 === 'gif') {
            if ($mine_type === 'image/jpeg') {
                $image_original = imagecreatefromjpeg($file);
            } elseif ($mine_type === 'image/png') {
                $image_original = imagecreatefrompng($file);
            } else {
                $image_original = imagecreatefromgif($file);
            }
        }

        list($width_old, $height_old) = getimagesize($file);

        if ($type === 1) {
            $to_crop_array = array('x' => 0, 'y' => 0, 'width' => $width, 'height' => $height);

            $image_tmp = imagecrop($image_original, $to_crop_array);
        } else {
            $image_tmp = imagecreatetruecolor($width, $height);

            imagecopyresampled($image_tmp, $image_original, 0, 0, 0, 0, $width, $height, $width_old, $height_old);
        }

        if ($type2 === 'jpg') {
            imagejpeg($image_tmp, $path);
        } elseif ($type2 === 'png') {
            imagepng($image_tmp, $path);
        } elseif ($type2 === 'gif') {
            imagegif($image_tmp, $path);
        }
    }

    /**
     * It takes all the data from the source field of the list table that was chosen.
     *
     * @param $table
     * @param $field_source
     * @return mixed
     */
    public function dataTableSourceFieldSingle($table, $field_source) {
        $db = JFactory::getDbo();

        try {
            $query = "SELECT
                `table`.id,
                `table`.{$field_source}
                FROM {$table} AS `table`
                ORDER BY `table`.id ASC;";

            $db->setQuery($query);

            return $db->loadAssocList();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Method to insert into source table that is databasejoin multSelect without sync.
     *
     * @param $join_source
     * @param $id_source
     * @param $text
     *
     * @return bool
     *
     * @since version
     */
    public function insertMultipleSourceTableFileupload($join_source, $id_source, $text) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "INSERT INTO `{$join_source->table_join}` (`{$join_source->table_join_key}`, `{$join_source->table_key}`) VALUES ({$id_source},'{$db->escape($text)}')";

            $db->setQuery($query);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Execute the path of the file that is in the database, check if it exists, copy it to a temporary one, resize or cut and save it in the same place and with the same name.
     *
     * @param $data
     * @param $params
     * @param $value
     * @return bool
     */
    public function performChangeThumbsCrops($params, $value, $key) {
        $path = dirname(dirname($_SERVER['SCRIPT_FILENAME']));

        $photo = file_exists($path . $value);

        $file = $path . $value;

        if ($photo) {
            if ($params->make_thumbnail === '1') {
                $width = $params->thumb_max_width;
                $height = $params->thumb_max_height;

                $path_thumbs = $path . '/' . $params->thumb_dir;

                $this->imageTransformationThumbsCrops($file, $path_thumbs, $width, $height);
            }

            if ($params->fileupload_crop === '1') {
                $width = $params->fileupload_crop_width;
                $height = $params->fileupload_crop_height;

                $path_crops = $path . '/' . $params->fileupload_crop_dir;

                $this->imageTransformationThumbsCrops($file, $path_crops, $width, $height, 1);
            }
        }
    }

    /**
     * Function that will take exception messages and return them to the client already treated.
     *
     * @param $code
     * @param $message
     * @return mixed
     */
    public static function handlePossibleExceptions($code, $message) {
        switch ($code) {
            case 1064:
                $text = FText::_('COM_FABRIK_EXCEPTION_MESSAGE_1064');

                break;
            default:
                $text = $message;

                break;
        }
        return $text;
    }

    /**
     * Method that changes the status of the harvesting table.
     *
     * @throws Exception
     */
    public function enableDisableHarvesting() {
        $app = JFactory::getApplication();

        $db = JFactory::getDbo();

        $id = $app->input->getInt("id");

        $status = $app->input->getInt("status");

        try {
            $db->transactionStart();

            $query = "UPDATE `#__fabrik_harvesting` SET `status` = '{$status}' WHERE `id` = {$id};";

            $db->setQuery($query);

            $db->execute();

            $db->transactionCommit();

            echo '1';
        } catch (Exception $exc) {
            $db->transactionRollback();

            echo '0';
        }

        $app->close();
    }

    /**
     * Method that deletes an item from the playlist.
     *
     * @throws Exception
     */
    public function deleteHarvesting() {
        $app = JFactory::getApplication();

        $db = JFactory::getDbo();

        $id = $app->input->getInt("id");

        try {
            $db->transactionStart();

            $query = "DELETE FROM `#__fabrik_harvesting` WHERE `id` = {$id}";

            $db->setQuery($query);

            $db->execute();

            $db->transactionCommit();

            echo '1';
        } catch (Exception $exc) {
            $db->transactionRollback();

            echo '0';
        }

        $app->close();
    }

    /**
     * Method that separates the form submission to perform each button of different actions on the same form.
     *
     * @throws Exception
     */
    public function submitHarvesting() {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $user = JFactory::getUser();

        $data['link'] = $db->escape($app->input->getString("linkHarvest"));
        $btn = $db->escape($app->input->getString("btnSubmit"));
        $data['id'] = $app->input->getInt("idHarvest", 0);
        $data['list'] = $app->input->getInt("listHarvert");
        $data['download'] = $app->input->getInt("downloadHarvest", NULL);
        $data['extract'] = $app->input->getInt("extractTextHarvert", NULL);
        $data['mapRarvest'] = $app->input->get('mapRarvest', array(), 'array');
        $data['listDublinCoreType'] = $app->input->get('listDublinCoreType', array(), 'array');
        $data['mapRarvestHeader'] = $app->input->get('mapRarvestHeader', array(), 'array');
        $data['listDublinCoreTypeHeader'] = $app->input->get('listDublinCoreTypeHeader', array(), 'array');
        $data['sync'] = $app->input->getInt("syncHarvest");

        if ($data['sync'] === 0) {
            $data['field1'] = NULL;
            $data['field2'] = NULL;
        } elseif ($data['sync'] === 1) {
            $data['field1'] = $app->input->getInt("dateListHarvest");
            $data['field2'] = $db->escape($app->input->getString("dateRepositoryHarvest"));
        }

        date_default_timezone_set('America/Sao_Paulo');
        $data['registerDate'] = date("Y-m-d H:i:s");

        $data['users'] = $user->get('id');
        $data['idElements'] = '';

        foreach ($data['mapRarvestHeader'] as $key => $value) {
            $data['header'][$data['listDublinCoreTypeHeader'][$key]] = $value;

            if (strlen($data['idElements']) === 0) {
                $data['idElements'] .= $value;
            } else {
                $data['idElements'] .= ',' . $value;
            }
        }

        foreach ($data['mapRarvest'] as $key => $value) {
            $data['metadata'][$data['listDublinCoreType'][$key]][] = $value;

            if (strlen($data['idElements']) === 0) {
                $data['idElements'] .= $value;
            } else {
                $data['idElements'] .= ',' . $value;
            }
        }

        switch ($btn) {
            case 'btnSave':
                $this->repositoryValidator($data['link']);

                $result = $this->saveHarvesting($data, NULL, $data['registerDate']);

                if ($result['status']) {
                    $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_SUCCESS1');
                    $type_message = 'success';
                } else {
                    $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR0');
                    $type_message = 'warning';
                }

                break;

            case 'btnSaveRun':
                $this->repositoryValidator($data['link']);

                $data['line_num'] = 0;
                $data['page_xml'] = 0;

                $result = $this->saveHarvesting($data, $data['registerDate'], $data['registerDate'], 1);

                if (!$result['status']) {
                    $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR0');
                    $type_message = 'warning';

                    break;
                }

                $data['id'] = $result['id'];

                $result = $this->saveRunHarvesting($data);

                if (!$result) {
                    $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR4');
                    $type_message = 'warning';

                    break;
                } else {
                    $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_SUCCESS3');
                    $type_message = 'success';
                }

                break;

            case 'btnRumList':
                $result = $this->runListHarvesting($data);

                if (!$result) {
                    $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
                    $type_message = 'warning';

                    break;
                } else {
                    $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_SUCCESS3');
                    $type_message = 'success';
                }

                break;
        }

        $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools&tab=2';
        $this->setRedirect($site_message, $message, $type_message);
    }

    /**
     * Method that saves the tool structure.
     *
     * @throws Exception
     */
    public function saveHarvesting($data, $dateRum = NULL, $dateRecordLast = NULL, $line = NULL) {
        $db = JFactory::getDbo();

        $header = $db->escape(json_encode($data['header']));

        $metadata = $db->escape(json_encode($data['metadata']));

        try {
            $db->transactionStart();

            if ($data['id'] === 0) {
                $query = "INSERT INTO `#__fabrik_harvesting` (`repository`, `list`, `dowload_file`, `extract`, `syncronism`, `field1`, `field2`, `status`, `date_creation`,
                        `date_execution`, `users_id`, `record_last`, `map_header`, `map_metadata`, `line_num`, `page_xml`) 
                        VALUES ('{$data['link']}', {$data['list']}, '{$data['download']}', {$data['extract']}, {$data['sync']}, '{$data['field1']}', '{$data['field2']}', '1', '{$data['registerDate']}', 
                        '{$dateRum}', {$data['users']}, '{$dateRecordLast}', '{$header}', '{$metadata}', 0, 0)";
            } else {
                $query = "UPDATE `#__fabrik_harvesting` SET 
                            `repository` = '{$data['link']}', 
                            `list` = {$data['list']},
                            `dowload_file` = '{$data['download']}', 
                            `extract` = {$data['extract']},
                            `syncronism` = {$data['sync']},
                            `field1` = '{$data['field1']}', 
                            `field2` = '{$data['field2']}', ";

                if (($dateRum !== NULL) && (strlen($dateRum) !== 0)) {
                    $query .= "`date_execution` = '{$dateRum}', ";
                }

                if (($line !== NULL) && (strlen($line) !== 0)) {
                    $query .= "`line_num` = {$data['line_num']},
                               `page_xml` = {$data['page_xml']}, ";
                }

                $query .= " `users_id` = {$data['users']},
                            `record_last` = '{$dateRecordLast}',
                            `map_header` = '{$header}',
                            `map_metadata` = '{$metadata}'                           
                            WHERE `id` = {$data['id']};";
            }

            $db->setQuery($query);

            $db->execute();

            if ($data['id'] === 0) {
                $dados['id'] = $db->insertid();
            } else {
                $dados['id'] = $data['id'];
            }

            $db->transactionCommit();

            $dados['status'] = true;

            return $dados;
        } catch (Exception $exc) {
            $db->transactionRollback();

            $dados['status'] = false;
            $dados['mensagem'] = $exc;

            return $dados;
        }
    }

    /**
     * Method that saves and executes what is on the form.
     *
     * @throws Exception
     */
    public function saveRunHarvesting($data) {
        set_time_limit(0);

        $db = JFactory::getDbo();
        $config = JFactory::getConfig();

        $ext = $config->get('dbprefix');

        $data['tableSource'] = $this->tableSource($data['list']);

        $totalRecords = $data['page_xml'];
        $currentRecords = 0;

        $baseURL = $data['link'] . '?verb=ListRecords';

        $initialParams = '&resumptionToken=oai_dc////' . $totalRecords;

        $resumptionBase = '&resumptionToken=';
        $resumptionToken = 'initial';

        $fetchCounter = 1;

        while ($resumptionToken != '') {
            if ($fetchCounter === 1) {
                $url = $baseURL . $initialParams;
                $resumptionToken = '';
            } else {
                $url = $baseURL . $resumptionBase . $resumptionToken;
            }

            $xmlObj = simplexml_load_file($url);

            if ($xmlObj) {
                $xmlNode = $xmlObj->ListRecords;

                if ($fetchCounter === 1) {
                    $arNumLineXML = get_object_vars($xmlNode->resumptionToken);
                    $lineNum = (int) $arNumLineXML['@attributes']['completeListSize'];

                    $table = $ext . 'fabrik_harvesting';
                    $this->updateDataTableSource($table, $lineNum, $data['id'], 'line_num');
                }

                if ($xmlNode->count() !== 0) {
                    $currentRecords = count($xmlNode->children());

                    $dom = new DOMDocument();

                    foreach ($xmlNode->record as $recordNode) {
                        $fields = '';
                        $fieldsContent = '';
                        unset($arFieldsElement);
                        unset($arFieldsContent);

                        if (is_array($data['header']) && (!is_null($data['header']))) {
                            $dom->loadHTML('<?xml encoding="utf-8" ?>' . trim($recordNode->header->asXML()));
                            $i = 0;

                            foreach ($data['header'] as $key => $value) {
                                $metas = $dom->getElementsByTagName($key);

                                $element = $this->mappedElementsData($data['list'], $value);

                                if ($i !== 0) {
                                    $fields .= ", {$element->name}";
                                } else {
                                    $fields .= "{$element->name}";
                                }

                                if ($data['sync'] === 1) {
                                    $arFieldsElement[] = $element->name;
                                }

                                switch ($element->plugin) {
                                    case 'date':
                                        $date = date("Y-m-d H:i:s", strtotime($db->escape($metas->item(0)->nodeValue)));

                                        if ($i !== 0) {
                                            $fieldsContent .= ", '{$date}'";
                                        } else {
                                            $fieldsContent .= "'{$date}'";
                                        }

                                        if ($data['sync'] === 1) {
                                            $arFieldsContent[] = $date;
                                        }

                                        if (($data['field2'] === 'datestamp') && ($data['sync'] === 1)) {
                                            $dateSync = date("Y-m-d", strtotime($db->escape($metas->item(0)->nodeValue)));
                                            ;
                                            $fieldSync = $element->name;
                                        }

                                        break;
                                    default:
                                        if ($i !== 0) {
                                            $fieldsContent .= ", '{$db->escape($metas->item(0)->nodeValue)}'";
                                        } else {
                                            $fieldsContent .= "'{$db->escape($metas->item(0)->nodeValue)}'";
                                        }

                                        if ($data['sync'] === 1) {
                                            $arFieldsContent[] = $db->escape($metas->item(0)->nodeValue);
                                        }

                                        if ($key === 'identifier') {
                                            $identifier = $db->escape($metas->item(0)->nodeValue);
                                            $fieldIdentifier = $element->name;
                                        }

                                        break;
                                }

                                $i += 1;
                            }

                            if ($data['sync'] === 1) {
                                $result_identifier = $this->checkDataTableExist($data['tableSource']->db_table_name, $fieldIdentifier, $identifier, $fieldSync, $dateSync);
                                $update = 1;
                            } else {
                                $result_identifier = $this->checkDataTableExist($data['tableSource']->db_table_name, $fieldIdentifier, $identifier);
                                $update = 0;
                            }

                            if (is_null($result_identifier->id) && ($update === 0)) {
                                if (is_array($data['metadata']) && (!is_null($data['metadata']))) {
                                    $dom->loadHTML('<?xml encoding="utf-8" ?>' . trim($recordNode->metadata->asXML()));

                                    $i = 0;
                                    unset($arFields);
                                    unset($tableRepeat);
                                    unset($arFieldsTag);
                                    unset($tableRepeatTag);

                                    foreach ($data['metadata'] as $key => $objFields) {
                                        $tag = explode(':', $key);
                                        $metas = $dom->getElementsByTagName($tag[1]);

                                        $arLength = array_count_values($objFields);
                                        $j = 0;

                                        $fieldExtra = "";

                                        foreach ($objFields as $index => $value) {
                                            $joinModelSource = JModelLegacy::getInstance('Join', 'FabrikFEModel');
                                            $element = $this->mappedElementsData($data['list'], $value);
                                            $objParams = json_decode($element->params);

                                            if (strlen($fields) !== 0) {
                                                if ($j === 0) {
                                                    if (($objParams->database_join_display_type !== "checkbox") && ($objParams->database_join_display_type !== "multilist") && ($element->plugin !== 'tags')) {
                                                        $fields .= ", {$element->name}";
                                                        $itemFieldElement = 1;
                                                    } else {
                                                        $itemFieldElement = 0;
                                                    }
                                                } elseif (($arLength[$value] === 1) && ($objParams->database_join_display_type !== "checkbox") && ($objParams->database_join_display_type !== "multilist") && ($element->plugin !== 'tags')) {
                                                    $fields .= ", {$element->name}";
                                                    $itemFieldElement = 1;
                                                }
                                            } else {
                                                $fields .= "{$element->name}";
                                                $itemFieldElement = 1;
                                            }

                                            if (($data['sync'] === 1) && ($itemFieldElement === 1)) {
                                                $arFieldsElement[] = $element->name;
                                            }

                                            switch ($element->plugin) {
                                                case 'date':
                                                    $date = date("Y-m-d H:i:s", strtotime($db->escape($metas->item($index)->nodeValue)));

                                                    if (strlen($fieldsContent) !== 0) {
                                                        $fieldsContent .= ", '{$date}'";
                                                    } else {
                                                        $fieldsContent .= "'{$date}'";
                                                    }

                                                    if ($data['sync'] === 1) {
                                                        $arFieldsContent[] = $date;
                                                    }

                                                    if (($data['field2'] === 'dc:date') && ($data['sync'] === 1)) {
                                                        $dateSync = date("Y-m-d", strtotime($db->escape($metas->item($index)->nodeValue)));
                                                        $fieldSync = $element->name;
                                                    }

                                                    break;
                                                case 'dropdown':
                                                    $objOptions = $objParams->sub_options;

                                                    if (!(in_array($db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue))), $objOptions->sub_values)) &&
                                                            !(in_array($db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue))), $objOptions->sub_labels))) {
                                                        $objOptions->sub_values[] = ucwords(mb_strtolower($db->escape($metas->item($index)->nodeValue)));
                                                        $objOptions->sub_labels[] = ucwords(mb_strtolower($db->escape($metas->item($index)->nodeValue)));
                                                    }

                                                    $parans_update = $this->updateTableElement($value, $objParams);

                                                    if ($parans_update) {
                                                        if (strlen($fieldsContent) !== 0) {
                                                            $fieldsContent .= ", '{$db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)))}'";
                                                        } else {
                                                            $fieldsContent .= "'{$db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)))}'";
                                                        }
                                                    }

                                                    if ($data['sync'] === 1) {
                                                        $arFieldsContent[] = $db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)));
                                                    }

                                                    break;
                                                case 'databasejoin':
                                                    if ((($objParams->database_join_display_type === "dropdown") || ($objParams->database_join_display_type === "radio") ||
                                                            ($objParams->database_join_display_type === "auto-complete"))) {
                                                        $exist_data_target = $this->existTargetTableData($objParams->join_db_name, $objParams->join_val_column, $metas->item($index)->nodeValue);

                                                        if (count($exist_data_target) === 0) {
                                                            $result = $this->insertTable($objParams->join_db_name, $objParams->join_val_column, $metas->item($index)->nodeValue);
                                                        } else {
                                                            $result = $exist_data_target['id'];
                                                        }

                                                        if ((strlen($fieldsContent) !== 0) && ($result !== false)) {
                                                            $fieldsContent .= ", '{$result}'";
                                                        } elseif ((strlen($fieldsContent) === 0) && ($result !== false)) {
                                                            $fieldsContent .= "'{$result}'";
                                                        } elseif ((strlen($fieldsContent) === 0) && ($result === false)) {
                                                            $fieldsContent .= ",''";
                                                        } elseif ((strlen($fieldsContent) !== 0) && ($result === false)) {
                                                            $fieldsContent .= "''";
                                                        }

                                                        if ($data['sync'] === 1) {
                                                            $arFieldsContent[] = $result;
                                                        }
                                                    } elseif ((($objParams->database_join_display_type === "checkbox") || ($objParams->database_join_display_type === "multilist"))) {
                                                        $tableRepeat[$tag[1]] = $joinModelSource->getJoinFromKey('element_id', $value);

                                                        for ($numDBJ = 0; $numDBJ < $metas->length; $numDBJ++) {
                                                            if ($tag[1] === 'creator') {
                                                                $arTagText = explode(',', $metas->item($numDBJ)->nodeValue);
                                                                $tagText = trim($arTagText[1]) . ' ' . trim($arTagText[0]);
                                                            } else {
                                                                $tagText = trim($metas->item($numDBJ)->nodeValue);
                                                            }

                                                            $exist_data_target = $this->existTargetTableData($objParams->join_db_name, $objParams->join_val_column, $tagText);

                                                            if (count($exist_data_target) === 0) {
                                                                $arFields[$tag[1]][] = (int) $this->insertTable($objParams->join_db_name, $objParams->join_val_column, $tagText);
                                                            } else {
                                                                $arFields[$tag[1]][] = (int) $exist_data_target['id'];
                                                            }
                                                        }
                                                    }

                                                    break;
                                                case 'tags':
                                                    $nameTableTags = $ext . 'tags';

                                                    if ($objParams->tags_dbname === $nameTableTags) {
                                                        $tagSelestField = 'title';
                                                    }

                                                    $tableRepeatTag[$tag[1]] = $joinModelSource->getJoinFromKey('element_id', $value);

                                                    for ($numDBJ = 0; $numDBJ < $metas->length; $numDBJ++) {
                                                        $tagText = trim(ucfirst($metas->item($numDBJ)->nodeValue));

                                                        $exist_data_target = $this->existTargetTableData($objParams->tags_dbname, $tagSelestField, $tagText);

                                                        if ($objParams->tags_dbname === $nameTableTags) {
                                                            $tagTextExtra = $this->removeAccentsSpecialCharacters(trim(strtolower($metas->item($numDBJ)->nodeValue)));
                                                            date_default_timezone_set('America/Sao_Paulo');
                                                            $data['registerDate'] = date("Y-m-d H:i:s");

                                                            $user = JFactory::getUser();
                                                            $data['users'] = $user->get('id');

                                                            $tagInsertField = "`parent_id`, `level`, `path`, `title`, `alias`, `published`, `checked_out_time`, `access`, `created_user_id`,
                                             `created_time`, `modified_time`, `publish_up`, `publish_down`";

                                                            $tagInsertData = "'1','1','{$tagTextExtra}','{$tagText}','{$tagTextExtra}','1','{$data['registerDate']}','1','{$data['users']}',
                                            '{$data['registerDate']}','{$data['registerDate']}','{$data['registerDate']}','{$data['registerDate']}'";
                                                        }

                                                        if (count($exist_data_target) === 0) {
                                                            $arFieldsTag[$tag[1]][] = (int) $this->insertTableMultipleFieldsData($objParams->tags_dbname, $tagInsertField, $tagInsertData);
                                                        } else {
                                                            $arFieldsTag[$tag[1]][] = (int) $exist_data_target['id'];
                                                        }
                                                    }

                                                    break;
                                                default:
                                                    $num = $index + 1;

                                                    if (strlen($fieldsContent) !== 0) {
                                                        if ($j === 0) {
                                                            $fieldExtra .= $db->escape($metas->item($index)->nodeValue);
                                                        } elseif ($arLength[$value] === 1) {
                                                            $fieldExtra = $db->escape($metas->item($j)->nodeValue);
                                                        } else {
                                                            $fieldExtra .= '|' . $db->escape($metas->item($index)->nodeValue);
                                                        }

                                                        if (($num === $arLength[$value]) || ($arLength[$value] === 1)) {
                                                            $fieldsContent .= ", '{$fieldExtra}'";
                                                        }
                                                    } else {
                                                        $fieldExtra .= $db->escape($metas->item($index)->nodeValue);

                                                        if ($num === $arLength[$value]) {
                                                            $fieldsContent .= "'{$fieldExtra}'";
                                                        }
                                                    }

                                                    if ($data['sync'] === 1) {
                                                        $arFieldsContent[] = $fieldExtra;
                                                    }

                                                    break;
                                            }

                                            $j += 1;
                                        }

                                        $i += 1;
                                    }
                                }

                                if ($data['download'] !== 0) {
                                    $metas2 = $this->searchFileRepositoryOER($data['link'], $identifier);

                                    $dirName = '';
                                    unset($tableRepeatFile);

                                    if ($metas2 !== false) {
                                        $path = dirname(dirname($_SERVER['SCRIPT_FILENAME']));
                                        for ($i = 0; $i < $metas2->length; $i++) {
                                            $tag = $metas2->item($i);

                                            if (($tag->nodeName == 'description') && ((strpos($tag->getAttribute('rdf:about'), '.pdf') !== false) &&
                                                    (strpos($tag->getAttribute('rdf:about'), '.pdf.') === false))) {
                                                $linkFileXML = substr($tag->getAttribute('rdf:about'), 0, strpos($tag->getAttribute('rdf:about'), '.pdf') + 4);
                                            }
                                        }

                                        $file_ext = explode('.', $linkFileXML);

                                        if (count($file_ext) === 2) {
                                            $elementFile = $this->mappedElementsData($data['list'], $data['download']);
                                            $objParamsFile = json_decode($elementFile->params);

                                            $urlDirectory = $objParamsFile->ul_directory;

                                            $nameIdentifier = str_replace('/', '_', str_replace(':', '_', str_replace('.', '_', $identifier)));

                                            $dir = $path . $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML);

                                            if ($objParamsFile->ajax_upload === '0') {
                                                $fields .= ", {$elementFile->name}";

                                                $dirName = $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML);
                                                $fieldsContent .= ", '{$dirName}'";

                                                if ($data['sync'] === 1) {
                                                    $arFieldsElement[] = $elementFile->name;
                                                    $arFieldsContent[] = $dirName;
                                                }
                                            } elseif ($objParamsFile->ajax_upload === '1') {
                                                $joinModelSourceFile = JModelLegacy::getInstance('Join', 'FabrikFEModel');
                                                $tableRepeatFile['file'] = $joinModelSourceFile->getJoinFromKey('element_id', $data['download']);
                                                $dirName = $db->escape(str_replace('/', '\\', $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML)));
                                            }

                                            $linkUFG = 'localhost:8080';

                                            if (strpos($linkFileXML, $linkUFG) !== false) {
                                                $arLinkFileXML = explode('/', $linkFileXML);

                                                $link = 'http://repositorio.bc.ufg.br/bitstream/ri/';

                                                $linkFileXML = $link . $arLinkFileXML[count($arLinkFileXML) - 3] . '/' . $arLinkFileXML[count($arLinkFileXML) - 2] . '/' . $arLinkFileXML[count($arLinkFileXML) - 1];
                                            }

                                            copy($linkFileXML, $dir);

                                            if ($objParamsFile->fu_make_pdf_thumb === '1') {
                                                $path_thumb = $path . '/' . $objParamsFile->thumb_dir . '/' . $nameIdentifier . '_' . basename($linkFileXML);
                                                $path_thumb = str_replace('.pdf', '.png', $path_thumb);

                                                if (!JFile::exists($path_thumb) && JFile::exists($dir)) {
                                                    $width_thumb = $objParamsFile->thumb_max_width;
                                                    $height_thumb = $objParamsFile->thumb_max_height;

                                                    $im = new Imagick($dir . '[0]');
                                                    $im->setImageFormat("png");
                                                    $im->setImageBackgroundColor(new ImagickPixel('white'));
                                                    $im->thumbnailImage($width_thumb, $height_thumb);
                                                    $im->writeImage($path_thumb);
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($data['extract'] !== 0) {
                                    $metas2 = $this->searchFileRepositoryOER($data['link'], $identifier);

                                    if ($metas2 !== false) {
                                        $path = dirname(dirname($_SERVER['SCRIPT_FILENAME']));

                                        for ($i = 0; $i < $metas2->length; $i++) {
                                            $tag = $metas2->item($i);

                                            if (($tag->nodeName == 'description') && ((strpos($tag->getAttribute('rdf:about'), '.pdf.txt') !== false))) {
                                                $linkFileXML = substr($tag->getAttribute('rdf:about'), 0, strpos($tag->getAttribute('rdf:about'), '.pdf.txt') + 8);
                                            }
                                        }

                                        $linkUFG = 'localhost:8080';

                                        if (strpos($linkFileXML, $linkUFG) !== false) {
                                            $arLinkFileXML = explode('/', $linkFileXML);

                                            $link = 'http://repositorio.bc.ufg.br/bitstream/ri/';

                                            $linkFile = $link . $arLinkFileXML[count($arLinkFileXML) - 3] . '/' . $arLinkFileXML[count($arLinkFileXML) - 2] . '/' . $arLinkFileXML[count($arLinkFileXML) - 1];
                                        }

                                        $textFile = trim($db->escape(file_get_contents($linkFile)));

                                        if ((strtotime($textFile) === 0) && (is_file($dir))) {
                                            $tikaAppPath = $path . '/plugins/fabrik_form/textextract/app/tika.jar';

                                            $command = ('java -jar ' . $tikaAppPath . ' "' . $dir . '" --text');
                                            exec($command, $execOutArray);
                                            $textFile = trim($db->escape(strip_tags(implode(' ', $execOutArray))));
                                        }

                                        $elementFile = $this->mappedElementsData($data['list'], $data['extract']);

                                        $fields .= ", {$elementFile->name}";
                                        $fieldsContent .= ", '{$textFile}'";

                                        if ($data['sync'] === 1) {
                                            $arFieldsElement[] = $elementFile->name;
                                            $arFieldsContent[] = $textFile;
                                        }
                                    }
                                }

                                $result_id = (int) $this->insertTableMultipleFieldsData($data['tableSource']->db_table_name, $fields, $fieldsContent);

                                if (is_array($arFields) && is_array($data['metadata']) && (!is_null($data['metadata']))) {
                                    foreach ($arFields as $key => $vlTarget) {
                                        foreach ($vlTarget as $vlRepeat) {
                                            $resultRepeat = $this->selectTableRepeat($tableRepeat[$key]->table_join, $tableRepeat[$key]->table_join_key, $tableRepeat[$key]->table_key, $result_id, $vlRepeat);

                                            if ($resultRepeat->total === '0') {
                                                $this->insertTableRepeat($tableRepeat[$key]->table_join, $tableRepeat[$key]->table_join_key, $tableRepeat[$key]->table_key, $result_id, $vlRepeat);
                                            }
                                        }
                                        unset($tableRepeat[$key]);
                                    }

                                    unset($arFields);
                                }

                                if (is_array($arFieldsTag) && is_array($data['metadata']) && (!is_null($data['metadata']))) {
                                    foreach ($arFieldsTag as $key => $vlTarget) {
                                        foreach ($vlTarget as $vlRepeat) {

                                            $resultRepeat = $this->selectTableRepeat($tableRepeatTag[$key]->table_join, $tableRepeatTag[$key]->table_join_key, $tableRepeatTag[$key]->table_key, $result_id, $vlRepeat);

                                            if ($resultRepeat->total === '0') {
                                                $this->insertTableRepeat($tableRepeatTag[$key]->table_join, $tableRepeatTag[$key]->table_join_key, $tableRepeatTag[$key]->table_key, $result_id, $vlRepeat);
                                            }
                                        }
                                        unset($tableRepeatTag[$key]);
                                    }

                                    unset($arFieldsTag);
                                }

                                if (($data['download'] !== 0) && ($metas2 !== false)) {
                                    $resultRepeat = $this->selectTableRepeatFileUpload($tableRepeatFile['file']->table_join, $tableRepeatFile['file']->table_join_key, $tableRepeatFile['file']->table_key, $result_id, $dirName);

                                    if ($resultRepeat->total === '0') {
                                        $this->insertTableRepeat($tableRepeatFile['file']->table_join, $tableRepeatFile['file']->table_join_key, $tableRepeatFile['file']->table_key, $result_id, $dirName);
                                    }
                                }
                            } elseif (!is_null($result_identifier->id) && ($update === 1) && ($data['sync'] === 1)) {
                                if (is_array($data['metadata']) && (!is_null($data['metadata']))) {
                                    $dom->loadHTML('<?xml encoding="utf-8" ?>' . trim($recordNode->metadata->asXML()));

                                    $i = 0;
                                    unset($arFields);
                                    unset($tableRepeat);
                                    unset($arFieldsTag);
                                    unset($tableRepeatTag);

                                    foreach ($data['metadata'] as $key => $objFields) {
                                        $tag = explode(':', $key);
                                        $metas = $dom->getElementsByTagName($tag[1]);

                                        $arLength = array_count_values($objFields);
                                        $j = 0;

                                        $fieldExtra = "";

                                        foreach ($objFields as $index => $value) {
                                            $joinModelSource = JModelLegacy::getInstance('Join', 'FabrikFEModel');
                                            $element = $this->mappedElementsData($data['list'], $value);
                                            $objParams = json_decode($element->params);

                                            if (strlen($fields) !== 0) {
                                                if ($j === 0) {
                                                    if (($objParams->database_join_display_type !== "checkbox") && ($objParams->database_join_display_type !== "multilist") && ($element->plugin !== 'tags')) {
                                                        $fields .= ", {$element->name}";
                                                        $itemFieldElement = 1;
                                                    } else {
                                                        $itemFieldElement = 0;
                                                    }
                                                } elseif (($arLength[$value] === 1) && ($objParams->database_join_display_type !== "checkbox") && ($objParams->database_join_display_type !== "multilist") && ($element->plugin !== 'tags')) {
                                                    $fields .= ", {$element->name}";
                                                    $itemFieldElement = 1;
                                                }
                                            } else {
                                                $fields .= "{$element->name}";
                                                $itemFieldElement = 1;
                                            }

                                            if (($data['sync'] === 1) && ($itemFieldElement === 1)) {
                                                $arFieldsElement[] = $element->name;
                                            }

                                            switch ($element->plugin) {
                                                case 'date':
                                                    $date = date("Y-m-d H:i:s", strtotime($db->escape($metas->item($index)->nodeValue)));

                                                    if (strlen($fieldsContent) !== 0) {
                                                        $fieldsContent .= ", '{$date}'";
                                                    } else {
                                                        $fieldsContent .= "'{$date}'";
                                                    }

                                                    if ($data['sync'] === 1) {
                                                        $arFieldsContent[] = $date;
                                                    }

                                                    if (($data['field2'] === 'dc:date') && ($data['sync'] === 1)) {
                                                        $dateSync = date("Y-m-d", strtotime($db->escape($metas->item($index)->nodeValue)));
                                                        $fieldSync = $element->name;
                                                    }

                                                    break;
                                                case 'dropdown':
                                                    $objOptions = $objParams->sub_options;

                                                    if (!(in_array($db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue))), $objOptions->sub_values)) &&
                                                            !(in_array($db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue))), $objOptions->sub_labels))) {
                                                        $objOptions->sub_values[] = ucwords(mb_strtolower($db->escape($metas->item($index)->nodeValue)));
                                                        $objOptions->sub_labels[] = ucwords(mb_strtolower($db->escape($metas->item($index)->nodeValue)));
                                                    }

                                                    $parans_update = $this->updateTableElement($value, $objParams);

                                                    if ($parans_update) {
                                                        if (strlen($fieldsContent) !== 0) {
                                                            $fieldsContent .= ", '{$db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)))}'";
                                                        } else {
                                                            $fieldsContent .= "'{$db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)))}'";
                                                        }
                                                    }

                                                    if ($data['sync'] === 1) {
                                                        $arFieldsContent[] = $db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)));
                                                    }

                                                    break;
                                                case 'databasejoin':
                                                    if ((($objParams->database_join_display_type === "dropdown") || ($objParams->database_join_display_type === "radio") ||
                                                            ($objParams->database_join_display_type === "auto-complete"))) {
                                                        $exist_data_target = $this->existTargetTableData($objParams->join_db_name, $objParams->join_val_column, $metas->item($index)->nodeValue);

                                                        if (count($exist_data_target) === 0) {
                                                            $result = $this->insertTable($objParams->join_db_name, $objParams->join_val_column, $metas->item($index)->nodeValue);
                                                        } else {
                                                            $result = $exist_data_target['id'];
                                                        }

                                                        if ((strlen($fieldsContent) !== 0) && ($result !== false)) {
                                                            $fieldsContent .= ", '{$result}'";
                                                        } elseif ((strlen($fieldsContent) === 0) && ($result !== false)) {
                                                            $fieldsContent .= "'{$result}'";
                                                        } elseif ((strlen($fieldsContent) === 0) && ($result === false)) {
                                                            $fieldsContent .= ",''";
                                                        } elseif ((strlen($fieldsContent) !== 0) && ($result === false)) {
                                                            $fieldsContent .= "''";
                                                        }

                                                        if ($data['sync'] === 1) {
                                                            $arFieldsContent[] = $result;
                                                        }
                                                    } elseif ((($objParams->database_join_display_type === "checkbox") || ($objParams->database_join_display_type === "multilist"))) {
                                                        $tableRepeat[$tag[1]] = $joinModelSource->getJoinFromKey('element_id', $value);

                                                        for ($numDBJ = 0; $numDBJ < $metas->length; $numDBJ++) {
                                                            if ($tag[1] === 'creator') {
                                                                $arTagText = explode(',', $metas->item($numDBJ)->nodeValue);
                                                                $tagText = trim($arTagText[1]) . ' ' . trim($arTagText[0]);
                                                            } else {
                                                                $tagText = trim($metas->item($numDBJ)->nodeValue);
                                                            }

                                                            $exist_data_target = $this->existTargetTableData($objParams->join_db_name, $objParams->join_val_column, $tagText);

                                                            if (count($exist_data_target) === 0) {
                                                                $arFields[$tag[1]][] = (int) $this->insertTable($objParams->join_db_name, $objParams->join_val_column, $tagText);
                                                            } else {
                                                                $arFields[$tag[1]][] = (int) $exist_data_target['id'];
                                                            }
                                                        }
                                                    }

                                                    break;
                                                case 'tags':
                                                    $nameTableTags = $ext . 'tags';

                                                    if ($objParams->tags_dbname === $nameTableTags) {
                                                        $tagSelestField = 'title';
                                                    }

                                                    $tableRepeatTag[$tag[1]] = $joinModelSource->getJoinFromKey('element_id', $value);

                                                    for ($numDBJ = 0; $numDBJ < $metas->length; $numDBJ++) {
                                                        $tagText = trim(ucfirst($metas->item($numDBJ)->nodeValue));

                                                        $exist_data_target = $this->existTargetTableData($objParams->tags_dbname, $tagSelestField, $tagText);

                                                        if ($objParams->tags_dbname === $nameTableTags) {
                                                            $tagTextExtra = $this->removeAccentsSpecialCharacters(trim(strtolower($metas->item($numDBJ)->nodeValue)));
                                                            date_default_timezone_set('America/Sao_Paulo');
                                                            $data['registerDate'] = date("Y-m-d H:i:s");

                                                            $user = JFactory::getUser();
                                                            $data['users'] = $user->get('id');

                                                            $tagInsertField = "`parent_id`, `level`, `path`, `title`, `alias`, `published`, `checked_out_time`, `access`, `created_user_id`,
                                             `created_time`, `modified_time`, `publish_up`, `publish_down`";

                                                            $tagInsertData = "'1','1','{$tagTextExtra}','{$tagText}','{$tagTextExtra}','1','{$data['registerDate']}','1','{$data['users']}',
                                            '{$data['registerDate']}','{$data['registerDate']}','{$data['registerDate']}','{$data['registerDate']}'";
                                                        }

                                                        if (count($exist_data_target) === 0) {
                                                            $arFieldsTag[$tag[1]][] = (int) $this->insertTableMultipleFieldsData($objParams->tags_dbname, $tagInsertField, $tagInsertData);
                                                        } else {
                                                            $arFieldsTag[$tag[1]][] = (int) $exist_data_target['id'];
                                                        }
                                                    }

                                                    break;
                                                default:
                                                    $num = $index + 1;

                                                    if (strlen($fieldsContent) !== 0) {
                                                        if ($j === 0) {
                                                            $fieldExtra .= $db->escape($metas->item($index)->nodeValue);
                                                        } elseif ($arLength[$value] === 1) {
                                                            $fieldExtra = $db->escape($metas->item($j)->nodeValue);
                                                        } else {
                                                            $fieldExtra .= '|' . $db->escape($metas->item($index)->nodeValue);
                                                        }

                                                        if (($num === $arLength[$value]) || ($arLength[$value] === 1)) {
                                                            $fieldsContent .= ", '{$fieldExtra}'";
                                                        }
                                                    } else {
                                                        $fieldExtra .= $db->escape($metas->item($index)->nodeValue);

                                                        if ($num === $arLength[$value]) {
                                                            $fieldsContent .= "'{$fieldExtra}'";
                                                        }
                                                    }

                                                    if ($data['sync'] === 1) {
                                                        $arFieldsContent[] = $fieldExtra;
                                                    }

                                                    break;
                                            }

                                            $j += 1;
                                        }

                                        $i += 1;
                                    }
                                }

                                if ($data['download'] !== 0) {
                                    $metas2 = $this->searchFileRepositoryOER($data['link'], $identifier);

                                    $dirName = '';
                                    unset($tableRepeatFile);

                                    if ($metas2 !== false) {
                                        $path = dirname(dirname($_SERVER['SCRIPT_FILENAME']));
                                        for ($i = 0; $i < $metas2->length; $i++) {
                                            $tag = $metas2->item($i);

                                            if (($tag->nodeName == 'description') && ((strpos($tag->getAttribute('rdf:about'), '.pdf') !== false) &&
                                                    (strpos($tag->getAttribute('rdf:about'), '.pdf.') === false))) {
                                                $linkFileXML = substr($tag->getAttribute('rdf:about'), 0, strpos($tag->getAttribute('rdf:about'), '.pdf') + 4);
                                            }
                                        }

                                        $file_ext = explode('.', $linkFileXML);

                                        if (count($file_ext) === 2) {
                                            $elementFile = $this->mappedElementsData($data['list'], $data['download']);
                                            $objParamsFile = json_decode($elementFile->params);

                                            $urlDirectory = $objParamsFile->ul_directory;

                                            $nameIdentifier = str_replace('/', '_', str_replace(':', '_', str_replace('.', '_', $identifier)));

                                            $dir = $path . $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML);

                                            if ($objParamsFile->ajax_upload === '0') {
                                                $fields .= ", {$elementFile->name}";

                                                $dirName = $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML);
                                                $fieldsContent .= ", '{$dirName}'";

                                                if ($data['sync'] === 1) {
                                                    $arFieldsElement[] = $elementFile->name;
                                                    $arFieldsContent[] = $dirName;
                                                }
                                            } elseif ($objParamsFile->ajax_upload === '1') {
                                                $joinModelSourceFile = JModelLegacy::getInstance('Join', 'FabrikFEModel');
                                                $tableRepeatFile['file'] = $joinModelSourceFile->getJoinFromKey('element_id', $data['download']);
                                                $dirName = $db->escape(str_replace('/', '\\', $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML)));
                                            }

                                            $linkUFG = 'localhost:8080';

                                            if (strpos($linkFileXML, $linkUFG) !== false) {
                                                $arLinkFileXML = explode('/', $linkFileXML);

                                                $link = 'http://repositorio.bc.ufg.br/bitstream/ri/';

                                                $linkFileXML = $link . $arLinkFileXML[count($arLinkFileXML) - 3] . '/' . $arLinkFileXML[count($arLinkFileXML) - 2] . '/' . $arLinkFileXML[count($arLinkFileXML) - 1];
                                            }

                                            copy($linkFileXML, $dir);

                                            if ($objParamsFile->fu_make_pdf_thumb === '1') {
                                                $path_thumb = $path . '/' . $objParamsFile->thumb_dir . '/' . $nameIdentifier . '_' . basename($linkFileXML);
                                                $path_thumb = str_replace('.pdf', '.png', $path_thumb);

                                                if (!JFile::exists($path_thumb) && JFile::exists($dir)) {
                                                    $width_thumb = $objParamsFile->thumb_max_width;
                                                    $height_thumb = $objParamsFile->thumb_max_height;

                                                    $im = new Imagick($dir . '[0]');
                                                    $im->setImageFormat("png");
                                                    $im->setImageBackgroundColor(new ImagickPixel('white'));
                                                    $im->thumbnailImage($width_thumb, $height_thumb);
                                                    $im->writeImage($path_thumb);
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($data['extract'] !== 0) {
                                    $metas2 = $this->searchFileRepositoryOER($data['link'], $identifier);

                                    if ($metas2 !== false) {
                                        $path = dirname(dirname($_SERVER['SCRIPT_FILENAME']));

                                        for ($i = 0; $i < $metas2->length; $i++) {
                                            $tag = $metas2->item($i);

                                            if (($tag->nodeName == 'description') && ((strpos($tag->getAttribute('rdf:about'), '.pdf.txt') !== false))) {
                                                $linkFileXML = substr($tag->getAttribute('rdf:about'), 0, strpos($tag->getAttribute('rdf:about'), '.pdf.txt') + 8);
                                            }
                                        }

                                        $linkUFG = 'localhost:8080';

                                        if (strpos($linkFileXML, $linkUFG) !== false) {
                                            $arLinkFileXML = explode('/', $linkFileXML);

                                            $link = 'http://repositorio.bc.ufg.br/bitstream/ri/';

                                            $linkFile = $link . $arLinkFileXML[count($arLinkFileXML) - 3] . '/' . $arLinkFileXML[count($arLinkFileXML) - 2] . '/' . $arLinkFileXML[count($arLinkFileXML) - 1];
                                        }

                                        $textFile = trim($db->escape(file_get_contents($linkFile)));

                                        if ((strtotime($textFile) === 0) && (is_file($dir))) {
                                            $tikaAppPath = $path . '/plugins/fabrik_form/textextract/app/tika.jar';

                                            $command = ('java -jar ' . $tikaAppPath . ' "' . $dir . '" --text');
                                            exec($command, $execOutArray);
                                            $textFile = trim($db->escape(strip_tags(implode(' ', $execOutArray))));
                                        }

                                        $elementFile = $this->mappedElementsData($data['list'], $data['extract']);

                                        $fields .= ", {$elementFile->name}";
                                        $fieldsContent .= ", '{$textFile}'";

                                        if ($data['sync'] === 1) {
                                            $arFieldsElement[] = $elementFile->name;
                                            $arFieldsContent[] = $textFile;
                                        }
                                    }
                                }

                                $this->updateTableMultipleFieldsData($data['tableSource']->db_table_name, $result_identifier->id, $arFieldsElement, $arFieldsContent);

                                if (is_array($arFields) && is_array($data['metadata']) && (!is_null($data['metadata']))) {
                                    foreach ($arFields as $key => $vlTarget) {
                                        foreach ($vlTarget as $vlRepeat) {
                                            $resultRepeat = $this->selectTableRepeat($tableRepeat[$key]->table_join, $tableRepeat[$key]->table_join_key, $tableRepeat[$key]->table_key, $result_identifier->id, $vlRepeat);

                                            if ($resultRepeat->total === '0') {
                                                $this->insertTableRepeat($tableRepeat[$key]->table_join, $tableRepeat[$key]->table_join_key, $tableRepeat[$key]->table_key, $result_identifier->id, $vlRepeat);
                                            }
                                        }
                                        unset($tableRepeat[$key]);
                                    }

                                    unset($arFields);
                                }

                                if (is_array($arFieldsTag) && is_array($data['metadata']) && (!is_null($data['metadata']))) {
                                    foreach ($arFieldsTag as $key => $vlTarget) {
                                        foreach ($vlTarget as $vlRepeat) {

                                            $resultRepeat = $this->selectTableRepeat($tableRepeatTag[$key]->table_join, $tableRepeatTag[$key]->table_join_key, $tableRepeatTag[$key]->table_key, $result_identifier->id, $vlRepeat);

                                            if ($resultRepeat->total === '0') {
                                                $this->insertTableRepeat($tableRepeatTag[$key]->table_join, $tableRepeatTag[$key]->table_join_key, $tableRepeatTag[$key]->table_key, $result_identifier->id, $vlRepeat);
                                            }
                                        }
                                        unset($tableRepeatTag[$key]);
                                    }

                                    unset($arFieldsTag);
                                }

                                if (($data['download'] !== 0) && ($metas2 !== false)) {
                                    $resultRepeat = $this->selectTableRepeatFileUpload($tableRepeatFile['file']->table_join, $tableRepeatFile['file']->table_join_key, $tableRepeatFile['file']->table_key, $result_identifier->id, $dirName);

                                    if ($resultRepeat->total === '0') {
                                        $this->insertTableRepeat($tableRepeatFile['file']->table_join, $tableRepeatFile['file']->table_join_key, $tableRepeatFile['file']->table_key, $result_identifier->id, $dirName);
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $xmlNode = $xmlObj->ListRecords;
            }

            if (!$xmlNode->resumptionToken) {
                $totalRecords = $totalRecords + $currentRecords;
            } else {
                $resumptionToken = $xmlNode->resumptionToken;

                $currentRecords = $currentRecords - 1;
                $totalRecords = $totalRecords + $currentRecords;
            }

            $table = $ext . 'fabrik_harvesting';
            $this->updateDataTableSource($table, $totalRecords, $data['id'], 'page_xml');

            $fetchCounter = $fetchCounter + 1;
        }

        return true;
    }

    /**
     * Ajax method that will check if the link (address) of the repository is valid if it is really a repository.
     *
     * @throws Exception
     */
    public function repositoryValidation() {
        $app = JFactory::getApplication();

        $db = JFactory::getDbo();

        $link = $db->escape($app->input->getString("link"));

        $url = $link . "?verb=Identify";

        if (!simplexml_load_file($url)) {
            echo "0";

            $app->close();
        }
        $url = $link . "?verb=ListMetadataFormats";

        if (!simplexml_load_file($url)) {
            echo "0";

            $app->close();
        }
        $url = $link . "?verb=ListSets";

        if (!simplexml_load_file($url)) {
            echo "0";

            $app->close();
        }
        $url = $link . '?verb=ListRecords&metadataPrefix=oai_dc';

        if (!simplexml_load_file($url)) {
            echo "0";

            $app->close();
        }
        $url = $link . '?verb=ListRecords&metadataPrefix=ore';

        if (!simplexml_load_file($url)) {
            echo "0";

            $app->close();
        }
        echo "1";

        $app->close();
    }

    /**
     * Method that brings the data of all the elements that were mapped on the form.
     *
     * @param $id_form
     * @param $arIdElementMap
     * @return mixed
     */
    public function mappedElementsData($id_form, $arIdElementMap) {
        $db = JFactory::getDbo();

        try {
            $sql = "SELECT element.* FROM
                    #__fabrik_elements AS element
                    LEFT JOIN #__fabrik_formgroup AS fg ON element.group_id = fg.group_id
                    LEFT JOIN #__fabrik_lists AS list ON fg.form_id = list.form_id
                WHERE
                    list.published = 1 AND
                    list.form_id = {$id_form}  AND
                    element.id IN ($arIdElementMap)";

            $db->setQuery($sql);

            return $db->loadObject();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Method that updates the table of elements with json already modified.
     *
     * @param $id_element
     * @param $element_params
     * @return bool
     */
    public function updateTableElement($id_element, $element_params) {
        $db = JFactory::getDbo();

        $paramsDB = json_encode($element_params);

        try {
            $db->transactionStart();

            $query1 = "UPDATE `#__fabrik_elements`
                    SET
                    `params` = '{$db->escape($paramsDB)}'
                    WHERE `id` = {$id_element};";

            $db->setQuery($query1);

            $db->execute();

            $db->transactionCommit();

            return true;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Method that inserts content in the fields in a given table passed by a parameter.
     *
     * @param $table_taget
     * @param $field_target
     * @param $data_target
     * @return bool|mixed
     */
    public function insertTable($table, $field, $data) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "INSERT INTO `{$table}` (`{$field}`) VALUES ('{$db->escape($data)}');";

            $db->setQuery($query);

            $db->execute();

            $id = $db->insertid();

            $db->transactionCommit();

            return $id;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return false;
        }
    }

    /**
     * Method that inserts into a table with multiple fields and multiple data.
     *
     * @param $table
     * @param $field
     * @param $data
     * @return bool|mixed
     */
    public function insertTableMultipleFieldsData($table, $field, $data) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "INSERT INTO `{$table}` ({$field}) VALUES ({$data});";

            $db->setQuery($query);

            $db->execute();

            $id = $db->insertid();

            $db->transactionCommit();

            return $id;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return '0';
        }
    }

    /**
     * Method that checks if there is data in a repetition table.
     *
     * @param $table
     * @param $parent_id
     * @param $target
     * @param $parent_data
     * @param $target_data
     * @return mixed
     */
    public function selectTableRepeat($table, $parent_id, $target, $parent_data, $target_data) {
        $db = JFactory::getDbo();

        try {
            $sql = "SELECT
                    COUNT(repeat.id) AS total
                FROM
                    {$table} AS `repeat`
                WHERE
                    repeat.{$parent_id} = {$parent_data}
                 AND
                    repeat.{$target} = {$target_data}";

            $db->setQuery($sql);

            return $db->loadObject();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Method that inserts its fields and data into a repetition table.
     *
     * @param $table
     * @param $field
     * @param $data
     * @return mixed|string
     */
    public function insertTableRepeat($table, $parent_id, $target, $parent_data, $target_data) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "INSERT INTO `{$table}` ({$parent_id}, $target) VALUES ('{$parent_data}','{$target_data}');";

            $db->setQuery($query);

            $db->execute();

            $id = $db->insertid();

            $db->transactionCommit();

            return $id;
        } catch (Exception $exc) {
            $db->transactionRollback();

            return '0';
        }
    }

    /**
     * Module that checks if there is already data in the database of the repetition structure of the plugins file upload.
     *
     * @param $table
     * @param $parent_id
     * @param $target
     * @param $parent_data
     * @param $target_data
     * @return mixed
     */
    public function selectTableRepeatFileUpload($table, $parent_id, $target, $parent_data, $target_data) {
        $db = JFactory::getDbo();

        try {
            $sql = "SELECT
                    COUNT(repeat.id) AS total
                FROM
                    {$table} AS `repeat`
                WHERE
                    repeat.{$parent_id} = {$parent_data}
                 AND
                    repeat.{$target} = '{$target_data}'";

            $db->setQuery($sql);

            return $db->loadObject();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * Ajax method that retrieves the data to be edited.
     * 
     * @throws Exception
     */
    public function editHarvesting() {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $id = $app->input->getInt("id");

        $sql = "SELECT
                    harvest.id, 
                    harvest.repository, 
                    harvest.list, 
                    harvest.dowload_file, 
                    harvest.extract, 
                    harvest.syncronism, 
                    harvest.field1, 
                    harvest.field2, 
                    harvest.map_header, 
                    harvest.map_metadata
                FROM
                    #__fabrik_harvesting AS harvest
                WHERE
                    harvest.id = {$id}";

        $db->setQuery($sql);

        $result = $db->loadObject();

        if (count($result) !== 0) {
            $data['id'] = $result->id;
            $data['repository'] = $result->repository;
            $data['list'] = $result->list;
            $data['dowload_file'] = $result->dowload_file;
            $data['extract'] = $result->extract;
            $data['syncronism'] = $result->syncronism;
            $data['field1'] = $result->field1;
            $data['field2'] = $result->field2;
            $data['map_header'] = json_decode($result->map_header);
            $data['map_metadata'] = json_decode($result->map_metadata);

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
                list.published = 1 AND
                fgroup.form_id = {$result->list}
                ORDER BY
                element.label ASC;";

            $db->setQuery($sql);

            $list = $db->loadObjectList();

            if (count($list) > 0) {
                $data['element'] = $list;
            }

            echo json_encode($data);
        } else {
            echo '0';
        }

        $app->close();
    }

    /**
     * Method that checks whether the data in the table exists.
     * @param $table
     * @param $field
     * @param $data
     * @return mixed
     */
    public function checkDataTableExist($table, $field, $data, $fieldDate = NULL, $date = NULL) {
        $db = JFactory::getDbo();

        try {
            $sql = "SELECT
                    `repeat`.id	AS id
                FROM
                    {$table} AS `repeat`
                WHERE
                    repeat.{$field} = '{$data}' ";

            if (($fieldDate !== NULL) && (strlen($fieldDate) !== 0)) {
                $sql .= "and DATE_FORMAT(repeat.{$fieldDate},'%Y-%m-%d') < '{$date}'";
            }

            $db->setQuery($sql);

            return $db->loadObject();
        } catch (Exception $exc) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR5');
            $type_message = 'warning';
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools';

            $this->setRedirect($site_message, $message, $type_message);
        }
    }

    /**
     * update data for multiple table fields
     *
     * @param $table
     * @param $field
     * @param $data
     * @return string
     */
    public function updateTableMultipleFieldsData($table, $id, $field, $data) {
        $db = JFactory::getDbo();

        try {
            $db->transactionStart();

            $query = "UPDATE `{$table}` SET ";

            foreach ($field as $key => $value) {
                if ($key === 0) {
                    $query .= "`{$value}` = '{$data[$key]}'";
                } else {
                    $query .= ", `{$value}` = '{$data[$key]}'";
                }
            }

            $query .= " WHERE `id` = {$id};";

            $db->setQuery($query);

            $db->execute();

            $db->transactionCommit();

            return '1';
        } catch (Exception $exc) {
            $db->transactionRollback();

            return '0';
        }
    }

    /**
     * Validating method or link provided by the user in the form.
     *
     * @param $link
     * @return bool
     */
    public function repositoryValidator($link) {
        $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR3');
        $type_message = 'warning';
        $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools&tab=2';

        $url = $link . '?verb=ListRecords&metadataPrefix=oai_dc';

        if (!simplexml_load_file($url)) {
            $this->setRedirect($site_message, $message, $type_message);

            return false;
        }
    }

    /**
     * Method that runs the button engine for the list of options already saved in the database.
     *
     * @param $data
     * @return bool
     */
    public function runListHarvesting($data) {
        set_time_limit(0);

        $db = JFactory::getDbo();
        $config = JFactory::getConfig();

        $ext = $config->get('dbprefix');

        $sql = "SELECT
                    harvest.*
                FROM
                    #__fabrik_harvesting AS harvest
                WHERE
                    harvest.id = {$data['id']}";

        $db->setQuery($sql);

        $harvesting = $db->loadObject();

        $data['tableSource'] = $this->tableSource($harvesting->list);

        $totalRecords = $harvesting->page_xml;
        $currentRecords = 0;

        $baseURL = $harvesting->repository . '?verb=ListRecords';

        $initialParams = '&resumptionToken=oai_dc////' . $totalRecords;

        $resumptionBase = '&resumptionToken=';
        $resumptionToken = 'initial';

        $fetchCounter = 1;

        while ($resumptionToken != '') {
            if ($fetchCounter == 1) {
                $url = $baseURL . $initialParams;
                $resumptionToken = '';
            } else {
                $url = $baseURL . $resumptionBase . $resumptionToken;
            }

            $xmlObj = simplexml_load_file($url);

            if ($xmlObj) {
                $xmlNode = $xmlObj->ListRecords;

                if ($fetchCounter === 1) {
                    $arNumLineXML = get_object_vars($xmlNode->resumptionToken);
                    $lineNum = (int) $arNumLineXML['@attributes']['completeListSize'];

                    $table = $ext . 'fabrik_harvesting';
                    $this->updateDataTableSource($table, $lineNum, $harvesting->id, 'line_num');
                }

                if ($xmlNode->count() !== 0) {
                    $currentRecords = count($xmlNode->children());

                    $dom = new DOMDocument();

                    $data['header'] = json_decode($harvesting->map_header);

                    $data['metadata'] = json_decode($harvesting->map_metadata);

                    foreach ($xmlNode->record as $recordNode) {
                        $fields = '';
                        $fieldsContent = '';
                        unset($arFieldsElement);
                        unset($arFieldsContent);

                        if (is_object($data['header']) && (!is_null($data['header']))) {

                            $dom->loadHTML('<?xml encoding="utf-8" ?>' . trim($recordNode->header->asXML()));
                            $i = 0;

                            foreach ($data['header'] as $key => $value) {
                                $metas = $dom->getElementsByTagName($key);


                                $element = $this->mappedElementsData($harvesting->list, $value);

                                if ($i !== 0) {
                                    $fields .= ", {$element->name}";
                                } else {
                                    $fields .= "{$element->name}";
                                }

                                if ($harvesting->syncronism === '1') {
                                    $arFieldsElement[] = $element->name;
                                }

                                switch ($element->plugin) {
                                    case 'date':
                                        $date = date("Y-m-d H:i:s", strtotime($db->escape($metas->item(0)->nodeValue)));

                                        if ($i !== 0) {
                                            $fieldsContent .= ", '{$date}'";
                                        } else {
                                            $fieldsContent .= "'{$date}'";
                                        }

                                        if ($harvesting->syncronism === '1') {
                                            $arFieldsContent[] = $date;
                                        }

                                        if (($harvesting->field2 === 'datestamp') && ($harvesting->syncronism === '1')) {
                                            $dateSync = date("Y-m-d", strtotime($db->escape($metas->item(0)->nodeValue)));
                                            ;
                                            $fieldSync = $element->name;
                                        }

                                        break;
                                    default:
                                        if ($i !== 0) {
                                            $fieldsContent .= ", '{$db->escape($metas->item(0)->nodeValue)}'";
                                        } else {
                                            $fieldsContent .= "'{$db->escape($metas->item(0)->nodeValue)}'";
                                        }

                                        if ($harvesting->syncronism === '1') {
                                            $arFieldsContent[] = $db->escape($metas->item(0)->nodeValue);
                                        }

                                        if ($key === 'identifier') {
                                            $identifier = $db->escape($metas->item(0)->nodeValue);
                                            $fieldIdentifier = $element->name;
                                        }

                                        break;
                                }

                                $i += 1;
                            }

                            if ($harvesting->syncronism === '1') {
                                $result_identifier = $this->checkDataTableExist($data['tableSource']->db_table_name, $fieldIdentifier, $identifier, $fieldSync, $dateSync);
                                $update = 1;
                            } else {
                                $result_identifier = $this->checkDataTableExist($data['tableSource']->db_table_name, $fieldIdentifier, $identifier);
                                $update = 0;
                            }

                            if (is_null($result_identifier->id) && ($update === 0)) {
                                if (is_object($data['metadata']) && (!is_null($data['metadata']))) {
                                    $dom->loadHTML('<?xml encoding="utf-8" ?>' . trim($recordNode->metadata->asXML()));

                                    $i = 0;
                                    unset($arFields);
                                    unset($tableRepeat);
                                    unset($arFieldsTag);
                                    unset($tableRepeatTag);

                                    foreach ($data['metadata'] as $key => $objFields) {
                                        $tag = explode(':', $key);
                                        $metas = $dom->getElementsByTagName($tag[1]);

                                        $arLength = array_count_values($objFields);
                                        $j = 0;

                                        $fieldExtra = "";

                                        foreach ($objFields as $index => $value) {
                                            $joinModelSource = JModelLegacy::getInstance('Join', 'FabrikFEModel');
                                            $element = $this->mappedElementsData($harvesting->list, $value);
                                            $objParams = json_decode($element->params);

                                            if (strlen($fields) !== 0) {
                                                if ($j === 0) {
                                                    if (($objParams->database_join_display_type !== "checkbox") && ($objParams->database_join_display_type !== "multilist") && ($element->plugin !== 'tags')) {
                                                        $fields .= ", {$element->name}";
                                                        $itemFieldElement = 1;
                                                    } else {
                                                        $itemFieldElement = 0;
                                                    }
                                                } elseif (($arLength[$value] === 1) && ($objParams->database_join_display_type !== "checkbox") && ($objParams->database_join_display_type !== "multilist") && ($element->plugin !== 'tags')) {
                                                    $fields .= ", {$element->name}";
                                                    $itemFieldElement = 1;
                                                }
                                            } else {
                                                $fields .= "{$element->name}";
                                                $itemFieldElement = 1;
                                            }

                                            if (($harvesting->syncronism === '1') && ($itemFieldElement === '1')) {
                                                $arFieldsElement[] = $element->name;
                                            }

                                            switch ($element->plugin) {
                                                case 'date':
                                                    $date = date("Y-m-d H:i:s", strtotime($db->escape($metas->item($index)->nodeValue)));

                                                    if (strlen($fieldsContent) !== 0) {
                                                        $fieldsContent .= ", '{$date}'";
                                                    } else {
                                                        $fieldsContent .= "'{$date}'";
                                                    }

                                                    if ($harvesting->syncronism === '1') {
                                                        $arFieldsContent[] = $date;
                                                    }

                                                    if (($harvesting->field2 === 'dc:date') && ($harvesting->syncronism === '1')) {
                                                        $dateSync = date("Y-m-d", strtotime($db->escape($metas->item($index)->nodeValue)));
                                                        $fieldSync = $element->name;
                                                    }

                                                    break;
                                                case 'dropdown':
                                                    $objOptions = $objParams->sub_options;

                                                    if (!(in_array($db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue))), $objOptions->sub_values)) &&
                                                            !(in_array($db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue))), $objOptions->sub_labels))) {
                                                        $objOptions->sub_values[] = ucwords(mb_strtolower($db->escape($metas->item($index)->nodeValue)));
                                                        $objOptions->sub_labels[] = ucwords(mb_strtolower($db->escape($metas->item($index)->nodeValue)));
                                                    }

                                                    $parans_update = $this->updateTableElement($value, $objParams);

                                                    if ($parans_update) {
                                                        if (strlen($fieldsContent) !== 0) {
                                                            $fieldsContent .= ", '{$db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)))}'";
                                                        } else {
                                                            $fieldsContent .= "'{$db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)))}'";
                                                        }
                                                    }

                                                    if ($harvesting->syncronism === '1') {
                                                        $arFieldsContent[] = $db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)));
                                                    }

                                                    break;
                                                case 'databasejoin':
                                                    if ((($objParams->database_join_display_type === "dropdown") || ($objParams->database_join_display_type === "radio") ||
                                                            ($objParams->database_join_display_type === "auto-complete"))) {
                                                        $exist_data_target = $this->existTargetTableData($objParams->join_db_name, $objParams->join_val_column, $metas->item($index)->nodeValue);

                                                        if (count($exist_data_target) === 0) {
                                                            $result = $this->insertTable($objParams->join_db_name, $objParams->join_val_column, $metas->item($index)->nodeValue);
                                                        } else {
                                                            $result = $exist_data_target['id'];
                                                        }

                                                        if ((strlen($fieldsContent) !== 0) && ($result !== false)) {
                                                            $fieldsContent .= ", '{$result}'";
                                                        } elseif ((strlen($fieldsContent) === 0) && ($result !== false)) {
                                                            $fieldsContent .= "'{$result}'";
                                                        } elseif ((strlen($fieldsContent) === 0) && ($result === false)) {
                                                            $fieldsContent .= ",''";
                                                        } elseif ((strlen($fieldsContent) !== 0) && ($result === false)) {
                                                            $fieldsContent .= "''";
                                                        }

                                                        if ($harvesting->syncronism === '1') {
                                                            $arFieldsContent[] = $result;
                                                        }
                                                    } elseif ((($objParams->database_join_display_type === "checkbox") || ($objParams->database_join_display_type === "multilist"))) {
                                                        $tableRepeat[$tag[1]] = $joinModelSource->getJoinFromKey('element_id', $value);

                                                        for ($numDBJ = 0; $numDBJ < $metas->length; $numDBJ++) {
                                                            if ($tag[1] === 'creator') {
                                                                $arTagText = explode(',', $metas->item($numDBJ)->nodeValue);
                                                                $tagText = trim($arTagText[1]) . ' ' . trim($arTagText[0]);
                                                            } else {
                                                                $tagText = trim($metas->item($numDBJ)->nodeValue);
                                                            }

                                                            $exist_data_target = $this->existTargetTableData($objParams->join_db_name, $objParams->join_val_column, $tagText);

                                                            if (count($exist_data_target) === 0) {
                                                                $arFields[$tag[1]][] = (int) $this->insertTable($objParams->join_db_name, $objParams->join_val_column, $tagText);
                                                            } else {
                                                                $arFields[$tag[1]][] = (int) $exist_data_target['id'];
                                                            }
                                                        }
                                                    }

                                                    break;
                                                case 'tags':
                                                    $nameTableTags = $ext . 'tags';

                                                    if ($objParams->tags_dbname === $nameTableTags) {
                                                        $tagSelestField = 'title';
                                                    }

                                                    $tableRepeatTag[$tag[1]] = $joinModelSource->getJoinFromKey('element_id', $value);

                                                    for ($numDBJ = 0; $numDBJ < $metas->length; $numDBJ++) {
                                                        $tagText = trim(ucfirst($metas->item($numDBJ)->nodeValue));

                                                        $exist_data_target = $this->existTargetTableData($objParams->tags_dbname, $tagSelestField, $tagText);

                                                        if ($objParams->tags_dbname === $nameTableTags) {
                                                            $tagTextExtra = $this->removeAccentsSpecialCharacters(trim(strtolower($metas->item($numDBJ)->nodeValue)));
                                                            date_default_timezone_set('America/Sao_Paulo');
                                                            $data['registerDate'] = date("Y-m-d H:i:s");

                                                            $user = JFactory::getUser();
                                                            $data['users'] = $user->get('id');

                                                            $tagInsertField = "`parent_id`, `level`, `path`, `title`, `alias`, `published`, `checked_out_time`, `access`, `created_user_id`,
										 `created_time`, `modified_time`, `publish_up`, `publish_down`";

                                                            $tagInsertData = "'1','1','{$tagTextExtra}','{$tagText}','{$tagTextExtra}','1','{$data['registerDate']}','1','{$data['users']}',
										'{$data['registerDate']}','{$data['registerDate']}','{$data['registerDate']}','{$data['registerDate']}'";
                                                        }

                                                        if (count($exist_data_target) === 0) {
                                                            $arFieldsTag[$tag[1]][] = (int) $this->insertTableMultipleFieldsData($objParams->tags_dbname, $tagInsertField, $tagInsertData);
                                                        } else {
                                                            $arFieldsTag[$tag[1]][] = (int) $exist_data_target['id'];
                                                        }
                                                    }

                                                    break;
                                                default:
                                                    $num = $index + 1;

                                                    if (strlen($fieldsContent) !== 0) {
                                                        if ($j === 0) {
                                                            $fieldExtra .= $db->escape($metas->item($index)->nodeValue);
                                                        } elseif ($arLength[$value] === 1) {
                                                            $fieldExtra = $db->escape($metas->item($j)->nodeValue);
                                                        } else {
                                                            $fieldExtra .= '|' . $db->escape($metas->item($index)->nodeValue);
                                                        }

                                                        if (($num === $arLength[$value]) || ($arLength[$value] === 1)) {
                                                            $fieldsContent .= ", '{$fieldExtra}'";
                                                        }
                                                    } else {
                                                        $fieldExtra .= $db->escape($metas->item($index)->nodeValue);

                                                        if ($num === $arLength[$value]) {
                                                            $fieldsContent .= "'{$fieldExtra}'";
                                                        }
                                                    }

                                                    if ($harvesting->syncronism === '1') {
                                                        $arFieldsContent[] = $fieldExtra;
                                                    }

                                                    break;
                                            }

                                            $j += 1;
                                        }

                                        $i += 1;
                                    }
                                }

                                if ($harvesting->dowload_file !== '0') {
                                    $metas2 = $this->searchFileRepositoryOER($harvesting->repository, $identifier);

                                    $dirName = '';
                                    unset($tableRepeatFile);

                                    if ($metas2 !== false) {
                                        $path = dirname(dirname($_SERVER['SCRIPT_FILENAME']));
                                        for ($i = 0; $i < $metas2->length; $i++) {
                                            $tag = $metas2->item($i);

                                            if (($tag->nodeName == 'description') && ((strpos($tag->getAttribute('rdf:about'), '.pdf') !== false) &&
                                                    (strpos($tag->getAttribute('rdf:about'), '.pdf.') === false))) {
                                                $linkFileXML = substr($tag->getAttribute('rdf:about'), 0, strpos($tag->getAttribute('rdf:about'), '.pdf') + 4);
                                            }
                                        }

                                        $file_ext = explode('.', $linkFileXML);

                                        if (count($file_ext) === 2) {
                                            $elementFile = $this->mappedElementsData($harvesting->list, $harvesting->dowload_file);
                                            $objParamsFile = json_decode($elementFile->params);

                                            $urlDirectory = $objParamsFile->ul_directory;

                                            $nameIdentifier = str_replace('/', '_', str_replace(':', '_', str_replace('.', '_', $identifier)));

                                            $dir = $path . $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML);

                                            if ($objParamsFile->ajax_upload === '0') {
                                                $fields .= ", {$elementFile->name}";

                                                $dirName = $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML);
                                                $fieldsContent .= ", '{$dirName}'";

                                                if ($harvesting->syncronism === '1') {
                                                    $arFieldsElement[] = $elementFile->name;
                                                    $arFieldsContent[] = $dirName;
                                                }
                                            } elseif ($objParamsFile->ajax_upload === '1') {
                                                $joinModelSourceFile = JModelLegacy::getInstance('Join', 'FabrikFEModel');
                                                $tableRepeatFile['file'] = $joinModelSourceFile->getJoinFromKey('element_id', $harvesting->dowload_file);
                                                $dirName = $db->escape(str_replace('/', '\\', $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML)));
                                            }

                                            $linkUFG = 'localhost:8080';

                                            if (strpos($linkFileXML, $linkUFG) !== false) {
                                                $arLinkFileXML = explode('/', $linkFileXML);

                                                $link = 'http://repositorio.bc.ufg.br/bitstream/ri/';

                                                $linkFileXML = $link . $arLinkFileXML[count($arLinkFileXML) - 3] . '/' . $arLinkFileXML[count($arLinkFileXML) - 2] . '/' . $arLinkFileXML[count($arLinkFileXML) - 1];
                                            }

                                            copy($linkFileXML, $dir);

                                            if ($objParamsFile->fu_make_pdf_thumb === '1') {
                                                $path_thumb = $path . '/' . $objParamsFile->thumb_dir . '/' . $nameIdentifier . '_' . basename($linkFileXML);
                                                $path_thumb = str_replace('.pdf', '.png', $path_thumb);

                                                if (!JFile::exists($path_thumb) && JFile::exists($dir)) {
                                                    $width_thumb = $objParamsFile->thumb_max_width;
                                                    $height_thumb = $objParamsFile->thumb_max_height;

                                                    $im = new Imagick($dir . '[0]');
                                                    $im->setImageFormat("png");
                                                    $im->setImageBackgroundColor(new ImagickPixel('white'));
                                                    $im->thumbnailImage($width_thumb, $height_thumb);
                                                    $im->writeImage($path_thumb);
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($harvesting->extract !== '0') {
                                    $metas2 = $this->searchFileRepositoryOER($harvesting->repository, $identifier);

                                    if ($metas2 !== false) {
                                        $path = dirname(dirname($_SERVER['SCRIPT_FILENAME']));

                                        for ($i = 0; $i < $metas2->length; $i++) {
                                            $tag = $metas2->item($i);

                                            if (($tag->nodeName == 'description') && ((strpos($tag->getAttribute('rdf:about'), '.pdf.txt') !== false))) {
                                                $linkFileXML = substr($tag->getAttribute('rdf:about'), 0, strpos($tag->getAttribute('rdf:about'), '.pdf.txt') + 8);
                                            }
                                        }

                                        $linkUFG = 'localhost:8080';

                                        if (strpos($linkFileXML, $linkUFG) !== false) {
                                            $arLinkFileXML = explode('/', $linkFileXML);

                                            $link = 'http://repositorio.bc.ufg.br/bitstream/ri/';

                                            $linkFile = $link . $arLinkFileXML[count($arLinkFileXML) - 3] . '/' . $arLinkFileXML[count($arLinkFileXML) - 2] . '/' . $arLinkFileXML[count($arLinkFileXML) - 1];
                                        }

                                        $textFile = trim($db->escape(file_get_contents($linkFile)));

                                        if ((strtotime($textFile) === 0) && (is_file($dir))) {
                                            $tikaAppPath = $path . '/plugins/fabrik_form/textextract/app/tika.jar';

                                            $command = ('java -jar ' . $tikaAppPath . ' "' . $dir . '" --text');
                                            exec($command, $execOutArray);
                                            $textFile = trim($db->escape(strip_tags(implode(' ', $execOutArray))));
                                        }

                                        $elementFile = $this->mappedElementsData($harvesting->list, $harvesting->extract);

                                        $fields .= ", {$elementFile->name}";
                                        $fieldsContent .= ", '{$textFile}'";

                                        if ($harvesting->syncronism === '1') {
                                            $arFieldsElement[] = $elementFile->name;
                                            $arFieldsContent[] = $textFile;
                                        }
                                    }
                                }

                                $result_id = (int) $this->insertTableMultipleFieldsData($data['tableSource']->db_table_name, $fields, $fieldsContent);

                                if (is_array($arFields) && is_object($data['metadata']) && (!is_null($data['metadata']))) {
                                    foreach ($arFields as $key => $vlTarget) {
                                        foreach ($vlTarget as $vlRepeat) {
                                            $resultRepeat = $this->selectTableRepeat($tableRepeat[$key]->table_join, $tableRepeat[$key]->table_join_key, $tableRepeat[$key]->table_key, $result_id, $vlRepeat);

                                            if ($resultRepeat->total === '0') {
                                                $this->insertTableRepeat($tableRepeat[$key]->table_join, $tableRepeat[$key]->table_join_key, $tableRepeat[$key]->table_key, $result_id, $vlRepeat);
                                            }
                                        }
                                        unset($tableRepeat[$key]);
                                    }

                                    unset($arFields);
                                }

                                if (is_array($arFieldsTag) && is_object($data['metadata']) && (!is_null($data['metadata']))) {
                                    foreach ($arFieldsTag as $key => $vlTarget) {
                                        foreach ($vlTarget as $vlRepeat) {

                                            $resultRepeat = $this->selectTableRepeat($tableRepeatTag[$key]->table_join, $tableRepeatTag[$key]->table_join_key, $tableRepeatTag[$key]->table_key, $result_id, $vlRepeat);

                                            if ($resultRepeat->total === '0') {
                                                $this->insertTableRepeat($tableRepeatTag[$key]->table_join, $tableRepeatTag[$key]->table_join_key, $tableRepeatTag[$key]->table_key, $result_id, $vlRepeat);
                                            }
                                        }
                                        unset($tableRepeatTag[$key]);
                                    }

                                    unset($arFieldsTag);
                                }

                                if (($harvesting->dowload_file !== '0') && ($metas2 !== false)) {
                                    $resultRepeat = $this->selectTableRepeatFileUpload($tableRepeatFile['file']->table_join, $tableRepeatFile['file']->table_join_key, $tableRepeatFile['file']->table_key, $result_id, $dirName);

                                    if ($resultRepeat->total === '0') {
                                        $this->insertTableRepeat($tableRepeatFile['file']->table_join, $tableRepeatFile['file']->table_join_key, $tableRepeatFile['file']->table_key, $result_id, $dirName);
                                    }
                                }
                            } elseif (!is_null($result_identifier->id) && ($update === 1) && ($harvesting->syncronism === '1')) {
                                if (is_object($data['metadata']) && (!is_null($data['metadata']))) {
                                    $dom->loadHTML('<?xml encoding="utf-8" ?>' . trim($recordNode->metadata->asXML()));

                                    $i = 0;
                                    unset($arFields);
                                    unset($tableRepeat);
                                    unset($arFieldsTag);
                                    unset($tableRepeatTag);

                                    foreach ($data['metadata'] as $key => $objFields) {
                                        $tag = explode(':', $key);
                                        $metas = $dom->getElementsByTagName($tag[1]);

                                        $arLength = array_count_values($objFields);
                                        $j = 0;

                                        $fieldExtra = "";

                                        foreach ($objFields as $index => $value) {
                                            $joinModelSource = JModelLegacy::getInstance('Join', 'FabrikFEModel');
                                            $element = $this->mappedElementsData($harvesting->list, $value);
                                            $objParams = json_decode($element->params);

                                            if (strlen($fields) !== 0) {
                                                if ($j === 0) {
                                                    if (($objParams->database_join_display_type !== "checkbox") && ($objParams->database_join_display_type !== "multilist") && ($element->plugin !== 'tags')) {
                                                        $fields .= ", {$element->name}";
                                                        $itemFieldElement = 1;
                                                    } else {
                                                        $itemFieldElement = 0;
                                                    }
                                                } elseif (($arLength[$value] === 1) && ($objParams->database_join_display_type !== "checkbox") && ($objParams->database_join_display_type !== "multilist") && ($element->plugin !== 'tags')) {
                                                    $fields .= ", {$element->name}";
                                                    $itemFieldElement = 1;
                                                }
                                            } else {
                                                $fields .= "{$element->name}";
                                                $itemFieldElement = 1;
                                            }

                                            if (($harvesting->syncronism === '1') && ($itemFieldElement === 1)) {
                                                $arFieldsElement[] = $element->name;
                                            }

                                            switch ($element->plugin) {
                                                case 'date':
                                                    $date = date("Y-m-d H:i:s", strtotime($db->escape($metas->item($index)->nodeValue)));

                                                    if (strlen($fieldsContent) !== 0) {
                                                        $fieldsContent .= ", '{$date}'";
                                                    } else {
                                                        $fieldsContent .= "'{$date}'";
                                                    }

                                                    if ($harvesting->syncronism === '1') {
                                                        $arFieldsContent[] = $date;
                                                    }

                                                    if (($harvesting->field2 === 'dc:date') && ($harvesting->syncronism === '1')) {
                                                        $dateSync = date("Y-m-d", strtotime($db->escape($metas->item($index)->nodeValue)));
                                                        $fieldSync = $element->name;
                                                    }

                                                    break;
                                                case 'dropdown':
                                                    $objOptions = $objParams->sub_options;

                                                    if (!(in_array($db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue))), $objOptions->sub_values)) &&
                                                            !(in_array($db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue))), $objOptions->sub_labels))) {
                                                        $objOptions->sub_values[] = ucwords(mb_strtolower($db->escape($metas->item($index)->nodeValue)));
                                                        $objOptions->sub_labels[] = ucwords(mb_strtolower($db->escape($metas->item($index)->nodeValue)));
                                                    }

                                                    $parans_update = $this->updateTableElement($value, $objParams);

                                                    if ($parans_update) {
                                                        if (strlen($fieldsContent) !== 0) {
                                                            $fieldsContent .= ", '{$db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)))}'";
                                                        } else {
                                                            $fieldsContent .= "'{$db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)))}'";
                                                        }
                                                    }

                                                    if ($harvesting->syncronism === '1') {
                                                        $arFieldsContent[] = $db->escape(ucwords(mb_strtolower($metas->item($index)->nodeValue)));
                                                    }

                                                    break;
                                                case 'databasejoin':
                                                    if ((($objParams->database_join_display_type === "dropdown") || ($objParams->database_join_display_type === "radio") ||
                                                            ($objParams->database_join_display_type === "auto-complete"))) {
                                                        $exist_data_target = $this->existTargetTableData($objParams->join_db_name, $objParams->join_val_column, $metas->item($index)->nodeValue);

                                                        if (count($exist_data_target) === 0) {
                                                            $result = $this->insertTable($objParams->join_db_name, $objParams->join_val_column, $metas->item($index)->nodeValue);
                                                        } else {
                                                            $result = $exist_data_target['id'];
                                                        }

                                                        if ((strlen($fieldsContent) !== 0) && ($result !== false)) {
                                                            $fieldsContent .= ", '{$result}'";
                                                        } elseif ((strlen($fieldsContent) === 0) && ($result !== false)) {
                                                            $fieldsContent .= "'{$result}'";
                                                        } elseif ((strlen($fieldsContent) === 0) && ($result === false)) {
                                                            $fieldsContent .= ",''";
                                                        } elseif ((strlen($fieldsContent) !== 0) && ($result === false)) {
                                                            $fieldsContent .= "''";
                                                        }

                                                        if ($harvesting->syncronism === '1') {
                                                            $arFieldsContent[] = $result;
                                                        }
                                                    } elseif ((($objParams->database_join_display_type === "checkbox") || ($objParams->database_join_display_type === "multilist"))) {
                                                        $tableRepeat[$tag[1]] = $joinModelSource->getJoinFromKey('element_id', $value);

                                                        for ($numDBJ = 0; $numDBJ < $metas->length; $numDBJ++) {
                                                            if ($tag[1] === 'creator') {
                                                                $arTagText = explode(',', $metas->item($numDBJ)->nodeValue);
                                                                $tagText = trim($arTagText[1]) . ' ' . trim($arTagText[0]);
                                                            } else {
                                                                $tagText = trim($metas->item($numDBJ)->nodeValue);
                                                            }

                                                            $exist_data_target = $this->existTargetTableData($objParams->join_db_name, $objParams->join_val_column, $tagText);

                                                            if (count($exist_data_target) === 0) {
                                                                $arFields[$tag[1]][] = (int) $this->insertTable($objParams->join_db_name, $objParams->join_val_column, $tagText);
                                                            } else {
                                                                $arFields[$tag[1]][] = (int) $exist_data_target['id'];
                                                            }
                                                        }
                                                    }

                                                    break;
                                                case 'tags':
                                                    $nameTableTags = $ext . 'tags';

                                                    if ($objParams->tags_dbname === $nameTableTags) {
                                                        $tagSelestField = 'title';
                                                    }

                                                    $tableRepeatTag[$tag[1]] = $joinModelSource->getJoinFromKey('element_id', $value);

                                                    for ($numDBJ = 0; $numDBJ < $metas->length; $numDBJ++) {
                                                        $tagText = trim(ucfirst($metas->item($numDBJ)->nodeValue));

                                                        $exist_data_target = $this->existTargetTableData($objParams->tags_dbname, $tagSelestField, $tagText);

                                                        if ($objParams->tags_dbname === $nameTableTags) {
                                                            $tagTextExtra = $this->removeAccentsSpecialCharacters(trim(strtolower($metas->item($numDBJ)->nodeValue)));
                                                            date_default_timezone_set('America/Sao_Paulo');
                                                            $data['registerDate'] = date("Y-m-d H:i:s");

                                                            $user = JFactory::getUser();
                                                            $data['users'] = $user->get('id');

                                                            $tagInsertField = "`parent_id`, `level`, `path`, `title`, `alias`, `published`, `checked_out_time`, `access`, `created_user_id`,
										 `created_time`, `modified_time`, `publish_up`, `publish_down`";

                                                            $tagInsertData = "'1','1','{$tagTextExtra}','{$tagText}','{$tagTextExtra}','1','{$data['registerDate']}','1','{$data['users']}',
										'{$data['registerDate']}','{$data['registerDate']}','{$data['registerDate']}','{$data['registerDate']}'";
                                                        }

                                                        if (count($exist_data_target) === 0) {
                                                            $arFieldsTag[$tag[1]][] = (int) $this->insertTableMultipleFieldsData($objParams->tags_dbname, $tagInsertField, $tagInsertData);
                                                        } else {
                                                            $arFieldsTag[$tag[1]][] = (int) $exist_data_target['id'];
                                                        }
                                                    }

                                                    break;
                                                default:
                                                    $num = $index + 1;

                                                    if (strlen($fieldsContent) !== 0) {
                                                        if ($j === 0) {
                                                            $fieldExtra .= $db->escape($metas->item($index)->nodeValue);
                                                        } elseif ($arLength[$value] === 1) {
                                                            $fieldExtra = $db->escape($metas->item($j)->nodeValue);
                                                        } else {
                                                            $fieldExtra .= '|' . $db->escape($metas->item($index)->nodeValue);
                                                        }

                                                        if (($num === $arLength[$value]) || ($arLength[$value] === 1)) {
                                                            $fieldsContent .= ", '{$fieldExtra}'";
                                                        }
                                                    } else {
                                                        $fieldExtra .= $db->escape($metas->item($index)->nodeValue);

                                                        if ($num === $arLength[$value]) {
                                                            $fieldsContent .= "'{$fieldExtra}'";
                                                        }
                                                    }

                                                    if ($harvesting->syncronism === '1') {
                                                        $arFieldsContent[] = $fieldExtra;
                                                    }

                                                    break;
                                            }

                                            $j += 1;
                                        }

                                        $i += 1;
                                    }
                                }

                                if ($harvesting->dowload_file !== '0') {
                                    $metas2 = $this->searchFileRepositoryOER($harvesting->repository, $identifier);

                                    $dirName = '';
                                    unset($tableRepeatFile);

                                    if ($metas2 !== false) {
                                        $path = dirname(dirname($_SERVER['SCRIPT_FILENAME']));
                                        for ($i = 0; $i < $metas2->length; $i++) {
                                            $tag = $metas2->item($i);

                                            if (($tag->nodeName == 'description') && ((strpos($tag->getAttribute('rdf:about'), '.pdf') !== false) &&
                                                    (strpos($tag->getAttribute('rdf:about'), '.pdf.') === false))) {
                                                $linkFileXML = substr($tag->getAttribute('rdf:about'), 0, strpos($tag->getAttribute('rdf:about'), '.pdf') + 4);
                                            }
                                        }

                                        $file_ext = explode('.', $linkFileXML);

                                        if (count($file_ext) === 2) {
                                            $elementFile = $this->mappedElementsData($harvesting->list, $harvesting->dowload_file);
                                            $objParamsFile = json_decode($elementFile->params);

                                            $urlDirectory = $objParamsFile->ul_directory;

                                            $nameIdentifier = str_replace('/', '_', str_replace(':', '_', str_replace('.', '_', $identifier)));

                                            $dir = $path . $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML);

                                            if ($objParamsFile->ajax_upload === '0') {
                                                $fields .= ", {$elementFile->name}";

                                                $dirName = $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML);
                                                $fieldsContent .= ", '{$dirName}'";

                                                if ($harvesting->syncronism === '1') {
                                                    $arFieldsElement[] = $elementFile->name;
                                                    $arFieldsContent[] = $dirName;
                                                }
                                            } elseif ($objParamsFile->ajax_upload === '1') {
                                                $joinModelSourceFile = JModelLegacy::getInstance('Join', 'FabrikFEModel');
                                                $tableRepeatFile['file'] = $joinModelSourceFile->getJoinFromKey('element_id', $harvesting->dowload_file);
                                                $dirName = $db->escape(str_replace('/', '\\', $urlDirectory . $nameIdentifier . '_' . basename($linkFileXML)));
                                            }

                                            $linkUFG = 'localhost:8080';

                                            if (strpos($linkFileXML, $linkUFG) !== false) {
                                                $arLinkFileXML = explode('/', $linkFileXML);

                                                $link = 'http://repositorio.bc.ufg.br/bitstream/ri/';

                                                $linkFileXML = $link . $arLinkFileXML[count($arLinkFileXML) - 3] . '/' . $arLinkFileXML[count($arLinkFileXML) - 2] . '/' . $arLinkFileXML[count($arLinkFileXML) - 1];
                                            }

                                            copy($linkFileXML, $dir);

                                            if ($objParamsFile->fu_make_pdf_thumb === '1') {
                                                $path_thumb = $path . '/' . $objParamsFile->thumb_dir . '/' . $nameIdentifier . '_' . basename($linkFileXML);
                                                $path_thumb = str_replace('.pdf', '.png', $path_thumb);

                                                if (!JFile::exists($path_thumb) && JFile::exists($dir)) {
                                                    $width_thumb = $objParamsFile->thumb_max_width;
                                                    $height_thumb = $objParamsFile->thumb_max_height;

                                                    $im = new Imagick($dir . '[0]');
                                                    $im->setImageFormat("png");
                                                    $im->setImageBackgroundColor(new ImagickPixel('white'));
                                                    $im->thumbnailImage($width_thumb, $height_thumb);
                                                    $im->writeImage($path_thumb);
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($harvesting->extract !== '0') {
                                    $metas2 = $this->searchFileRepositoryOER($harvesting->repository, $identifier);

                                    if ($metas2 !== false) {
                                        $path = dirname(dirname($_SERVER['SCRIPT_FILENAME']));

                                        for ($i = 0; $i < $metas2->length; $i++) {
                                            $tag = $metas2->item($i);

                                            if (($tag->nodeName == 'description') && ((strpos($tag->getAttribute('rdf:about'), '.pdf.txt') !== false))) {
                                                $linkFileXML = substr($tag->getAttribute('rdf:about'), 0, strpos($tag->getAttribute('rdf:about'), '.pdf.txt') + 8);
                                            }
                                        }

                                        $linkUFG = 'localhost:8080';

                                        if (strpos($linkFileXML, $linkUFG) !== false) {
                                            $arLinkFileXML = explode('/', $linkFileXML);

                                            $link = 'http://repositorio.bc.ufg.br/bitstream/ri/';

                                            $linkFile = $link . $arLinkFileXML[count($arLinkFileXML) - 3] . '/' . $arLinkFileXML[count($arLinkFileXML) - 2] . '/' . $arLinkFileXML[count($arLinkFileXML) - 1];
                                        }

                                        $textFile = trim($db->escape(file_get_contents($linkFile)));

                                        if ((strtotime($textFile) === 0) && (is_file($dir))) {
                                            $tikaAppPath = $path . '/plugins/fabrik_form/textextract/app/tika.jar';

                                            $command = ('java -jar ' . $tikaAppPath . ' "' . $dir . '" --text');
                                            exec($command, $execOutArray);
                                            $textFile = trim($db->escape(strip_tags(implode(' ', $execOutArray))));
                                        }

                                        $elementFile = $this->mappedElementsData($harvesting->list, $harvesting->extract);

                                        $fields .= ", {$elementFile->name}";
                                        $fieldsContent .= ", '{$textFile}'";

                                        if ($harvesting->syncronism === '1') {
                                            $arFieldsElement[] = $elementFile->name;
                                            $arFieldsContent[] = $textFile;
                                        }
                                    }
                                }

                                $this->updateTableMultipleFieldsData($data['tableSource']->db_table_name, $result_identifier->id, $arFieldsElement, $arFieldsContent);

                                if (is_array($arFields) && is_object($data['metadata']) && (!is_null($data['metadata']))) {
                                    foreach ($arFields as $key => $vlTarget) {
                                        foreach ($vlTarget as $vlRepeat) {
                                            $resultRepeat = $this->selectTableRepeat($tableRepeat[$key]->table_join, $tableRepeat[$key]->table_join_key, $tableRepeat[$key]->table_key, $result_identifier->id, $vlRepeat);

                                            if ($resultRepeat->total === '0') {
                                                $this->insertTableRepeat($tableRepeat[$key]->table_join, $tableRepeat[$key]->table_join_key, $tableRepeat[$key]->table_key, $result_identifier->id, $vlRepeat);
                                            }
                                        }
                                        unset($tableRepeat[$key]);
                                    }

                                    unset($arFields);
                                }

                                if (is_array($arFieldsTag) && is_object($data['metadata']) && (!is_null($data['metadata']))) {
                                    foreach ($arFieldsTag as $key => $vlTarget) {
                                        foreach ($vlTarget as $vlRepeat) {

                                            $resultRepeat = $this->selectTableRepeat($tableRepeatTag[$key]->table_join, $tableRepeatTag[$key]->table_join_key, $tableRepeatTag[$key]->table_key, $result_identifier->id, $vlRepeat);

                                            if ($resultRepeat->total === '0') {
                                                $this->insertTableRepeat($tableRepeatTag[$key]->table_join, $tableRepeatTag[$key]->table_join_key, $tableRepeatTag[$key]->table_key, $result_identifier->id, $vlRepeat);
                                            }
                                        }
                                        unset($tableRepeatTag[$key]);
                                    }

                                    unset($arFieldsTag);
                                }

                                if (($harvesting->dowload_file !== '0') && ($metas2 !== false)) {
                                    $resultRepeat = $this->selectTableRepeatFileUpload($tableRepeatFile['file']->table_join, $tableRepeatFile['file']->table_join_key, $tableRepeatFile['file']->table_key, $result_identifier->id, $dirName);

                                    if ($resultRepeat->total === '0') {
                                        $this->insertTableRepeat($tableRepeatFile['file']->table_join, $tableRepeatFile['file']->table_join_key, $tableRepeatFile['file']->table_key, $result_identifier->id, $dirName);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!$xmlNode->resumptionToken) {
                $totalRecords = $totalRecords + $currentRecords;
            } else {
                $resumptionToken = $xmlNode->resumptionToken;

                $currentRecords = $currentRecords - 1;
                $totalRecords = $totalRecords + $currentRecords;

                $table = $ext . 'fabrik_harvesting';
                $this->updateDataTableSource($table, $totalRecords, $harvesting->id, 'page_xml');
            }

            $fetchCounter = $fetchCounter + 1;
        }

        return true;
    }

    /**
     * Method to check if the OER repository is valid and valid with meta data from xml.
     *
     * @param $link
     * @param $identifier
     * @return bool|DOMNodeList
     */
    public function searchFileRepositoryOER($link, $identifier) {
        $baseURL2 = $link . '?verb=GetRecord';
        $initialParams2 = '&metadataPrefix=ore&identifier=' . $identifier;

        $url2 = $baseURL2 . $initialParams2;

        $xmlObj2 = simplexml_load_file($url2);

        if (!$xmlObj2) {
            $message = JText::_('COM_FABRIK_EXCEPTION_MESSAGE_ERROR3');
            $site_message = JUri::base() . 'index.php?option=com_fabrik&view=administrativetools&tab=2';
            $type_message = 'warning';

            $this->setRedirect($site_message, $message, $type_message);

            return false;
        }

        $xmlObj1 = $xmlObj2->GetRecord;

        if ($xmlObj1->count() !== 0) {
            $xmlNode2 = $xmlObj2->GetRecord->record;

            $dom2 = new DOMDocument();

            $dom2->loadHTML('<?xml encoding="utf-8" ?>' . trim($xmlNode2->metadata->asXML()));

            return $dom2->getElementsByTagName('triples')->item(0)->childNodes;
        } else {
            return false;
        }
    }

    /**
     * Method that removes accented characters.
     *
     * @param $str
     * @return string|string[]
     */
    public function removeAccentsSpecialCharacters($str) {
        $comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú',
            'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
        $semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u',
            'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U');

        return str_replace($comAcentos, $semAcentos, $str);
    }

}
