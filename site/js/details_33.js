/**
* Ensure that Fabrik's loaded
*/
window.addEvent('fabrik.loaded', function () {


setTimeout(() => {
    let request_type = Fabrik.getBlock('details_33').elements.get('edu_patrimonios___rede').get('value');
    switchPatrimonio(request_type);
}, 500);

const hideGroups = function (groups_to_hide) {
    groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
}

const switchPatrimonio = function (aux) {
    switch (aux) {
        case "COTEC":
            groups_to_hide = ['.fb_el_edu_patrimonios___patri_sedi_ro'];
            hideGroups(groups_to_hide);
            break;
        case "EFG":
            groups_to_hide = ['.fb_el_edu_patrimonios___nmr_patri_ro'];
            hideGroups(groups_to_hide);
            break;
    }
}


});