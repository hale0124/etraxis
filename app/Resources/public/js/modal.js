/*!
 *  Copyright (C) 2012-2015 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var eTraxis = window.eTraxis || {};

/**
 * Full-featured modal dialog.
 */
eTraxis.modal = function(options) {

    var settings = $.extend({
        url: null,
        title: null,
        btnOk: eTraxis.i18n.Ok,
        btnCancel: eTraxis.i18n.Cancel,
        data: {},
        success: null,
        error: null
    }, options);

    eTraxis.block();

    $.get(settings.url, function(data) {

        eTraxis.unblock();

        var $modal = $(data).insertAfter('#__etraxis_modal');

        $modal.initUI();

        // Let the form be submitted via "ajaxForm" plugin.
        $('form', $modal).ajaxForm({

            cache: false,

            // Optional extra data to be submitted along with the form.
            data: settings.data,

            // Block the UI until AJAX query is completed.
            beforeSend: function() {
                eTraxis.block();
                $('.ui-state-error', $modal).remove();
            },

            // When the AJAX query is done (no matter the result) - unblock the UI.
            complete: function() {
                eTraxis.unblock();
            },

            // Execute callback success handler if one is provided.
            success: function(data) {
                if (typeof settings.success === 'function') {
                    if (settings.success(data)) {
                        $modal.dialog('destroy');
                        $modal.remove();
                    }
                }
                else {
                    $modal.dialog('destroy');
                    $modal.remove();
                }
            },

            // Execute callback error handler if one is provided.
            error: function(xhr) {
                if (typeof settings.error === 'function') {
                    if (settings.error(xhr)) {
                        $modal.dialog('destroy');
                        $modal.remove();
                    }
                }
                else if (xhr.status >= 400) {
                    var response = xhr.responseJSON ? xhr.responseJSON : xhr.responseText;
                    if (typeof response === 'object') {
                        $.each(response, function(id, message) {
                            var name = $('form', $modal).prop('name');
                            var $control = $('#' + name + '_' + id);
                            $control.after('<p class="ui-corner-all ui-state-error">' + message + '</p>');
                        });
                    }
                    else {
                        eTraxis.alert(eTraxis.i18n.Error, response);
                    }
                }
            }
        });

        var buttons = {};

        buttons[settings.btnOk] = function() {
            $('form', $modal).submit();
        };

        buttons[settings.btnCancel] = function() {
            $modal.dialog('destroy');
            $modal.remove();
        };

        // Show the dialog.
        $modal.dialog({
            title: settings.title,
            autoOpen: true,
            modal: true,
            resizable: false,
            width: 'auto',
            height: 'auto',
            buttons: buttons,
            close: function() {
                $modal.remove();
            }
        });
    });
};
