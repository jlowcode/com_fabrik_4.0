/**
* Ensure that Fabrik's loaded
*/

AddButton();

// Adding redirect button
function AddButton() {
    const elementTarget = jQuery('.fabrikButtonsContainer').children();
    var content = '';

    //Defining the element html
    const url = window.location.href;
    if(url.includes('/protocolo/concluidas')) {
        content = '<li style="padding-top: 10px"><div><i data-isicon="true" class="icon-list"></i><a style="margin-left:8px;" href="/protocolo">Protocolo</a></div></li>';
    } else {
        content = '<li style="padding-top: 10px; margin-left:12px;"><div><i data-isicon="true" class="icon-ok"></i><a style="margin-left:8px;" href="/protocolo/concluidas">Concluídas</a></div></li>';
    }

    elementTarget.append(content);
}