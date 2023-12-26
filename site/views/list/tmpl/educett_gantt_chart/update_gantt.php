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

$idTask =  $_GET['idTask'];
$IDArray =  $_GET['IDArray'];
$pai = $_GET['pai'];
$progress = $_GET['progress'];
$type = $_GET['type'];
$start = $_GET['start'];
$end = $_GET['end'];
$idProjeto =  $_GET['idProjeto'];
$user = JFactory::getUser();

$startFormat = $start . ' 03:00:00';
$endFormat = $end . ' 03:00:00';

if($type == 'loadChildren'){  
    $tasksID = json_decode(urldecode($IDArray), true);
    $tasksIDString = implode('","', $tasksID);

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
        ->from($db->qn('edu_projtarefas') . ' AS pt')
        ->where('(' . $db->qn('tarefa_pai') . ' IS NULL OR ' . $db->qn('tarefa_pai') . ' IN ("' . $tasksIDString . '"))')
        ->where($db->qn('projeto_id') . ' = ' . $idProjeto)
        ->where('NOT (' . $db->qn('estado') . ' LIKE ' . $db->q('%Lixeira%') . ')')
        ->where('NOT (' . $db->qn('estado') . ' LIKE ' . $db->q('%Rascunho%') . ' AND ' . $db->qn('created_by') . ' != ' . $user->id . ')')
        ->order('data_inicio ASC');
    $db->setQuery($query);

    $tasks = $db->loadObjectList();

    $groupedTasks = array();
    foreach ($tasks as $task) {
        $groupedTasks[$task->id] = $task;
        getChildrenTasks($groupedTasks, $task->id, $idProjeto, $tasks);
    }

    foreach ($groupedTasks as $task) {
        if ($task->tarefa_pai !== null && array_key_exists($task->tarefa_pai, $groupedTasks)) {
            $parentTask = $groupedTasks[$task->tarefa_pai];
            $maxSubtaskDateFinal = new DateTime($parentTask->data_final);
            $taskDateFinal = new DateTime($task->data_final);

            if ($maxSubtaskDateFinal < $taskDateFinal) {
                $query = $db->getQuery(true)
                    ->update($db->qn('edu_projtarefas')) 
                    ->set($db->qn('prazo_final') . ' = ' . $db->q($task->data_final))
                    ->where($db->qn('id') . ' = ' . $parentTask->id);
                $db->setQuery($query);
                $db->execute();
            }
        }
    }

    $tasks = array_values($groupedTasks);
    $arrTasks = Array();
    foreach($tasks as $key => $value) {
        $formatTitle = str_replace(' ', '_', strtolower($value->title));
        $arrTasks[$key]['id'] = $value->id; 
        $arrTasks[$key]['id_categoria'] = $value->id_categoria;
        $arrTasks[$key]['name'] = $value->title;
        $arrTasks[$key]['progress'] = $value->progresso; 
        $arrTasks[$key]['prioridade'] = $value->prioridade; 
        $arrTasks[$key]['estado'] = $value->estado;   
        $arrTasks[$key]['criador'] = $value->criador;  
        $arrTasks[$key]['tarefa_pai'] = $value->tarefa_pai;   
        $arrTasks[$key]['categoria'] = $value->categoria;
        $arrTasks[$key]['cor'] = $value->cor;
        $arrTasks[$key]['start'] = $value->data_inicio;
        $arrTasks[$key]['end'] = $value->data_final;
        $arrTasks[$key]['ordem'] = $value->ordem;
        $arrTasks[$key]['dependencies'] = $value->pre_requisito;
        $arrTasks[$key]['custom_class'] = str_replace(' ', '_', $value->categoria) . '---' . $value->id_categoria;

        $query = $db->getQuery(true)->clear()
            ->select('pt.' . $db->qn('name') . ' AS name')
            ->select('pt.' . $db->qn('estado') . ' AS estado')
            ->from($db->qn('edu_projtarefas') . ' AS pt')
            ->where($db->qn('projeto_id') . ' = ' . $idProjeto)
            ->where($db->qn('tarefa_pai') . ' = ' . $value->id)
            ->order('data_inicio ASC');
        $db->setQuery($query);
        $subs = $db->loadObjectList();

        $NonLixeiraSubtasks = false;

        foreach ($subs as $sub) {
            if ($sub->estado != 'Lixeira') {
                $NonLixeiraSubtasks = true;
                break;
            }
        }

        if (!empty($subs) && $NonLixeiraSubtasks) {
            if(in_array($value->id, $tasksID)){
                $arrTasks[$key]['custom_class'] = str_replace(' ', '_', $value->categoria) . '---' . $value->id_categoria . '---expandido';
                $arrTasks[$key]['name'] = '(-) ' . $value->title;
            }else{
                $arrTasks[$key]['custom_class'] = str_replace(' ', '_', $value->categoria) . '---' . $value->id_categoria;
                $arrTasks[$key]['name'] = '(+) ' . $value->title;
            }
        }
    }

    echo json_encode($arrTasks);
}

