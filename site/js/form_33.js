/**
* Ensure that Fabrik's loaded
*/

let groups_to_hide = ['.fb_el_edu_patrimonios___nmr_patri', '.fb_el_edu_patrimonios___patri_sedi'];

const hideGroups = function (groups_to_hide) {
    groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
}

const showGroups = function (groups_to_show) {
    groups_to_show.forEach((element) => jQuery(element).removeClass('fabrikHide'));
}

const switchPatrimonio = function (aux) {
    switch (aux) {
        case "COTEC":
            groups_to_show = ['.fb_el_edu_patrimonios___nmr_patri'];
            showGroups(groups_to_show);
            groups_to_hide = ['.fb_el_edu_patrimonios___patri_sedi'];
            hideGroups(groups_to_hide);
            break;
        case "EFG":
            groups_to_show = ['.fb_el_edu_patrimonios___patri_sedi'];
            showGroups(groups_to_show);
            groups_to_hide = ['.fb_el_edu_patrimonios___nmr_patri'];
            hideGroups(groups_to_hide);
            break;
    }
}

setTimeout(() => {
    hideGroups(groups_to_hide);
    let initial_type = jQuery("#edu_patrimonios___rede").val();
    if (initial_type == ''){
        initial_type = jQuery("#edu_patrimonios___rede")[0].innerText;
    }
    switchPatrimonio(initial_type);
}, 100);

jQuery('#edu_patrimonios___rede').on('change', function () {
    switchPatrimonio(this.value);
});

