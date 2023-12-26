/**
* Ensure that Fabrik's loaded
*/
window.addEvent('fabrik.loaded', function () {

    setTimeout(() => {
        let request_type = Fabrik.getBlock('details_87').elements.get('edu_produtos___situacao').get('value');
        switchSituacao(request_type);
    }, 500);
    
    const hideGroups = function (groups_to_hide) {
        groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
    }
    
    const switchSituacao = function (aux) {
        switch (aux) {
            case "Em análise":
            case "Em analise":
            case "Aprovado":
                groups_to_hide = ['.fb_el_edu_produtos___justificativa_ro'];
                hideGroups(groups_to_hide);
                break;
        }
    }
});