if($type == 'updateProgress') {
    if($progress <= 12){
        $progressFormat = '0';
    }else if($progress > 12 && $progress <= 37){
        $progressFormat = '25';
    }else if($progress > 37 && $progress <= 62){
        $progressFormat = '50';
    }else if($progress > 62 && $progress <= 87){
        $progressFormat = '75';
    }else{
        $progressFormat = '100';
    }

    $db = JFactory::getDbo();
    $query = $db->getQuery(true)
        ->update($db->qn('edu_projtarefas'))
        ->set($db->qn('progresso') . ' = ' . $db->q($progressFormat))
        ->where($db->qn('id') . ' = ' . $db->q($idTask));
    $db->setQuery($query);
    $sucess = $db->execute();
}

if($type == 'updateDate') {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true)
        ->update($db->qn('edu_projtarefas'))
        ->set($db->qn('data_inicio') . ' = ' . $db->q($startFormat))
        ->set($db->qn('prazo_final') . ' = ' . $db->q($endFormat))
        ->where($db->qn('id') . ' = ' . $db->q($idTask));
    $db->setQuery($query);
    $sucess = $db->execute();

    if($pai != "null"){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->qn('prazo_final'))
            ->from($db->qn('edu_projtarefas'))
            ->where($db->qn('id') . ' = ' . $db->q($pai));
        $db->setQuery($query);
        $task = $db->loadObjectList();

        $prazoTarefaPai = new DateTime($task[0]->prazo_final);
        $prazoSubtarefa = new DateTime($endFormat);

        if($prazoTarefaPai < $prazoSubtarefa){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->update($db->qn('edu_projtarefas'))
                ->set($db->qn('prazo_final') . ' = ' . $db->q($endFormat))
                ->where($db->qn('id') . ' = ' . $db->q($pai));
            $db->setQuery($query);
            $sucess = $db->execute(); 
        }   
    }else{
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->qn('id'))
            ->select($db->qn('prazo_final'))
            ->from($db->qn('edu_projtarefas'))
            ->where($db->qn('tarefa_pai') . ' = ' . $db->q($idTask));
        $db->setQuery($query);
        $subs = $db->loadObjectList();

        $prazoTarefaPai = new DateTime($endFormat);

        foreach($subs as $sub){
            $prazoSubtarefa = new DateTime($sub->prazo_final);

            if($prazoTarefaPai < $prazoSubtarefa){
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                    ->update($db->qn('edu_projtarefas'))
                    ->set($db->qn('prazo_final') . ' = ' . $db->q($endFormat))
                    ->where($db->qn('id') . ' = ' . $db->q($sub->id));
                $db->setQuery($query);
                $sucess = $db->execute(); 
            }  
        }
         
    }
}

if($type != 'loadChildren'){
    if($sucess) {
        echo json_encode(true);
    } else {
        echo json_encode(false); 
    }
}

function getChildrenTasks(&$groupedTasks, $id, $idProjeto, $tasks){
    $db = JFactory::getDbo();
    $query = $db->getQuery(true)->clear()
                ->select('pt.' . $db->qn('id') . ' AS id')
                ->select('pt.' . $db->qn('prazo_final') . ' AS data_final')
                ->join('left', $db->qn('edu_projgestao_198_repeat') . ' AS pr ON pt.categoria = pr.id' )
                ->from($db->qn('edu_projtarefas') . ' AS pt')
                ->where($db->qn('projeto_id') . ' = ' . $idProjeto)
                ->where($db->qn('tarefa_pai') . ' = ' . $id)
                ->order('data_inicio ASC');
    $db->setQuery($query);
    $subs = $db->loadObjectList();

    if(!empty($subs)) {
        foreach($subs as $sub) {
            if(!array_key_exists($sub->id, $groupedTasks)) {
                foreach($tasks as $taskGet) {
                    if($taskGet->id == $sub->id) {
                        $groupedTasks[$sub->id] = $taskGet;
                        getChildrenTasks($groupedTasks, $sub->id, $idProjeto, $tasks);
                    }
                }
            }
        }
    }
}

?>  