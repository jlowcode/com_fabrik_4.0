/**
 * Administrative Tools Business Rules Script.
 */

jQuery(document).ready(function () {
    alertify.defaults.transition = "slide";
    alertify.defaults.theme.ok = "btn btn-success";
    alertify.defaults.theme.cancel = "btn btn-danger";

    jQuery("#listTrans").change(function () {
        var idList = jQuery("#listTrans").val();

        delimiterTransf(false, 'none');
        elementDestTrans(false, 'none');
        sourceTypeField(false, 'none');
        fildFixFileupload(false, 'none');
        fieldUpdateDelete(false, 'none');

        if (idList.length === 0) {
            alertify.alert(Joomla.JText._('COM_FABRIK_MESSAGE_TITLE_ALERT'), Joomla.JText._('COM_FABRIK_MESSAGE_LABEL_ALERT_REQUIRED_FIELDS'));
        } else {
            jQuery.post('./index.php?option=com_fabrik&task=administrativetools.listElement', {
                idList: idList
            }, function (res) {
                if (res !== '0') {
                    var json = jQuery.parseJSON(res);

                    jQuery('#combo_elementSourceTrans').html('<select id="elementSourceTrans" name="elementSourceTrans" form="formTransformation" required>' +
                        '<option value="">' + Joomla.JText._('COM_FABRIK_TRANSFORMATION_FIELD_VALUE0') + '</option>' +
                        '</select>');

                    jQuery.each(json, function (index, value) {
                        jQuery('#elementSourceTrans').append('<option value="' + value.id + '">' + value.label + '</option>');
                    });

                    sourceTypeField(true, 'block');
                } else {
                    alertify.alert(Joomla.JText._('COM_FABRIK_MESSAGE_TITLE_ALERT'), Joomla.JText._('COM_FABRIK_MESSAGE_ALERT_ERRO_SELECT_LIST'));
                }
            });
        }

    });

    jQuery("#typeTrans").change(function () {
        var idList = jQuery("#listTrans").val();
        var idType = jQuery("#typeTrans").val();

        verificationShowHide('0');

        if (idList.length === 0) {
            alertify.alert(Joomla.JText._('COM_FABRIK_MESSAGE_TITLE_ALERT'), Joomla.JText._('COM_FABRIK_MESSAGE_LABEL_ALERT_REQUIRED_FIELDS'));
        } else {
            var type = verificationType(idType);

            if (type['bool']) {
                switch (type['field']) {
                    case 'databasejoin':
                    case 'dropdown':
                        jQuery.post('./index.php?option=com_fabrik&task=administrativetools.listElementType', {
                            idList: idList, typePlugin: type['field']
                        }, function (res) {
                            if (res !== '0') {
                                var json = jQuery.parseJSON(res);

                                jQuery('#combo_elementDestTrans').html('<select id="elementDestTrans" name="elementDestTrans" form="formTransformation" required>' +
                                    '<option value="">' + Joomla.JText._('COM_FABRIK_TRANSFORMATION_FIELD_ELEMENT_VALUE0') + '</option>' +
                                    '</select>');

                                jQuery.each(json, function (index, value) {
                                    jQuery('#elementDestTrans').append('<option value="' + value.id + '">' + value.label + '</option>');
                                });

                                verificationShowHide(idType);

                            } else {
                                jQuery('#typeTrans').val('');
                                alertify.alert(Joomla.JText._('COM_FABRIK_MESSAGE_TITLE_ALERT'), Joomla.JText._('COM_FABRIK_MESSAGE_ALERT_ERRO_SELECT_LIST'));
                            }
                        });

                        break;
                    case 'fileupload':
                        jQuery.post('./index.php?option=com_fabrik&task=administrativetools.listElementType', {
                            idList: idList, typePlugin: type['field']
                        }, function (res) {
                            if (res === '0') {
                                verificationShowHide('0');
                                jQuery('#elementSourceTrans').val('');
                                jQuery('#typeTrans').val('');
                                alertify.alert(Joomla.JText._('COM_FABRIK_MESSAGE_TITLE_ALERT'), Joomla.JText._('COM_FABRIK_MESSAGE_ALERT_ERRO_SELECT_LIST'));
                            }else{
                                verificationShowHide(idType);
                            }
                        });

                        break;
                }
            }else{
                verificationShowHide(type['type']);
            }
        }
    });

    jQuery("#listHarvert").change(function () {
        var idList = jQuery("#listHarvert").val();

        var varRadio = jQuery("input[name='syncHarvest']:checked").val();

        disabledHarvesting();

        if (idList.length === 0) {
            alertify.alert(Joomla.JText._('COM_FABRIK_MESSAGE_TITLE_ALERT'), Joomla.JText._('COM_FABRIK_MESSAGE_LABEL_ALERT_REQUIRED_FIELDS'));
        } else {
            jQuery.post('./index.php?option=com_fabrik&task=administrativetools.listElement', {
                idList: idList
            }, function (res) {
                if (res !== '0') {
                    var json = jQuery.parseJSON(res);

                    jQuery('#mdBdFieldDynamic').html('');

                    jQuery('#btnMapElement').removeAttr('disabled');
                    jQuery('#btnMapHeader').removeAttr('disabled');

                    jQuery('#downloadHarvest').html('<option value="">' + Joomla.JText._('COM_FABRIK_HARVESTING_OPTION_REPOSITORY_1') + '</option>');

                    jQuery.each(json, function (index, value) {
                        if (value.plugin === 'fileupload') {
                            jQuery('#downloadHarvest').append('<option value="' + value.id + '">' + value.label + '</option>');
                        }

                    });

                    jQuery('#downloadHarvest').removeAttr('disabled');

                    jQuery('#extractTextHarvert').html('<option value="">' + Joomla.JText._('COM_FABRIK_HARVESTING_OPTION_REPOSITORY_1') + '</option>');

                    jQuery.each(json, function (index, value) {
                        if (value.plugin === 'textarea') {
                            jQuery('#extractTextHarvert').append('<option value="' + value.id + '">' + value.label + '</option>');
                        }
                    });

                    jQuery('#extractTextHarvert').removeAttr('disabled');

                    jQuery('#dateListHarvest').html('<option value="">' + Joomla.JText._('COM_FABRIK_HARVESTING_OPTION_REPOSITORY_1') + '</option>');

                    jQuery.each(json, function (index, value) {
                        if (value.plugin === 'date') {
                            jQuery('#dateListHarvest').append('<option value="' + value.id + '">' + value.label + '</option>');
                        }
                    });

                    if (varRadio === '1') {
                        jQuery('#dateListHarvest').removeAttr('disabled');
                        jQuery('#dateRepositoryHarvest').removeAttr('disabled');
                    }
                } else {
                    alertify.alert(Joomla.JText._('COM_FABRIK_MESSAGE_TITLE_ALERT'), Joomla.JText._('COM_FABRIK_MESSAGE_ALERT_ERRO_SELECT_LIST'));
                }
            });
        }

    });

    jQuery(document).on('click', '#btnAddHarvestingHeader', function () {
        var scntDiv = jQuery('#mdMapHeader');

        var idListMaps = jQuery("#listHarvert").val();

        var vrSelect = '';

        jQuery.post('./index.php?option=com_fabrik&task=administrativetools.listElement', {
            idList: idListMaps
        }, function (res) {
            if (res !== '0') {
                var json = jQuery.parseJSON(res);

                vrSelect = '<select name="mapRarvestHeader[]" form="formHarvesting">' +
                    '<option value="">' + Joomla.JText._('COM_FABRIK_HARVESTING_OPTION_REPOSITORY_1') + '</option>';

                jQuery.each(json, function (index, value) {
                    vrSelect += '<option value="' + value.id + '">' + value.label + '</option>';
                });

                vrSelect += '</select>';

                jQuery('<div class="row">' +
                    '<div class="span5">' + vrSelect +
                    '</div>' +
                    '<div class="span5">' + selectBoxDublinCoreTypeHeader() +
                    '</div>' +
                    '<div class="span2">' +
                    '<div class="btn-group">' +
                    '<button type="button" id="btnAddHarvestingHeader" class="add btn button btn-success"><i class="icon-plus"></i></button>' +
                    '<button type="button" id="btnRemHarvestingHeader" class="remove btn button btn-danger"><i class="icon-minus"></i></button>' +
                    '</div>' +
                    '</div>' +
                    '</div>').appendTo(scntDiv);
            }
        });

        return false;
    });

    jQuery(document).on('click', '#btnAddHarvesting', function () {
        var scntDiv = jQuery('#mdMapElement');

        var idListMaps = jQuery("#listHarvert").val();

        var vrSelect = '';

        jQuery.post('./index.php?option=com_fabrik&task=administrativetools.listElement', {
            idList: idListMaps
        }, function (res) {
            if (res !== '0') {
                var json = jQuery.parseJSON(res);

                vrSelect = '<select name="mapRarvest[]" form="formHarvesting">' +
                    '<option value="">' + Joomla.JText._('COM_FABRIK_HARVESTING_OPTION_REPOSITORY_1') + '</option>';

                jQuery.each(json, function (index, value) {
                    vrSelect += '<option value="' + value.id + '">' + value.label + '</option>';
                });

                vrSelect += '</select>';

                jQuery('<div class="row">' +
                    '<div class="span5">' + vrSelect +
                    '</div>' +
                    '<div class="span5">' + selectBoxDublinCoreType() +
                    '</div>' +
                    '<div class="span2">' +
                    '<div class="btn-group">' +
                    '<button type="button" id="btnAddHarvesting" class="add btn button btn-success"><i class="icon-plus"></i></button>' +
                    '<button type="button" id="btnRemHarvesting" class="remove btn button btn-danger"><i class="icon-minus"></i></button>' +
                    '</div>' +
                    '</div>' +
                    '</div>').appendTo(scntDiv);
            }
        });

        return false;
    });

    jQuery(document).on('click', '#btnRemHarvesting', function () {
        jQuery(this).parents('div.row').remove();
        return false;
    });

    jQuery(document).on('click', '#btnRemHarvestingHeader', function () {
        jQuery(this).parents('div.row').remove();
        return false;
    });

    jQuery("input:radio[name='syncHarvest']").click(function () {
        var varRadio = jQuery(this).val();
        var idListMaps = jQuery("#listHarvert").val();

        if (varRadio === '0') {
            jQuery('#dateListHarvest').attr('disabled', 'true');
            jQuery('#dateListHarvest').val('');

            jQuery('#dateRepositoryHarvest').attr('disabled', 'true');
            jQuery('#dateRepositoryHarvest').val('');
        } else if (varRadio === '1') {
            if (idListMaps.length !== 0) {
                jQuery('#dateListHarvest').removeAttr('disabled');
                jQuery('#dateRepositoryHarvest').removeAttr('disabled');
            } else {
                jQuery('#dateListHarvest').attr('disabled', 'true');
                jQuery('#dateListHarvest').val('');

                jQuery('#dateRepositoryHarvest').attr('disabled', 'true');
                jQuery('#dateRepositoryHarvest').val('');
            }
        } else if (varRadio === '2') {
            jQuery('#dateListHarvest').attr('disabled', 'true');
            jQuery('#dateListHarvest').val('');

            jQuery('#dateRepositoryHarvest').attr('disabled', 'true');
            jQuery('#dateRepositoryHarvest').val('');
        }
    });

    jQuery("#btnRepository").click(function () {
        var link = jQuery("#linkHarvest").val();

        if (link.length === 0) {
            jQuery("#linkHarvest").focus();
            alertify.alert(Joomla.JText._('COM_FABRIK_MESSAGE_TITLE_ALERT'), Joomla.JText._('COM_FABRIK_MESSAGE_LABEL_ALERT_REQUIRED_FIELD'));
            return false;
        }

        jQuery.post('./index.php?option=com_fabrik&task=administrativetools.repositoryValidation', {
            link: link
        }, function (res) {
            if (res !== '0') {
                jQuery("#check-validar").css('display', 'block');
            } else {
                jQuery("#check-validar").css('display', 'none');
                alertify.alert(Joomla.JText._('COM_FABRIK_MESSAGE_TITLE_ALERT'), Joomla.JText._('COM_FABRIK_EXCEPTION_MESSAGE_ERROR2'));
            }
        });
    });
});


