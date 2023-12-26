/**
* Ensure that Fabrik's loaded
*/


const hideGroups = function (groups_to_hide) {
    groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
}

const showGroups = function (groups_to_show) {
    groups_to_show.forEach((element) => jQuery(element).removeClass('fabrikHide'));
}

const switchCNH = function (aux) {
    switch (aux) {
        case "0":
            groups_to_hide = ['.fb_el_edu_beneficiarios___data_cnh', '.fb_el_edu_beneficiarios___cnh'];
            hideGroups(groups_to_hide);
            break;
        case "1":
            groups_to_show = ['.fb_el_edu_beneficiarios___data_cnh', '.fb_el_edu_beneficiarios___cnh'];
            showGroups(groups_to_show);
            break;
    }
}

setTimeout(() => {
    const initial_type = jQuery("#edu_beneficiarios___possui_cnh input[type='radio']:checked");
    switchCNH(initial_type[0].value);
}, 100);

jQuery("*[id^='edu_beneficiarios___possui_cnh_input_']").on('click', function () {
    switchCNH(this.value);
});

