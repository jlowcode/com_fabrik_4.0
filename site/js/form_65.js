/**
* Ensure that Fabrik's loaded
*/

let aux = "";
let groups_to_hide = ['.fb_el_edu_recebimentos___protocolo_atual', '.fb_el_edu_recebimentos___protocolo_antigo'];

const hideGroups = function (groups_to_hide) {
    groups_to_hide.forEach((element) => jQuery(element).addClass('fabrikHide'));
}

const showGroups = function (groups_to_show) {
    groups_to_show.forEach((element) => jQuery(element).removeClass('fabrikHide'));
}

const switchProtocolo = function (aux) {
    switch (aux.value) {
        case "Atual":
            groups_to_hide = ['.fb_el_edu_recebimentos___protocolo_antigo'];
            groups_to_show = ['.fb_el_edu_recebimentos___protocolo_atual'];

            showGroups(groups_to_show);
            hideGroups(groups_to_hide);
            break;
        case "Antigo":
            groups_to_show = ['.fb_el_edu_recebimentos___protocolo_antigo'];
            groups_to_hide = ['.fb_el_edu_recebimentos___protocolo_atual'];

            showGroups(groups_to_show);
            hideGroups(groups_to_hide);
            break;
        case "Nao possui":
            groups_to_hide = ['.fb_el_edu_recebimentos___protocolo_atual', '.fb_el_edu_recebimentos___protocolo_antigo'];
            hideGroups(groups_to_hide);
            break;
        default:
            groups_to_hide = ['.fb_el_edu_recebimentos___protocolo_atual', '.fb_el_edu_recebimentos___protocolo_antigo'];
            hideGroups(groups_to_hide);
            break;
    }
}

setTimeout(() => {
    let initial_status = jQuery("#edu_recebimentos___protocolo input[type='radio']:checked")[0];

    if (initial_status){
        switchProtocolo(initial_status)
    } else {
        groups_to_hide = ['.fb_el_edu_recebimentos___protocolo_atual', '.fb_el_edu_recebimentos___protocolo_antigo'];
        hideGroups(groups_to_hide);
    }
}, 100);

jQuery("*[id^='edu_recebimentos___protocolo_input_']").on('click', function () {
    switchProtocolo(this);
});