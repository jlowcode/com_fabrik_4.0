<?php

define('_JEXEC', 1);

// defining the base path.
if (stristr( $_SERVER['SERVER_SOFTWARE'], 'win32' )) {
    define( 'JPATH_BASE', realpath(dirname(__FILE__).'\..\..\..\..\..\..' ));
} else {
    define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../..' ));
}

define('DS', DIRECTORY_SEPARATOR);

// including the main joomla files
require_once(JPATH_BASE.'/includes/defines.php');
require_once(JPATH_BASE.'/includes/framework.php');

// Creating an app instance 
$app = JFactory::getApplication('site');

$app->initialise();
jimport('joomla.user.user');
jimport('joomla.user.helper');

$user = JFactory::getUser();
$groups = $user->groups;

$idProjeto =  $_GET['idProjeto'];
$tableName = $_GET['tableModel'];
$layout = $_GET['layout'];

if(!$idProjeto) {
    return '';
}

$db = JFactory::getDbo();
$query = $db->getQuery(true)
    ->select('pt.' . $db->qn('id') . ' AS id')
    ->select('pt.' . $db->qn('categoria') . ' AS id_categoria')
    ->select('pt.' . $db->qn('name') . ' AS title')
    ->select('pt.' . $db->qn('data_inicio') . ' AS data_inicio')
    ->select('pt.' . $db->qn('prazo_final') . ' AS data_final')
    ->select('pt.' . $db->qn('estado') . ' AS estado')
    ->select('pt.' . $db->qn('tarefa_pai') . ' AS tarefa_pai')
    ->select('pt.' . $db->qn('pre_requisito') . ' AS pre_requisito')
    ->select('pt.' . $db->qn('progresso') . ' AS progresso')
    ->select('pt.' . $db->qn('created_by') . ' AS criador')
    ->select('pt.' . $db->qn('created_date') . ' AS data_criacao')
    ->select('pr.' . $db->qn('titulo') . ' AS categoria')
    ->select('pr.' . $db->qn('cor')  . ' AS cor')
    ->select('pt.' . $db->qn('prioridade')  . ' AS prioridade')
    ->select('pr.' . $db->qn('ordem')  . ' AS ordem')
    ->join('left', $db->qn('edu_projgestao_198_repeat') . ' AS pr ON pt.categoria = pr.id' )
    ->from($db->qn($tableName) . ' AS pt')
    ->where($db->qn('tarefa_pai') . ' IS NULL')
    ->where($db->qn('projeto_id') . ' = ' . $idProjeto)
    ->where('NOT (' . $db->qn('estado') . ' LIKE ' . $db->q('%Lixeira%') . ')')
    ->where('NOT (' . $db->qn('estado') . ' LIKE ' . $db->q('%Rascunho%') . ' AND ' . $db->qn('created_by') . ' != ' . $user->id . ')')
    ->order('data_inicio ASC');
$db->setQuery($query);

$tasks = $db->loadObjectList();

$arrTasks = Array();
foreach($tasks as $key => $value) {
    $formatTitle = str_replace(' ', '_', strtolower($value->title));
    $arrTasks[$key]['id'] = $value->id; 
    $arrTasks[$key]['id_categoria'] = $value->id_categoria;
    $arrTasks[$key]['prioridade'] = $value->prioridade; 
    $arrTasks[$key]['progress'] = $value->progresso; 
    $arrTasks[$key]['estado'] = $value->estado;   
    $arrTasks[$key]['criador'] = $value->criador;  
    $arrTasks[$key]['tarefa_pai'] = $value->tarefa_pai;   
    $arrTasks[$key]['name'] = $value->title;
    $arrTasks[$key]['categoria'] = $value->categoria;
    $arrTasks[$key]['cor'] = $value->cor;
    $arrTasks[$key]['start'] = $value->data_inicio;
    $arrTasks[$key]['end'] = $value->data_final;
    $arrTasks[$key]['ordem'] = $value->ordem;
    $arrTasks[$key]['dependencies'] = $value->pre_requisito;
    $arrTasks[$key]['custom_class'] = str_replace(' ', '_', $value->categoria) . '---' . $value->id_categoria;

    $subQuery = $db->getQuery(true)
                ->select('pt.' . $db->qn('name') . ' AS name')
                ->select('pt.' . $db->qn('estado') . ' AS estado')
                ->from($db->qn($tableName) . ' AS pt')
                ->where($db->qn('projeto_id') . ' = ' . $idProjeto)
                ->where($db->qn('tarefa_pai') . ' = ' . $value->id)
                ->order('data_inicio ASC');
    $db->setQuery($subQuery);
    $subs = $db->loadObjectList();

    $NonLixeiraSubtasks = false;

    foreach ($subs as $sub) {
        if ($sub->estado != 'Lixeira') {
            $NonLixeiraSubtasks = true;
            break;
        }
    }

    if (!empty($subs) && $NonLixeiraSubtasks) {
        $arrTasks[$key]['name'] = '(+) ' . $value->title;
    }
}

if($layout == 'kanban') {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true)
        ->select('ct.' . $db->qn('id') . ' AS id')
        ->select('ct.' . $db->qn('titulo') . ' AS title')
        ->select('ct.' . $db->qn('cor') . ' AS cor')
        ->select('ct.' . $db->qn('ordem') . ' AS ordem')
        ->from($db->qn('edu_projgestao_198_repeat') . ' AS ct')
        ->order('ct.' . $db->qn('ordem') . ' ASC')
        ->where('ct.parent_id = ' . $idProjeto);
    $db->setQuery($query);
    $categorias = $db->loadAssocList('ordem');

    $arrTasksByOrdens = Array();
    foreach($arrTasks as $task) {
        $arrTasksByOrdens[$task['ordem']][] = $task;
    }

    foreach($categorias as $name => $categoria) {
        if(!$arrTasksByOrdens[$name]) {
            $arrTasksByOrdens[$name][0]['id_categoria'] = $categoria['id'];
            $arrTasksByOrdens[$name][0]['cor'] = $categoria['cor'];
        }
    }

    ksort($arrTasksByOrdens, SORT_NUMERIC);

    $arrTasksByCategorias = Array();
    foreach($arrTasksByOrdens as $ordem => $tasksCat) {
        $arrTasksByCategorias[$categorias[$ordem]['title']] = $tasksCat;
    }

    if(!$tasks) {
        $arrTasksByCategorias['vazio'] = true;
    }

    $arrTasks = $arrTasksByCategorias;
}

echo json_encode($arrTasks);
?>  