/**
 * Function that checks the transformation type and hides or makes delimiter, target element and plugin fields appear.
 *
 * @param type
 * @returns {[]}
 */
function verificationType(type) {
    var va_type = [];

    switch (type) {
        case '1':
        case '3':
        case '4':
            va_type['bool'] = true;
            va_type['field'] = 'databasejoin';

            break;
        case '2':
            va_type['bool'] = true;
            va_type['field'] = 'dropdown';

            break;
        case '6':
            va_type['bool'] = true;
            va_type['field'] = 'fileupload';

            break;
        case '5':
        default:
            va_type['bool'] = false;
            va_type['type'] = type;
    }

    return va_type;
}


/**
 * show and hide verification
 *
 * @param type
 */
function verificationShowHide(type) {

    switch (type) {
        case '1':
        case '4':
            delimiterTransf(false, 'none');
            elementDestTrans(true, 'block');
            fieldUpdateDelete(false, 'none');
            fildFixFileupload(false, 'none');

            break;
        case '2':
        case '3':
            delimiterTransf(true, 'block');
            elementDestTrans(true, 'block');
            fieldUpdateDelete(false, 'none');
            fildFixFileupload(false, 'none');

            break;
        case '5':
            delimiterTransf(false, 'none');
            elementDestTrans(false, 'none');
            fieldUpdateDelete(true, 'block');
            fildFixFileupload(false, 'none');

            break;
        case '6':
            delimiterTransf(false, 'none');
            elementDestTrans(false, 'none');
            fieldUpdateDelete(false, 'none');
            fildFixFileupload(true, 'block');

            break;
        default:
            delimiterTransf(false, 'none');
            elementDestTrans(false, 'none');
            fieldUpdateDelete(false, 'none');
            fildFixFileupload(false, 'none');
    }
}

