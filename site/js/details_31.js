/**
* Ensure that Fabrik's loaded
*/
window.addEvent('fabrik.loaded', function () {


setTimeout(() => {
    let request_type = Fabrik.getBlock('details_31').elements.get('edu_beneficiarios___possui_cnh').get('value');
    debugger
    switchCNH(request_type);
}, 500);

const hideGroups = function (groups_to_hide) {
    groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
}

const switchCNH = function (aux) {
    switch (aux) {
        case "0":
            groups_to_hide = ['.fb_el_edu_beneficiarios___data_cnh_ro', '.fb_el_edu_beneficiarios___cnh_ro'];
            hideGroups(groups_to_hide);
            break;
    }
}

});