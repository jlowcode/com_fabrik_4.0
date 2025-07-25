jQuery(document).ready(function () {
    jQuery("#list").change(function () {
        var idList = jQuery("#list").val();

        if (idList !== '0') {
            jQuery.post('./index.php?option=com_fabrik&task=mediamanager.listElementosImage', {
                idList: idList
            }, function (res) {
                if (res !== '0') {
                    var json = jQuery.parseJSON(res);

                    jQuery('#combo_element').html('<select form="listForm" id="element" name="element" ' +
                        'onchange="elementoImage(this.value,\'' + json[0].table + '\',this.options[this.options.selectedIndex].dataset)">' +
                        '<option value="0">Selecione o Elemento</option>' +
                        '</select>');

                    jQuery.each(json, function (index, value) {
                        if (value.paramList.list_search_elements === 'null') {
                            var list = value.paramList.list_search_elements;
                        } else {
                            var json_list = jQuery.parseJSON(value.paramList.list_search_elements);
                            var list = json_list.search_elements;
                        }

                        jQuery('#element').append('<option data-ajax="' + value.params.ajax_upload + '" data-plugin="' + value.plugin + '" ' +
                            'data-thumb_dir="' + value.params.thumb_dir + '" data-crop_dir="' + value.params.fileupload_crop_dir + '" ' +
                            'data-thumb_active="' + value.params.make_thumbnail + '" data-crop_active="' + value.params.fileupload_crop + '"' +
                            'data-search="' + list + '" value="' + value.name + '">' + value.label + '</option>');
                    });

                    clearImageArea();
                } else {
                    jQuery('#combo_element').html('Não tem imagem');
                    clearImageArea();
                }
            });
        } else {
            jQuery('#combo_element').html('');
            clearImageArea();
        }
    });
});

function elementoImage(value, table, type, start = 0, stop = 18) {
    if (value !== '0') {
        jQuery.post('./index.php?option=com_fabrik&task=mediamanager.listImage', {
            name: value, table: table, type: type.plugin, ajax_upload: type.ajax, start: start, stop: stop, search_field: type.search
        }, function (res) {
            if (res !== '0') {
                var json = jQuery.parseJSON(res);
                jQuery('#search').val('');
                jQuery('#ul_list_image').html(json.text);

                paginationListImages(value, table, type.plugin, type.ajax, json.total, start, stop, 1, type.search);

                if (type.thumb_active === '1') {
                    jQuery('#thumb').attr('onClick', 'javascript:btnElementImage(\'' + value + '\', \'' + table + '\', \'' + type.ajax + '\', \'' + type.thumb_dir + '\', ' + start + ', ' + stop + ', \'' + type.search + '\');');
                    jQuery('#thumb').css({'display': 'block'});
                }

                if (type.crop_active === '1') {
                    jQuery('#crop').attr('onClick', 'javascript:btnElementImage(\'' + value + '\', \'' + table + '\', \'' + type.ajax + '\', \'' + type.crop_dir + '\', ' + start + ', ' + stop + ', \'' + type.search + '\');');
                    jQuery('#crop').css({'display': 'block'});
                }

                if (type.search !== 'null') {
                    jQuery('#btn_search').attr('onClick', 'javascript:searchElementImage(\'' + value + '\', \'' + table + '\', \'' + type.plugin + '\', \'' +
                        type.ajax + '\', \'' + start + '\', \'' + stop + '\', \'' + type.search + '\');');
                    jQuery('#search_pitt').css({'display': 'block'});
                }
            } else {
                clearImageArea();
            }
        });
    } else {
        clearImageArea();
    }
}

function searchElementImage(value, table, plugin, ajax, start, stop, search_field) {
    var search = jQuery('#search').val();

    jQuery.post('./index.php?option=com_fabrik&task=mediamanager.listImage', {
        name: value, table: table, type: plugin, ajax_upload: ajax, start: start, stop: stop, search_field: search_field, search: search
    }, function (res) {
        if (res !== '0') {
            var json = jQuery.parseJSON(res);
            jQuery('#ul_list_image').html(json.text);

            paginationListImages(value, table, plugin, ajax, json.total, start, stop, 1, search_field);
        } else {
            jQuery('#ul_list_image').html('');
        }
    });

}

function layoutMediaPitt(key) {
    jQuery("[id^='link_']").removeClass('selected');
    jQuery('#link_' + key).addClass('selected');
}

