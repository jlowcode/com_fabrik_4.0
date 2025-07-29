/**
* Ensure that Fabrik's loaded
*/

const switchStatus = function (status) {
    switch (status) {
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
            break;
    }
}

const switchStatusValue = function (status) {
    initial_status = jQuery("#edu_alteracoes___status input[type='radio']:checked")
    switch (initial_status[0].value) {
        case 'Negado':
            jQuery(initial_status).parent().css("background-color", "#A41623");
            jQuery('.fabrikgrid_em_analise').removeAttr('style');
            jQuery('.fabrikgrid_aprovado').removeAttr('style');
            prestacaoContasEtapaeStatus();
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

setTimeout(() => {
    let initial_status = jQuery("#edu_alteracoes___status input[type='radio']:checked").val();

    if (initial_status == undefined){
        initial_status = jQuery("#edu_alteracoes___status")[0].innerText;
        switchStatus(initial_status);
    } else {
        switchStatusValue(initial_status);
    }
    
}, 100);

jQuery("*[id^='edu_alteracoes___status_input_']").on('click', function () {
    switchStatusChange(this);
});