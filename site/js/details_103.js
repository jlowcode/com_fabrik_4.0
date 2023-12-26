/**
* Ensure that Fabrik's loaded
*/
window.addEvent('fabrik.loaded', function () {

let groups_to_hide = ['.fb_el_edu_bolsistas___termo_justificativa_ro','.fb_el_edu_bolsistas___matricula_ro', '.fb_el_edu_bolsistas___declaracao_vinculo_ro','.fb_el_edu_bolsistas___instituicao_ro'];

setTimeout(() => {
    let tipo_bolsista = Fabrik.getBlock('details_103').elements.get('edu_bolsistas___vinculacao').get('value');
    
    hideGroups(groups_to_hide);
    tipoDeBolsista(tipo_bolsista);

}, 1000);

const showGroups = function (groups_to_show) {
    console.log("groups_to_show " + groups_to_show);
    groups_to_show.forEach((element) => jQuery(element).removeClass('fabrikHide'));
}

const hideGroups = function (groups_to_hide) {
    groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
}

const tipoDeBolsista = function (tipo) {
    switch (tipo) {
        case 'Docente':
        case 'Discente':
        case 'Tecnico':
            groups_to_show = ['.fb_el_edu_bolsistas___matricula_ro','.fb_el_edu_bolsistas___declaracao_vinculo_ro','.fb_el_edu_bolsistas___instituicao_ro']
            showGroups(groups_to_show);
            break;

        case 'Externo':
            groups_to_show = ['.fb_el_edu_bolsistas___termo_justificativa_ro']
            showGroups(groups_to_show);
            break;
    }
}

});