/**
 * Function that hides or shows the delimiter.
 *
 * @param required
 * @param display
 */
function delimiterTransf(required, display) {
    jQuery('#delimiterTransf').val('');

    if (required) {
        jQuery('#delimiterTransf').attr('required', required);
    } else {
        jQuery('#delimiterTransf').removeAttr('required');
    }

    jQuery('#row_delimiterTransf').css('display', display);
}

/**
 * Function that hides or shows the target element.
 *
 * @param required
 * @param display
 */
function elementDestTrans(required, display) {
    if (required) {
        jQuery('#elementDestTrans').val('');
        jQuery('#elementDestTrans').attr('required', required);
    } else {
        jQuery('#elementDestTrans').removeAttr('required');
        jQuery('#combo_elementDestTrans').html('');
    }

    jQuery('#row_combo_elementDestTrans').css('display', display);
}

/**
 * Function that hides or shows the source element and the transformation field.
 *
 * @param required
 * @param display
 */
function sourceTypeField(required, display) {
    if (required) {
        jQuery('#typeTrans').val('');
        jQuery('#typeTrans').attr('required', required);
        jQuery('#elementSourceTrans').val('');
        jQuery('#elementSourceTrans').attr('required', required);
    } else {
        jQuery('#typeTrans').val('');
        jQuery('#typeTrans').removeAttr('required');
        jQuery('#elementSourceTrans').removeAttr('required');
        jQuery('#combo_elementSourceTrans').html('');
    }

    jQuery('#typeTrans').css('display', display);
    jQuery('#elementSourceTrans').css('display', display);
}

