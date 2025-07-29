/**
* Ensure that Fabrik's loaded
*/

let aux = "";
let groups_to_hide = ['.fb_el_edu_aditamentos___data_inicio','.fb_el_edu_aditamentos___data_termino','.fb_el_edu_aditamentos___carga_horaria','.fb_el_edu_aditamentos___valor_mensal','.fb_el_edu_aditamentos___parcelas','.fb_el_edu_aditamentos___valor_total'];

let groups_to_show = [];

const hideGroups = function (groups_to_hide) {
    groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
}

const showGroups = function (groups_to_show) {
    groups_to_show.forEach((element) => jQuery(element).removeClass('fabrikHide'));
}

const switchStatus = function (status) {
    switch (status) {
        case 'Negado':
            jQuery(status).parent().css("background-color", "#A41623");
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            break;

        case 'Em análise':
        case 'Em analise':
            jQuery(status).parent().css("background-color", "#ED7D3A");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            break;

        case 'Aprovado':
            jQuery(status).parent().css("background-color", "#136F63");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            break;
    }
}

const switchStatusValue = function (status) {
    initial_status = jQuery("#edu_aditamentos___status input[type='radio']:checked")
    switch (initial_status[0].value) {
        case 'Negado':
            jQuery(initial_status).parent().css("background-color", "#A41623");
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            break;

        case 'Em análise':
        case 'Em analise':
            jQuery(initial_status).parent().css("background-color", "#ED7D3A");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            break;

        case 'Aprovado':
            jQuery(initial_status).parent().css("background-color", "#136F63");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            break;
    }
}

const switchStatusChange = function (status) {
    switch (status.value) {
        case 'Negado':
            jQuery(status).parent().css("background-color", "#A41623");
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            prestacaoContasEtapaeStatus();
            break;

        case 'Em análise':
        case 'Em analise':
            jQuery(status).parent().css("background-color", "#ED7D3A");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            break;

        case 'Aprovado':
            jQuery(status).parent().css("background-color", "#136F63");
            jQuery('.fabrikgrid_negado').removeAttr('style');
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            prestacaoContasEtapaeStatus();
            break;
    }
}

const switchGroups = function (aux, groups_to_hide) {
    groups_to_hide = ['.fb_el_edu_aditamentos___data_inicio','.fb_el_edu_aditamentos___data_termino','.fb_el_edu_aditamentos___carga_horaria','.fb_el_edu_aditamentos___valor_mensal'];
    let groups_to_show = [];
    switch (aux) {
        case "Renovacao":
            groups_to_show = ['.fb_el_edu_aditamentos___data_inicio','.fb_el_edu_aditamentos___data_termino','.fb_el_edu_aditamentos___carga_horaria','.fb_el_edu_aditamentos___valor_mensal'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Alteracao da carga horaria":
            groups_to_show = ['.fb_el_edu_aditamentos___carga_horaria'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Alteracao da carga horaria e valor":
            groups_to_show = ['.fb_el_edu_aditamentos___carga_horaria','.fb_el_edu_aditamentos___valor_mensal'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            
            break;

        case "Alteracao do valor":
            groups_to_show = ['.fb_el_edu_aditamentos___valor_mensal'];
            showGroups(groups_to_show);

            groups_to_show.forEach((element) => groups_to_hide.splice(groups_to_hide.indexOf(element), 1));
            hideGroups(groups_to_hide);
            break;

        case "Encerramento":
        case " ":
            hideGroups(groups_to_hide);
            break;
    }
}

setTimeout(() => {
    hideGroups(groups_to_hide);
    let initial_status = jQuery("#edu_aditamentos___status input[type='radio']:checked").val();

    if (initial_status == undefined){
        initial_status = jQuery("#edu_aditamentos___status")[0].innerText;
        switchStatus(initial_status);
    } else {
        switchStatusValue(initial_status);
    }

    hideGroups(groups_to_hide);
    let initial_type = jQuery("#edu_aditamentos___acao").val();

    switchGroups(initial_type, groups_to_hide);

}, 100);

jQuery("*[id^='edu_aditamentos___status_input_']").on('click', function () {
    switchStatusChange(this);
});

jQuery('#edu_aditamentos___acao').on('change', function () {
    switchGroups(this.value, groups_to_hide);
});