function clearImageArea() {
    jQuery('#ul_list_image').html('');
    jQuery('#thumb').removeAttr('onClick');
    jQuery('#crop').removeAttr('onClick');
    jQuery('#thumb').css({'display': 'none'});
    jQuery('#crop').css({'display': 'none'});
    jQuery('#search_pitt').css({'display': 'none'});
    jQuery('#pagination_pitt').html('');
    jQuery('#search').val('');
}

function btnElementImage(value, table, ajaxt, drive, start = 0, stop = 18, search_field) {
    var search = jQuery('#search').val();

    jQuery.post('./index.php?option=com_fabrik&task=mediamanager.btnElementImage', {
        name: value, table: table, ajax_upload: ajaxt, drive: drive, start: start, stop: stop, search_field: search_field, search: search
    }, function (res) {
        if (res !== '0') {
            var json = jQuery.parseJSON(res);
            jQuery('#ul_list_image').html(json.text);

            paginationListImagesBtn(value, table, drive, ajaxt, json.total, 0, 18, 1, search_field);
        } else {
            jQuery('#ul_list_image').html('');
        }
    });
}

function paginationListImagesBtn(value, table, drive, ajaxt, total, start = 0, stop = 18, page = 1, search_field) {
    var div_total = Math.ceil(total / stop);

    if (page === 1) {
        var text = '<li class="disabled"><span><span class="icon-backward icon-first" aria-hidden="true"></span></span></li>' +
            '<li class="disabled"><span><span class="icon-step-backward icon-previous" aria-hidden="true"></span></span></li>';
    } else {
        var page_previous = page - 1;
        var namber = (stop * page_previous) - stop;

        var text = '<li class="pag_li"><a title="Ir para Inicio" onclick="paginationListImagesItensBtn(\'' + value + '\',\'' + table + '\',\'' + drive + '\',' + ajaxt + ',' + total + ',0,' + stop + ',1, \'' + search_field + '\')">' +
            '<span class="icon-backward icon-first" aria-hidden="true"></span></a></li>' +
            '<li class="pag_li"><a title="Anterior" onclick="paginationListImagesItens(\'' + value + '\',\'' + table + '\',\'' + drive + '\',' + ajaxt + ',' + total + ', ' + namber + ',' + stop + ', ' + page_previous + ', \'' + search_field + '\')">' +
            '<span class="icon-step-backward icon-previous" aria-hidden="true"></span></a></li>';
    }

    for (num = 1; num <= div_total; num++) {
        if (num === page) {
            text += '<li class="active"><span aria-current="true" aria-label="Página ' + num + '">' + num + '</span></li>';
        } else {
            var namber = (stop * num) - stop;
            text += '<li class="pag_li">' +
                '<a title="Ir para a página ' + num + '" onclick="javascript:paginationListImagesItensBtn(\'' + value + '\',\'' + table + '\',\'' + drive + '\',' + ajaxt + ',' + total + ',' + namber + ',' + stop + ',' + num + ', \'' + search_field + '\');">' +
                num + '</a>' +
                '</li>';
        }
    }

    if (page === div_total) {
        text += '<li class="disabled"><span><span class="icon-step-forward icon-next" aria-hidden="true"></span></span></li>' +
            '<li class="disabled"><span><span class="icon-forward icon-last" aria-hidden="true"></span></span></li>';
    } else {
        var page_previous = page + 1;
        var namber = (stop * page_previous) - stop;
        var last = (stop * div_total) - stop;

        text += '<li class="pag_li"><a title="Ir para Próximo" onclick="paginationListImagesItensBtn(\'' + value + '\',\'' + table + '\',\'' + drive + '\',' + ajaxt + ',' + total + ', ' + namber + ',' + stop + ', ' + page_previous + ', \'' + search_field + '\')">' +
            '<span class="icon-step-forward icon-next" aria-hidden="true"></span></a></li>' +
            '<li class="pag_li"><a title="Anterior" onclick="paginationListImagesItensBtn(\'' + value + '\',\'' + table + '\',\'' + drive + '\',' + ajaxt + ',' + total + ', ' + last + ',' + stop + ', ' + div_total + ', \'' + search_field + '\')">' +
            '<span class="icon-forward icon-last" aria-hidden="true"></span></a></li>';
    }

    jQuery('#pagination_pitt').html(text);
}