/**
 * Function that shows and hides the update and delete fields.
 *
 * @param required
 * @param display
 */
function fieldUpdateDelete(required, display) {
    if (required) {
        jQuery('#updateDB').attr('required', required);
        jQuery('#deleteDB').attr('required', required);
    } else {
        jQuery('#updateDB').removeAttr('required');
        jQuery('#deleteDB').removeAttr('required');
    }

    jQuery('#row_updateDB').css('display', display);
    jQuery('#row_deleteDB').css('display', display);
}

/**
 * Function that shows and hides the fileupload adjustment fields.
 *
 * @param required
 * @param display
 */
function fildFixFileupload(required, display) {
    if (!required) {
        jQuery('#tableRepeat').attr("checked", false);
        jQuery('#thumbsCrops').attr("checked", false);
    }

    jQuery('#row_repeat').css('display', display);
    jQuery('#row_thumbs_crops').css('display', display);
}

/**
 * Disable the mandatory fields that you need to select from the list.
 */
function disabledHarvesting() {
    jQuery('#downloadHarvest').attr('disabled', 'true');
    jQuery('#downloadHarvest').val('');

    jQuery('#extractTextHarvert').attr('disabled', 'true');
    jQuery('#extractTextHarvert').val('');

    jQuery('#dateListHarvest').attr('disabled', 'true');
    jQuery('#dateListHarvest').val('');

    jQuery('#dateRepositoryHarvest').attr('disabled', 'true');
    jQuery('#dateRepositoryHarvest').val('');

    jQuery('#mdBdFieldDynamic').html('');

    jQuery('#btnMapHeader').attr('disabled', 'true');
    jQuery('#btnMapElement').attr('disabled', 'true');
}

