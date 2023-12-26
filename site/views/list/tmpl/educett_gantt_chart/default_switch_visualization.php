<?php
/**
 * Bootstrap List Template - Switch Visualization
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.1
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<style>
.info-visualization {
    background-color: #032b43;
    border: none;
    color: white;
    padding: 0 16px;
    text-align: center;
    text-decoration: none;
    font-size: 15px;
	width:200px;
	height:30px;
	margin:10px 0;
	outline: none;
}
</style>

<select class="info-visualization" id='info-visualization' name="info-visualization">
	<option value="kanban">Quadro Kanban</option>
	<option value="gantt">Gráfico de Gantt</option>
</select>

<script>

function getVisualizationModel() {
    var idProjeto = document.querySelector("#edu_projtarefas___projeto_idvalue").value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/components/com_fabrik/views/list/tmpl/educett_gantt_chart/get_visualization_model.php?idProjeto=" + idProjeto, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var model = '';
            function isJSON(str) {
                try {
                    JSON.parse(str);
                    return true;
                } catch (error) {
                    return false;
                }
            }
            if(xhr.responseText && isJSON(xhr.responseText)) {
                model = JSON.parse(xhr.responseText);
            }
        }
    };
    xhr.send();
}
</script>