function paginationListImagesItensBtn(value, table, drive, ajaxt, total, start, stop, page, search_field) {
    var search = jQuery('#search').val();

    jQuery.post('./index.php?option=com_fabrik&task=mediamanager.btnElementImage', {
        name: value, table: table, ajax_upload: ajaxt, drive: drive, start: start, stop: stop, search_field: search_field, search: search
    }, function (res) {
        if (res !== '0') {
            var json = jQuery.parseJSON(res);
            jQuery('#ul_list_image').html(json.text);

            paginationListImagesBtn(value, table, drive, ajaxt, total, start, stop, page, search_field);
        }
    });
}

function paginationListImages(value, table, plugin, ajaxt, total, start = 0, stop = 18, page = 1, search_field) {
    var div_total = Math.ceil(total / stop);

    if (page === 1) {
        var text = '<li class="disabled"><span><span class="icon-backward icon-first" aria-hidden="true"></span></span></li>' +
            '<li class="disabled"><span><span class="icon-step-backward icon-previous" aria-hidden="true"></span></span></li>';
    } else {
        var page_previous = page - 1;
        var namber = (stop * page_previous) - stop;

        var text = '<li class="pag_li"><a title="Ir para Inicio" onclick="paginationListImagesItens(\'' + value + '\',\'' + table + '\',\'' + plugin + '\',' + ajaxt + ',' + total + ',0,' + stop + ',1, \'' + search_field + '\')">' +
            '<span class="icon-backward icon-first" aria-hidden="true"></span></a></li>' +
            '<li class="pag_li"><a title="Anterior" onclick="paginationListImagesItens(\'' + value + '\',\'' + table + '\',\'' + plugin + '\',' + ajaxt + ',' + total + ', ' + namber + ',' + stop + ', ' + page_previous + ', \'' + search_field + '\')">' +
            '<span class="icon-step-backward icon-previous" aria-hidden="true"></span></a></li>';
    }

    for (num = 1; num <= div_total; num++) {
        if (num === page) {
            text += '<li class="active"><span aria-current="true" aria-label="Página ' + num + '">' + num + '</span></li>';
        } else {
            var namber = (stop * num) - stop;
            text += '<li class="pag_li">' +
                '<a title="Ir para a página ' + num + '" onclick="javascript:paginationListImagesItens(\'' + value + '\',\'' + table + '\',\'' + plugin + '\',' + ajaxt + ',' + total + ',' + namber + ',' + stop + ',' + num + ', \'' + search_field + '\');">' +
                num + '</a>' +
                '</li>';
        }
    }

    if (page === div_total) {
        text += '<li class="disabled"><span><span class="icon-step-forward icon-next" aria-hidden="true"></span></span></li>' +
            '<li class="disabled"><span><span class="icon-forward icon-last" aria-hidden="true"></span></span></li>';
    } else {
        var page_previous = page + 1;
        var namber = (stop * page_previous) - stop;
        var last = (stop * div_total) - stop;

        text += '<li class="pag_li"><a title="Ir para Próximo" onclick="paginationListImagesItens(\'' + value + '\',\'' + table + '\',\'' + plugin + '\',' + ajaxt + ',' + total + ', ' + namber + ',' + stop + ', ' + page_previous + ', \'' + search_field + '\')">' +
            '<span class="icon-step-forward icon-next" aria-hidden="true"></span></a></li>' +
            '<li class="pag_li"><a title="Anterior" onclick="paginationListImagesItens(\'' + value + '\',\'' + table + '\',\'' + plugin + '\',' + ajaxt + ',' + total + ', ' + last + ',' + stop + ', ' + div_total + ', \'' + search_field + '\')">' +
            '<span class="icon-forward icon-last" aria-hidden="true"></span></a></li>';
    }

    jQuery('#pagination_pitt').html(text);
}

function paginationListImagesItens(value, table, plugin, ajaxt, total, start, stop, page, search_field) {
    var search = jQuery('#search').val();

    jQuery.post('./index.php?option=com_fabrik&task=mediamanager.listImage', {
        name: value, table: table, type: plugin, ajax_upload: ajaxt, start: start, stop: stop, search_field: search_field, search: search
    }, function (res) {
        if (res !== '0') {
            var json = jQuery.parseJSON(res);
            jQuery('#ul_list_image').html(json.text);

            paginationListImages(value, table, plugin, ajaxt, total, start, stop, page, search_field);
        }
    });
}