/**
 * enable and disable harvesting execution structure.
 */
function enableDisableHarvesting(id, status) {
    jQuery.post('./index.php?option=com_fabrik&task=administrativetools.enableDisableHarvesting', {
        id: id, status: status
    }, function (res) {
        if (res !== '0') {
            if (status === '1') {
                jQuery('#btn_status' + id).html('<button type="button" class="btn" onclick="enableDisableHarvesting(' + id + ',\'0\');">' +
                    '<i class="icon-ok text-success"></i></button>');
            } else {
                jQuery('#btn_status' + id).html('<button type="button" class="btn" onclick="enableDisableHarvesting(' + id + ',\'1\');">' +
                    '<i class="icon-remove text-error"></i></button>');
            }

        }
    });
}

/**
 * Delete harvesting table line.
 *
 * @param id
 * @param file
 * @param message
 */
function deleteHarvesting(id, message) {
    var ar_message = message.split('|');

    alertify.confirm(ar_message[2], ar_message[3], function () {
        jQuery.post('./index.php?option=com_fabrik&task=administrativetools.deleteHarvesting', {
            id: id
        }, function (res) {
            if (res !== '0') {
                alertify.alert(ar_message[4], ar_message[5]);

                jQuery('#rowTable' + id).css('display', 'none');
            } else {
                alertify.alert(ar_message[2], ar_message[6]);
            }
        });
    }, function () {
    }).set('labels', {ok: ar_message[0], cancel: ar_message[1]});
}

/**
 * function that creates Dublin Core Type select box.
 *
 * @returns {string}
 */
function selectBoxDublinCoreTypeHeader(id = '') {
    var text = '';

    text += '<select id="listDublinCoreTypeHeader' + id + '" name="listDublinCoreTypeHeader[]" form="formHarvesting">';

    text += '<option selected value="">' + Joomla.JText._('COM_FABRIK_DUBLIN_CORE_TYPE_LABEL') + '</option>' +
        '<option value="identifier">identifier</option>' +
        '<option value="datestamp">datestamp</option>' +
        '<option value="setSpec">setSpec</option>';

    text += '</select>';

    return text;
}

/**
 * function that creates Dublin Core Type select box.
 *
 * @returns {string}
 */
