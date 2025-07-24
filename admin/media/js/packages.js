/**
 * Js file with script configuration of views packages.
 */

$(document).ready(function () {
    alertify.defaults.transition = "slide";
    alertify.defaults.theme.ok = "btn btn-success";
    alertify.defaults.theme.cancel = "btn btn-danger";

    $('#btnS').click(function () {
        var i = $('#opRecord1').val();
        $('#recordDB').val(i);
    });

    $('#btnN').click(function () {
        var i = $('#opRecord0').val();
        $('#recordDB').val(i);
    });

    $("#all").click(function () {
        if ($("#all").is(':checked')) {
            $("input[id^='file_a']").prop("checked", true);
        } else {
            $("input[id^='file_a']").prop("checked", false);
        }

    });
});

/**
 * Ajax function that calls a php structure that will delete the file inside the packagesupload folder.
 *
 * @param name
 * @param col
 * @param message
 */
function deleteFile(name, col, message) {
    var ar_message = message.split('|');

    alertify.confirm(ar_message[2], ar_message[3], function () {
        $.post('./index.php?option=com_fabrik&task=packages.deleteFile', {
            name: name
        }, function (res) {
            if (res !== '0') {
                alertify.alert(ar_message[5], ar_message[6]);

                $('#colFile' + col).css('display', 'none');
            }
        });
    }, function () {
    }).set('labels', {ok: ar_message[0], cancel: ar_message[1]});
}

/**
 * Ajax function that calls a php structure that will delete the package inside the generatepackages folder and the information inside the
 * database.
 *
 * @param id
 * @param file
 * @param message
 */
function deletePackage(id, file, message) {
    var ar_message = message.split('|');

    alertify.confirm(ar_message[2], ar_message[4], function () {
        $.post('./index.php?option=com_fabrik&task=packages.deletePackage', {
            id: id, file: file
        }, function (res) {
            if (res !== '0') {
                alertify.alert(ar_message[5], ar_message[7]);

                $('#listRow' + id).css('display', 'none');
            }
        });
    }, function () {
    }).set('labels', {ok: ar_message[0], cancel: ar_message[1]});
}