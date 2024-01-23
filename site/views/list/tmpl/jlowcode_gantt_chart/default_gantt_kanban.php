<?php
/**
 * Fabrik List Template: Default Gantt Chart
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

$tableName = $this->getModel()->getTable()->db_table_name;
$formPath = $this->addRecordLink;
$detailsPath = str_replace('/form/', '/details/', $formPath);
$user = JFactory::getUser();
$groups = $user->groups;
$drag = 'false';
if(in_array('8', $groups) || in_array('106', $groups) || in_array('108', $groups) || in_array('109', $groups)) {
    $drag = 'true';
}
?>

<link rel="stylesheet" href="components/com_fabrik/views/list/tmpl/educett_gantt_chart/jkanban.min.css" />
<style>
    .custom-button{
        border-radius: 4px;
        color: white;
        padding: 6px 10px 6px 10px;
        margin: 10px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        position: relative;
        transition: 0.5s;
        width: 35px;
    }

    .custom-button:after {
        text-align: center;
        content: 'Novo';
        position: absolute;
        opacity: 0;  
        top: 6px;
        right: -60px;
        transition: 0.5s;
    }

    .custom-button:hover{
        width: 75px;
        text-align: left;
    }

    .custom-button:hover:after {
        opacity: 1;
        right: 10px;
    }

    .chart-controls {
        text-align: center;
        margin-top: 20px;
        display: none;
    }

    .chart-controls .all-btn {
        font-size: 0.9rem;
        font-weight: 500;
        padding: 3px 20px;
        margin: 0 -1px;
        border: 1px solid #ccc;
        background-color: #eee;
        cursor: pointer;
    }

    .chart-controls .all-btn:hover {
        background: #ccc;
        transition: ease 0.2s;
    }

    #day-btn{
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }

    #month-btn{
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .popup-wrapper {
        width: 200px;
    }

    .details-container{
        padding: 0 10px 10px 10px;
    }

    .details-container h5{
        font-size: 16px;
    }

    .details-container p{
        margin: 3px 0;
    }

    #myKanban{
        margin: 20px auto;
    }

    .kanban-container{
        overflow-x: auto;
        display: flex;
        width: 100% !important; 
        align-items: flex-start;
    }

    .kanban-board{
        border-radius: 10px;
        min-width: 300px !important;
        display: inline-block;
        float: none;
    }

    .kanban-board footer{
        box-sizing: border-box;
    }

    .kanban-board header{
        border-top-right-radius: 10px;
        border-top-left-radius: 10px;
    }

    .kanban-drag{
        overflow-y: auto;
        max-height: 600px;
    }

    .kanban-drag::-webkit-scrollbar {
        width: 10px;
    }

    .kanban-drag::-webkit-scrollbar-thumb {
        background-color: #c2c2c2;
        border-radius:10px;
    }

    .kanban-title-board{
        color: white;
        font-weight: normal;
    }

    .drag_handler_icon{
        position: relative;
        width: 20px;
        height: 1px;
        top: 14px
    }

    .kanban-item div{
        cursor: pointer !important;
        margin-bottom: 10px;
    }

    .kanban-item div:hover{
        text-decoration: underline;
    }

    .kanban-button{
        padding: 6px 0px;
        margin-right: 8px;
        width: 60px;
        background-color: #e3e3e3;
        font-size: 13px;
        border-radius: 5px;
        border: none !important;
        color: #383838;
        display: none;
    }
    
    .kanban-button:hover{
        background-color: #c2c2c2;
    }

    .link{
        text-decoration: none;
        padding: 3px 6px;
        border: 1px solid #ccc;
        background: #ccc;
        border-radius: 3px;
        width: 100%;
        display: block;
        text-align: center;
        margin: 0 3px;
    }

    .link:hover{
        color: #ccc;
        background: none;
        transition: 0.3s;
    }

    .button-wrapper{
        display: flex;
    }

</style>

<script type="text/javascript" src="components/com_fabrik/views/list/tmpl/educett_gantt_chart/jkanban.js"></script>
<link rel="stylesheet" href="components/com_fabrik/views/list/tmpl/educett_gantt_chart/frappe-gantt.css" />
<script src="components/com_fabrik/views/list/tmpl/educett_gantt_chart/frappe-gantt.js"></script>
<script type="text/javascript">

function drawBoard(myBoards) {
    document.getElementById("chart-controls").style.display = "none";
    document.getElementById("chart_div").style.display = "none";
    document.getElementById("myKanban").style.display = "flex";

    if(!myBoards['vazio']) {
        var KanbanTest = new jKanban({
            element: "#myKanban",
            gutter: "10px",
            dragItems: <?php echo $drag; ?>, 
            responsivePercentage: false,
            itemHandleOptions:{
                enabled: <?php echo $drag; ?>,
            },
            itemAddOptions: {
                enabled: true,
                content: '+',
                class: 'custom-button',
                footer: true
            },
            buttonClick: function(el, boardId) {
                var idProjeto = document.querySelector("#edu_projtarefas___projeto_idvalue").value;
                let categoria = boardId.replace(/\D/g, '');
                var addRecordLink = "<?php echo str_replace('amp;', '', $this->addRecordLink); ?>";
                window.location.replace(addRecordLink + "?projeto_id=" + idProjeto + "&categoria_id=" +categoria);
            }
        });

        for(categoria in myBoards) {
            var idCategoria = myBoards[categoria]['0']['id_categoria'];
            var categoriaFormat = categoria.replace(/ /g, '_');
            var categoriaFormatId = categoriaFormat + '---' + idCategoria;
            
            KanbanTest.addBoards([
                {
                    id: categoriaFormatId,
                    title: categoria,
                    class: categoriaFormatId,
                }
            ]);

            var cor = myBoards[categoria]['0']['cor'];
            var elemento = document.querySelector('.' + categoriaFormatId);
            var customButton = document.querySelector('.' + categoriaFormatId + ' ~ footer .custom-button');

            customButton.style.background = 'rgb(' + cor + ')';
            customButton.style.border = '1px solid rgb(' + cor + ')';
            elemento.style.background = 'rgb(' + cor + ')';

            var kanbanBoards = document.querySelectorAll('.kanban-board');
            kanbanBoards.forEach(function(element,customButton) {
                element.style.marginBottom = '20px';  
            });
            
            for(task of myBoards[categoria]) {
                if(task.name) {
                    var titleTask = task.name.replace(' ', '_');
                    if(task.tarefa_pai != null){
                        var titleFormatTask = titleTask + '-Pai_' + task.tarefa_pai;
                    }else{
                        var titleFormatTask = titleTask;
                    }
                    
                    KanbanTest.addElement(categoriaFormatId, {
                        title: task.name,
                        id: titleFormatTask,
                        class:  task.custom_class,
                        id_tarefa: task.id,
                        id_categoria: task.id_categoria,
                        drop: function(el, target, source, sibling) {
                            var categoriaUpdate = target.parentElement.getAttribute('data-id').replace('_', ' ');
                            var idCategoriaUpdate = categoriaUpdate.split('---')[1];
                            var idTarefaUpdated = el.dataset.id_tarefa.replace('_', ' ');
                            var ajax = new XMLHttpRequest();
                            ajax.open("GET", "/components/com_fabrik/views/list/tmpl/educett_gantt_chart/update_kanban.php?type=categoriaUpdate&idCategoriaUpdate=" + idCategoriaUpdate + "&idTarefaUpdated=" + idTarefaUpdated, true);
                            ajax.onreadystatechange = function() {
                                if (ajax.readyState === 4 && ajax.status === 200) {
                                    if(ajax.responseText) {
                                        //sucesso
                                    } 
                                }
                            };
                            ajax.send();
                        },
                        click: function (el) {
                            var elemento = document.querySelector(".kanban-container");
                            var posicaoHorizontal = elemento.scrollLeft;

                            var idProjeto = document.querySelector("#edu_projtarefas___projeto_idvalue").value;

                            var elementosExpandidos = document.querySelectorAll('[data-class*="---expandido"]');

                            var IdArray = [];

                            elementosExpandidos.forEach(function(elemento) {
                                var dataIdValue = elemento.getAttribute('data-id_tarefa');
                                if(dataIdValue != el.dataset.id_tarefa){
                                    IdArray.push(dataIdValue);
                                }
                            });

                            if(el.dataset.class.indexOf("---expandido") < 0){
                                IdArray.push(el.dataset.id_tarefa);
                            }

                            var IDArray = JSON.stringify(IdArray);
                            var ajax = new XMLHttpRequest();
                            ajax.open("GET", "/components/com_fabrik/views/list/tmpl/educett_gantt_chart/update_kanban.php?type=loadChildren&IDArray=" + IDArray + "&idProjeto=" + idProjeto, true);
                            ajax.onreadystatechange = function() {
                                if (ajax.readyState === 4 && ajax.status === 200) {
                                    var myBoards = '';
                                    if(ajax.responseText) {
                                        myBoards = JSON.parse(ajax.responseText);
                                        if(!myBoards) {
                                            alert("Erro ao carregar as tarefas filhas!");
                                        }else {
                                            let myKanban = document.getElementById("myKanban");
                                            myKanban.innerHTML = "";
                                            drawBoard(myBoards);
                                            document.querySelector(".kanban-container").scrollLeft = posicaoHorizontal; 
                                        }
                                    } 
                                }
                            };
                            ajax.send();
                        }          
                    });

                    var kanbanItems = document.querySelectorAll('.kanban-item:not(:has(.kanban-button))');
                    kanbanItems.forEach(function(item) {
                        var editButton = document.createElement('button');
                        editButton.textContent = 'Editar';
                        editButton.classList.add('kanban-button');

                        var viewButton = document.createElement('button');
                        viewButton.textContent = 'Ver';
                        viewButton.classList.add('kanban-button');

                        var dataIdTarefa = item.getAttribute('data-id_tarefa');

                        var editButtonUrl = '<?php echo $formPath ?>' + dataIdTarefa;
                        editButton.addEventListener('click', function() {
                            window.location.href = editButtonUrl;
                        });

                        var viewButtonUrl = '<?php echo $detailsPath ?>' + dataIdTarefa;
                        viewButton.addEventListener('click', function() {
                            window.location.href = viewButtonUrl;
                        });
                        
                        item.appendChild(viewButton);
                        item.appendChild(editButton);
                    });

                    var elements = document.querySelectorAll('.kanban-item');
                    elements.forEach(function(element) {
                        var dataClass = element.getAttribute('data-class');
                        var dataParts = dataClass.split('---');
                        if (dataParts.length === 3 || dataParts.length === 4) {
                            var colorString = dataParts[2];
                            var colorValues = colorString.split(',');
                            var red = parseInt(colorValues[0]);
                            var green = parseInt(colorValues[1]);
                            var blue = parseInt(colorValues[2]);
                            element.style.border = '2px solid rgb(' + red + ',' + green + ',' + blue + ')';
                        }

                        var buttons = element.querySelectorAll('.kanban-button');
                        buttons.forEach(function(button) {
                            element.addEventListener('mouseenter', function() {
                                button.style.display = 'inline-block';
                            });

                            element.addEventListener('mouseleave', function() {
                                button.style.display = 'none';
                            });
                        });
                    });

                    const dataAtual = new Date();
                    const dataFormatada = formatarData(dataAtual);
                    const itemID = task.id;
                    const kanbanItem = document.querySelector(`.kanban-item[data-id_tarefa="${itemID}"]`);
                    let kanbanDiv;

                    if(kanbanItem.querySelector('div')){
                        kanbanDiv = kanbanItem.querySelectorAll('div')[1];
                    }else{
                        kanbanDiv = kanbanItem;
                    }
                    

                    if(task.end < dataFormatada && task.progress != '100' && task.progress != 'Continua' && task.progress != 'Nao se aplica'){
                        kanbanDiv.style.color = "red";
                    }
                }

                function formatarData(data) {
                    const ano = data.getFullYear();
                    const mes = String(data.getMonth() + 1).padStart(2, '0');
                    const dia = String(data.getDate()).padStart(2, '0');
                    const horas = String(data.getHours()).padStart(2, '0');
                    const minutos = String(data.getMinutes()).padStart(2, '0');
                    const segundos = String(data.getSeconds()).padStart(2, '0');

                    return `${ano}-${mes}-${dia} ${horas}:${minutos}:${segundos}`;
                }
            }
        }
    }

    var windowHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
    var kanbanDragElements = document.querySelectorAll('.kanban-drag');
    kanbanDragElements.forEach(function(kanbanDrag) {
        kanbanDrag.style.maxHeight = (windowHeight - 200) + 'px';
    });
}

function drawChart(tasks) {
    var ganttDiv = document.getElementById("chart_div");
    document.getElementById("myKanban").style.display = "none";
    document.getElementById("chart-controls").style.display = "flex";
    ganttDiv.style.display = "block";

    var viewMode = ganttDiv.className;
    if(viewMode == ""){
        viewMode = 'Month';
    }
    
    var gantt_chart = new Gantt("#chart_div", tasks, {
        view_mode: viewMode,
        date_format: 'DD-MM-YYYY',
        language: 'en',
        bar_height: 30,
        padding: 20,
        step: 24,
        on_click: (task) => {
            var elemento = document.querySelector(".gantt-container");
            var posicaoHorizontal = elemento.scrollLeft;

            var idProjeto = document.querySelector("#edu_projtarefas___projeto_idvalue").value;

            var elementosExpandidos = document.querySelectorAll('[class*="---expandido"]');

            var IdArray = [];

            elementosExpandidos.forEach(function(elemento) {
                var dataIdValue = elemento.getAttribute('data-id');
                if(dataIdValue != task.id){
                    IdArray.push(dataIdValue);
                }
            });

            if(task.custom_class.indexOf("---expandido") < 0){
                IdArray.push(task.id);
            }

            var IDArray = JSON.stringify(IdArray);
            var ajax = new XMLHttpRequest();
            ajax.open("GET", "/components/com_fabrik/views/list/tmpl/educett_gantt_chart/update_gantt.php?type=loadChildren&IDArray=" + IDArray + "&idProjeto=" + idProjeto + "&nomeTask=" + task.name, true);
            ajax.onreadystatechange = function() {
                if (ajax.readyState === 4 && ajax.status === 200) {
                    var tasks = '';
                    if(ajax.responseText) {
                        tasks = JSON.parse(ajax.responseText);
                        if(!tasks) {
                            alert("Erro ao carregar as tarefas filhas!");
                        }else {
                            let chart_div = document.getElementById("chart_div");
                            chart_div.innerHTML = "";
                            drawChart(tasks); 
                            document.querySelector(".gantt-container").scrollLeft = posicaoHorizontal; 
                        }
                    } 
                }
            };
            ajax.send();

           
        },
        on_date_change: (task, start, end) => {
            var viewMonth = document.querySelector("#month-btn.selected");
            if(viewMonth){
                var endFormat = end.getFullYear() + "-" + (end.getMonth()+1) + "-" + (end.getDate()+1);
            }else{
                var endFormat = end.getFullYear() + "-" + (end.getMonth()+1) + "-" + end.getDate();
            }
            var startFormat = start.getFullYear() + "-" + (start.getMonth()+1) + "-" + start.getDate();
            
            var ajax = new XMLHttpRequest();
            ajax.open("GET", "/components/com_fabrik/views/list/tmpl/educett_gantt_chart/update_gantt.php?type=updateDate&idTask=" + task.id + "&start=" + startFormat + "&end=" + endFormat + "&pai=" + task.tarefa_pai, true);
            ajax.onreadystatechange = function() {
                if (ajax.readyState === 4 && ajax.status === 200) {
                    var updatedGantt = '';
                    if(ajax.responseText) {
                        updatedGantt = JSON.parse(ajax.responseText);
                        if(!updatedGantt) {
                            alert("Erro ao atualizar data!");
                        }else {
                            alert("Data atualizada com sucesso!");
                            window.location.reload();
                        }
                    } 
                }
            };
            ajax.send();
        },
        on_progress_change: (task, progress) => {
            var ajax = new XMLHttpRequest();
            ajax.open("GET", "/components/com_fabrik/views/list/tmpl/educett_gantt_chart/update_gantt.php?type=updateProgress&idTask=" + task.id + "&progress=" + progress, true);
            ajax.onreadystatechange = function() {
                if (ajax.readyState === 4 && ajax.status === 200) {
                    var updatedGantt = '';
                    if(ajax.responseText) {
                        updatedGantt = JSON.parse(ajax.responseText);
                        if(!updatedGantt) {
                            alert("Erro ao atualizar progresso!");
                        }else{
                            alert("Progresso atualizado com sucesso!");
                            window.location.reload();
                        }
                    } 
                }
            };
            ajax.send();
        },
        custom_popup_html: function(task) {
            function addZero(number){
                if (number <= 9) 
                    return "0" + number;
                else
                    return number; 
            }
            let startFormat = (addZero(task._start.getDate().toString())) + "/" + (addZero(task._start.getMonth() + 1).toString()) + "/" + task._start.getFullYear();
            let endFormat = (addZero(task._end.getDate().toString())) + "/" + (addZero(task._end.getMonth() + 1).toString()) + "/" + task._end.getFullYear();
            let editLink = '<?php echo $formPath ?>' + task.id;
            let viewLink = '<?php echo $detailsPath ?>' + task.id;
            return `
            <div class="details-container">
                <h5>${task.name}</h5>
                <p>Data de início: ${startFormat}</p>
                 <p>Prazo final: ${endFormat}</p>
                <p>Progresso: ${task.progress}</p>
                <div class='button-wrapper'>
                    <a href='${editLink}' class='edit link'>Editar</a>
                    <a href='${viewLink}' class='view link'>Ver</a>
                </div>
            </div>
            `;
        }
    });

    var height = tasks.length * 50 + 80;
    document.querySelector(".gantt").style.height = height;
    document.querySelector(".gantt").style.maxWidth = 'none';
    var dayButton = document.querySelector(".chart-controls #day-btn");
    var weekButton = document.querySelector(".chart-controls #week-btn");
    var monthButton = document.querySelector(".chart-controls #month-btn");

    function textRed(){
        for(task of tasks) {
            function formatarData(data) {
                const ano = data.getFullYear();
                const mes = String(data.getMonth() + 1).padStart(2, '0');
                const dia = String(data.getDate()).padStart(2, '0');
                const horas = String(data.getHours()).padStart(2, '0');
                const minutos = String(data.getMinutes()).padStart(2, '0');
                const segundos = String(data.getSeconds()).padStart(2, '0');

                return `${ano}-${mes}-${dia} ${horas}:${minutos}:${segundos}`;
            }

            const dataAtual = new Date();
            const dataFormatada = formatarData(dataAtual);
            const itemID = task.id;
            const Item = document.querySelector(`g[data-id="${itemID}"] .bar-label`);

            if(task.end < dataFormatada && task.progress != '100' && task.progress != 'Continua' && task.progress != 'Nao se aplica'){
                Item.style.fill = "red";
            }
        }
    }

    function getColor(){
        for(task of tasks) {
            var cor = task.cor;
            var elementoPai = document.querySelectorAll('.' + task.custom_class);
            for(element of elementoPai){
                var elementoFilhoBar = element.querySelector('.bar');
                var elementoFilhoProgress = element.querySelector('.bar-progress');
                elementoFilhoBar.style.fill = 'rgb(' + cor + ')';
                elementoFilhoBar.style.opacity = '0.7';
                elementoFilhoProgress.style.fill = 'rgb(' + cor + ')';
            }
        }
    }

    getColor();
    textRed();

    dayButton.addEventListener("click", () => {
        dayButton.classList.add("selected");
        ganttDiv.classList.add("Day");

        weekButton.classList.remove("selected");
        monthButton.classList.remove("selected");
        ganttDiv.classList.remove("Week");
        ganttDiv.classList.remove("Month");

        gantt_chart.change_view_mode("Day");
        
        getColor();
        textRed();
    })
    weekButton.addEventListener("click", () => {
        weekButton.classList.add("selected");
        ganttDiv.classList.add("Week");
        
        dayButton.classList.remove("selected");
        monthButton.classList.remove("selected");
        ganttDiv.classList.remove("Day");
        ganttDiv.classList.remove("Month");

        gantt_chart.change_view_mode("Week");
        
        getColor();
        textRed();
    })
    monthButton.addEventListener("click", () => {
        monthButton.classList.add("selected");
        ganttDiv.classList.add("Month");

        weekButton.classList.remove("selected");
        dayButton.classList.remove("selected");
        ganttDiv.classList.remove("Day");
        ganttDiv.classList.remove("Week");

        gantt_chart.change_view_mode("Month");
        
        getColor();
        textRed();
    })
}

function getTasksGantt() {
    var idProjeto = document.querySelector("#edu_projtarefas___projeto_idvalue").value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/components/com_fabrik/views/list/tmpl/educett_gantt_chart/get_tasks.php?idProjeto=" + idProjeto + "&layout=gantt&tableModel=" + "<?php echo $tableName; ?>" , true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var tasks = '';
            if(xhr.responseText) {
                tasks = JSON.parse(xhr.responseText);
            }
            drawChart(tasks);
        }
    };
    xhr.send();
}

function getTasksKanban() {
    var idProjeto = document.querySelector("#edu_projtarefas___projeto_idvalue").value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/components/com_fabrik/views/list/tmpl/educett_gantt_chart/get_tasks.php?idProjeto=" + idProjeto + "&layout=kanban&tableModel=" + "<?php echo $tableName; ?>" , true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var tasks = '';
            if(xhr.responseText) {
                tasks = JSON.parse(xhr.responseText);
            }
            drawBoard(tasks);
        }
    };
    xhr.send();
}

document.querySelector("#edu_projtarefas___projeto_idvalue").addEventListener("change", function() {
    let myKanban = document.getElementById("myKanban");
    myKanban.innerHTML = "";
    let chart_div = document.getElementById("chart_div");
    chart_div.innerHTML = "";

	const element = document.querySelector("#edu_projtarefas___projeto_idvalue");
    if(element.value != '') {
        document.getElementById("info-visualization").style.display = "initial";
        document.getElementById("myKanban").style.display = "flex";
        document.getElementById("chart_div").style.display = "block";
    } else {
        document.getElementById("info-visualization").style.display = "none ";
        document.getElementById("myKanban").style.display = "none ";
        document.getElementById("chart_div").style.display = "none ";
        document.getElementById("chart-controls").style.display = "none";
    }

    var idProjeto = document.querySelector("#edu_projtarefas___projeto_idvalue").value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/components/com_fabrik/views/list/tmpl/educett_gantt_chart/get_visualization_model.php?idProjeto=" + idProjeto, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var defaultView = '';
            if(xhr.responseText) {
                defaultView = JSON.parse(xhr.responseText);
                if(defaultView == 'kanban') {
                    elementView = document.getElementById("info-visualization");
                    elementView.value = 'kanban';
                    getTasksKanban();
                } else {
                    elementView = document.getElementById("info-visualization");
                    elementView.value = 'gantt';
                    getTasksGantt();
                }
            }
        }
    };
    xhr.send();
});

document.addEventListener("DOMContentLoaded", function() {
	const element = document.querySelector("#edu_projtarefas___projeto_idvalue");

    if(element.value == '') {
        document.getElementById("info-visualization").style.display = "none";
    }

	const event = new Event('change');
	element.dispatchEvent(event);
});

document.querySelector(".info-visualization").addEventListener("change", function() {
    let myKanban = document.getElementById("myKanban");
    myKanban.innerHTML = "";
    let chart_div = document.getElementById("chart_div");
    chart_div.innerHTML = "";

    elementView = document.getElementById("info-visualization");
    viewSelected = elementView.value;

    if(viewSelected == 'kanban') {
        getTasksKanban();
    } else {
        getTasksGantt();
    }
});

</script>

<div class="chart-controls" id="chart-controls">
    <div id="day-btn" class="all-btn">
        Dia
    </div>

    <div id="week-btn" class="all-btn">
        Semana
    </div>

    <div id="month-btn" class="all-btn selected">
        Mês
    </div>
</div>

<div id="myKanban"></div>
<div><div id="chart_div" style="margin-bottom:20px;margin-top:10px;"></div></div>