function selectBoxDublinCoreType(id = '') {
    var text = '';

    text += '<select id="listDublinCoreType' + id + '" name="listDublinCoreType[]" form="formHarvesting">';

    text += '<option selected value="">' + Joomla.JText._('COM_FABRIK_DUBLIN_CORE_TYPE_LABEL') + '</option>' +
        '<option value="dc:description.abstract">abstract</option>' +
        '<option value="dc:contributor">contributor</option>' +
        '<option value="dc:coverage">coverage</option>' +
        '<option value="dc:creator">creator</option>' +
        '<option value="dc:date">date</option>' +
        '<option value="dc:description">description</option>' +
        '<option value="dc:format">format</option>' +
        '<option value="dc:identifier">identifier</option>' +
        '<option value="dc:language">language</option>' +
        '<option value="dc:publisher">publisher</option>' +
        '<option value="dc:relation">relation</option>' +
        '<option value="dc:rights">rights</option>' +
        '<option value="dc:source">source</option>' +
        '<option value="dc:subject">subject</option>' +
        '<option value="dc:title">title</option>' +
        '<option value="dc:type">type</option>' +
        '<option value="dcterms:accrualMethod">dcterms:accrualMethod</option>' +
        '<option value="dcterms:accrualPeriodicity">dcterms:accrualPeriodicity</option>' +
        '<option value="dcterms:accrualPeriodicity">dcterms:accrualPeriodicity</option>' +
        '<option value="dcterms:accessRights">dcterms:accessRights</option>' +
        '<option value="dcterms:audience">dcterms:audience</option>' +
        '<option value="dcterms:conformsTo">dcterms:conformsTo</option>' +
        '<option value="dcterms:contributor">dcterms:contributor</option>' +
        '<option value="dcterms:coverage">dcterms:coverage</option>' +
        '<option value="dcterms:creator">dcterms:creator</option>' +
        '<option value="dcterms:educationLevel">dcterms:educationLevel</option>' +
        '<option value="dcterms:extent">dcterms:extent</option>' +
        '<option value="dcterms:format">dcterms:format</option>' +
        '<option value="dcterms:hasFormat">dcterms:hasFormat</option>' +
        '<option value="dcterms:hasPart">dcterms:hasPart</option>' +
        '<option value="dcterms:hasVersion">dcterms:hasVersion</option>' +
        '<option value="dcterms:instructionalMethod">dcterms:instructionalMethod</option>' +
        '<option value="dcterms:isFormatOf">dcterms:isFormatOf</option>' +
        '<option value="dcterms:isPartOf">dcterms:isPartOf</option>' +
        '<option value="dcterms:isReferencedBy">dcterms:isReferencedBy</option>' +
        '<option value="dcterms:isReplacedBy">dcterms:isReplacedBy</option>' +
        '<option value="dcterms:isRequiredBy">dcterms:isRequiredBy</option>' +
        '<option value="dcterms:isVersionOf">dcterms:isVersionOf</option>' +
        '<option value="dcterms:language">dcterms:language</option>' +
        '<option value="dcterms:license">dcterms:license</option>' +
        '<option value="dcterms:mediator">dcterms:mediator</option>' +
        '<option value="dcterms:medium">dcterms:medium</option>' +
        '<option value="dcterms:provenance">dcterms:provenance</option>' +
        '<option value="dcterms:publisher">dcterms:publisher</option>' +
        '<option value="dcterms:references">dcterms:references</option>' +
        '<option value="dcterms:relation">dcterms:relation</option>' +
        '<option value="dcterms:replaces">dcterms:replaces</option>' +
        '<option value="dcterms:requires">dcterms:requires</option>' +
        '<option value="dcterms:rights">dcterms:rights</option>' +
        '<option value="dcterms:rightsHolder">dcterms:rightsHolder</option>' +
        '<option value="dcterms:source">dcterms:source</option>' +
        '<option value="dcterms:spatial">dcterms:spatial</option>' +
        '<option value="dcterms:subject">dcterms:subject</option>' +
        '<option value="dcterms:temporal">dcterms:temporal</option>' +
        '<option value="dcterms:type">dcterms:type</option>'

    text += '</select>';

    return text;
}

/**
 * Ajax function that takes information from the database of the harvesting table and positions it for user editing.
 *
 * @param id
 */
