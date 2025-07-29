/**
* Ensure that Fabrik's loaded
*/

let groups_to_hide = ['.fb_el_edu_produtos___justificativa'];

const hideGroups = function (groups_to_hide) {
    groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
}

const showGroups = function (groups_to_show) {
    groups_to_show.forEach((element) => jQuery(element).removeClass('fabrikHide'));
}

const switchSituacao = function (aux) {
    switch (aux) {
        case "Em analise":
        case "Aprovado":;
            groups_to_hide = ['.fb_el_edu_produtos___justificativa'];
            hideGroups(groups_to_hide);
            break;
        case "Negado":
            groups_to_show = ['.fb_el_edu_produtos___justificativa'];
            showGroups(groups_to_show);
            break;
    }
}

setTimeout(() => {
    hideGroups(groups_to_hide);
    let initial_type = jQuery("#edu_produtos___situacao").val();
    if (initial_type == ''){
        initial_type = jQuery("#edu_produtos___situacao")[0].innerText;
    }
    switchSituacao(initial_type);
}, 100);

jQuery('#edu_produtos___situacao').on('change', function () {
    switchSituacao(this.value);
});

