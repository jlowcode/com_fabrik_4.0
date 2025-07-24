/**
* Ensure that Fabrik's loaded
*/

let aux = "";
let groups_to_hide = ['.fb_el_edu_bolsistas___termo_justificativa'];

const hideGroups = function (groups_to_hide) {
    groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
}

const showGroups = function (groups_to_show) {
    groups_to_show.forEach((element) => jQuery(element).removeClass('fabrikHide'));
}

const tipoDeBolsista = function (tipo) {
    switch (tipo) {
        case 'Docente':
        case 'Discente':
        case 'Tecnico':
            groups_to_hide = ['.fb_el_edu_bolsistas___termo_justificativa']
            groups_to_show= ['.fb_el_edu_bolsistas___matricula', '.fb_el_edu_bolsistas___declaracao_vinculo','.fb_el_edu_bolsistas___instituicao']
            hideGroups(groups_to_hide);
            showGroups(groups_to_show);
            break;

        case 'Externo':
            groups_to_show = ['.fb_el_edu_bolsistas___termo_justificativa']
            groups_to_hide= ['.fb_el_edu_bolsistas___matricula', '.fb_el_edu_bolsistas___declaracao_vinculo','.fb_el_edu_bolsistas___instituicao']
            hideGroups(groups_to_hide);
            showGroups(groups_to_show);
            break;
    }
}

setTimeout(() => {
    hideGroups(groups_to_hide);

    let bolsista_inicial = jQuery("#edu_bolsistas___vinculacao").val();
    tipoDeBolsista(bolsista_inicial);

}, 100);

jQuery('#edu_bolsistas___vinculacao').on('change', function () {
    tipoDeBolsista(this.value);
});