function editHarvesting(id) {
    jQuery.post('./index.php?option=com_fabrik&task=administrativetools.editHarvesting', {
        id: id
    }, function (res) {
        if (res !== '0') {
            var json = jQuery.parseJSON(res);

            jQuery('#linkHarvest').val(json.repository);
            jQuery('#listHarvert').val(json.list);

            jQuery('#idHarvest').val(json.id);

            jQuery('#btnMapElement').removeAttr('disabled');
            jQuery('#btnMapHeader').removeAttr('disabled');

            jQuery('#downloadHarvest').html('<option value="">' + Joomla.JText._('COM_FABRIK_HARVESTING_OPTION_REPOSITORY_1') + '</option>');
            jQuery.each(json.element, function (index, value) {
                if (value.plugin === 'fileupload') {
                    jQuery('#downloadHarvest').append('<option value="' + value.id + '">' + value.label + '</option>');
                }

            });
            jQuery('#downloadHarvest').removeAttr('disabled');
            if ((json.dowload_file !== '0') && (json.dowload_file !== '')) {
                jQuery('#downloadHarvest').val(json.dowload_file);
            }

            jQuery('#extractTextHarvert').html('<option value="">' + Joomla.JText._('COM_FABRIK_HARVESTING_OPTION_REPOSITORY_1') + '</option>');
            jQuery.each(json.element, function (index, value) {
                if (value.plugin === 'textarea') {
                    jQuery('#extractTextHarvert').append('<option value="' + value.id + '">' + value.label + '</option>');
                }
            });
            jQuery('#extractTextHarvert').removeAttr('disabled');
            if ((json.extract !== '0') && (json.extract !== '')) {
                jQuery('#extractTextHarvert').val(json.extract);
            }

            jQuery('#dateListHarvest').html('<option value="">' + Joomla.JText._('COM_FABRIK_HARVESTING_OPTION_REPOSITORY_1') + '</option>');
            jQuery.each(json.element, function (index, value) {
                if (value.plugin === 'date') {
                    jQuery('#dateListHarvest').append('<option value="' + value.id + '">' + value.label + '</option>');
                }
            });

            if (json.syncronism === '1') {
                jQuery('#syncHarvest0').removeAttr("checked");
                jQuery('#syncHarvest1').attr("checked", true);
                jQuery('#dateListHarvest').removeAttr('disabled');
                jQuery('#dateRepositoryHarvest').removeAttr('disabled');

                jQuery('#dateListHarvest').val(json.field1);
                jQuery('#dateRepositoryHarvest').val(json.field2);
            }else{
                jQuery('#syncHarvest0').attr("checked", true);
                jQuery('#syncHarvest1').removeAttr("checked");
                jQuery('#dateListHarvest').attr('disabled', true);
                jQuery('#dateRepositoryHarvest').attr('disabled', true);

                jQuery('#dateListHarvest').val('');
                jQuery('#dateRepositoryHarvest').val('');
            }

            var scntDiv = jQuery('#mdMapHeader');

            scntDiv.html('');

            jQuery.each(json.map_header, function (index, value) {
                var vrSelect = '';

                vrSelect = '<select id="mapRarvestHeader' + value + '" name="mapRarvestHeader[]" form="formHarvesting">' +
                    '<option value="">' + Joomla.JText._('COM_FABRIK_HARVESTING_OPTION_REPOSITORY_1') + '</option>';

                jQuery.each(json.element, function (index1, value1) {
                    vrSelect += '<option value="' + value1.id + '">' + value1.label + '</option>';
                });

                vrSelect += '</select>';

                jQuery('<div class="row">' +
                    '<div class="span5">' + vrSelect +
                    '</div>' +
                    '<div class="span5">' + selectBoxDublinCoreTypeHeader(value) +
                    '</div>' +
                    '<div class="span2">' +
                    '<div class="btn-group">' +
                    '<button type="button" id="btnAddHarvestingHeader" class="add btn button btn-success"><i class="icon-plus"></i></button>' +
                    '<button type="button" id="btnRemHarvestingHeader" class="remove btn button btn-danger"><i class="icon-minus"></i></button>' +
                    '</div>' +
                    '</div>' +
                    '</div>').appendTo(scntDiv);

                jQuery('#mapRarvestHeader' + value).val(value);
                jQuery('#listDublinCoreTypeHeader' + value).val(index);
            });

            var scntDiv = jQuery('#mdMapElement');

            scntDiv.html('');

            jQuery.each(json.map_metadata, function (index, value) {
                jQuery.each(value, function (index1, value1) {
                    console.log(index);
                    console.log(value1);
                    var vrSelect = '';

                    vrSelect = '<select id="mapRarvest' + value1 + '" name="mapRarvest[]" form="formHarvesting">' +
                        '<option value="">' + Joomla.JText._('COM_FABRIK_HARVESTING_OPTION_REPOSITORY_1') + '</option>';

                    jQuery.each(json.element, function (index2, value2) {
                        vrSelect += '<option value="' + value2.id + '">' + value2.label + '</option>';
                    });

                    vrSelect += '</select>';

                    jQuery('<div class="row">' +
                        '<div class="span5">' + vrSelect +
                        '</div>' +
                        '<div class="span5">' + selectBoxDublinCoreType(value1) +
                        '</div>' +
                        '<div class="span2">' +
                        '<div class="btn-group">' +
                        '<button type="button" id="btnAddHarvesting" class="add btn button btn-success"><i class="icon-plus"></i></button>' +
                        '<button type="button" id="btnRemHarvesting" class="remove btn button btn-danger"><i class="icon-minus"></i></button>' +
                        '</div>' +
                        '</div>' +
                        '</div>').appendTo(scntDiv);

                    jQuery('#mapRarvest' + value1).val(value1);
                    jQuery('#listDublinCoreType' + value1).val(index);
                });
            });

        }
    });
}