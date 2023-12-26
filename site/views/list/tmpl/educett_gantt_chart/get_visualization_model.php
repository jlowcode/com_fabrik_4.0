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

$idProjeto =  $_GET['idProjeto'];

if(!$idProjeto) {
    return '';
}

$db = JFactory::getDbo();
$query = $db->getQuery(true)
    ->select($db->qn('visualizacao'))
    ->from($db->qn('edu_projgestao'))
    ->where($db->qn('id') . ' = ' . $idProjeto);
$db->setQuery($query);

$defaultView = $db->loadResult();

echo json_encode($defaultView);
?>  