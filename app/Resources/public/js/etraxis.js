/*!
 *  Copyright (C) 2006-2015 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var eTraxis = window.eTraxis || {};

/**
 * Global one-time initialization of AJAX requests for just loaded page.
 */
eTraxis.initAjax = function() {

    // Inject default CSRF token into POST AJAX requests if it's missing there.
    $.ajaxPrefilter(function(options, originalOptions) {
        if (options.type && options.type.toUpperCase() === 'POST') {
            var $token = $('#__etraxis_token');
            if ($token.length != 0) {
                if (typeof originalOptions.data === 'undefined') {
                    originalOptions.data = {};
                }
                if (typeof originalOptions.data === 'object' && typeof originalOptions.data._token === 'undefined') {
                    originalOptions.data._token = $token.val();
                    options.data = $.param(originalOptions.data);
                }
            }
        }
    });

    // Show appropriate message to user if AJAX request ends up with '401 Unauthorized' HTTP error.
    $(document).ajaxError(function(e, xhr) {
        if (xhr.status === 401) {
            eTraxis.unblock();
            eTraxis.alert(eTraxis.i18n['error'], xhr.responseText);
        }
    });
};

/**
 * Blocks UI with specified message.
 *
 * @param {string} message Blocking message.
 */
eTraxis.block = function(message) {
    $.blockUI({
        theme: true,
        title: null,
        message: message ? message : eTraxis.i18n['please_wait'],
        themedCSS: {
            padding: '10px'
        }
    });
};

/**
 * Unblocks UI.
 */
eTraxis.unblock = function() {
    $.unblockUI();
};

/**
 * Simple message dialog (alternative to JavaScript "alert").
 *
 * @param {string}   title   Dialog title.
 * @param {string}   message Dialog message.
 * @param {function} onClose Optional handler to call when dialog is closed.
 */
eTraxis.alert = function(title, message, onClose) {

    var buttons = {};

    buttons[eTraxis.i18n['button.close']] = function() {
        $(this).dialog('close');
    };

    $('#__etraxis_modal')
        .html(message)
        .dialog({
            title: title,
            autoOpen: true,
            modal: true,
            resizable: false,
            close: onClose,
            buttons: buttons
        });
};

/**
 * Confirmation dialog (alternative to JavaScript "confirm").
 *
 * @param {string}   title     Dialog title.
 * @param {string}   message   Dialog message.
 * @param {function} onConfirm Optional handler to call when dialog is closed with confirmation (via "Yes" button).
 */
eTraxis.confirm = function(title, message, onConfirm) {

    var buttons = {};

    buttons[eTraxis.i18n['button.yes']] = function() {
        $(this).dialog('close');
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    };

    buttons[eTraxis.i18n['button.no']] = function() {
        $(this).dialog('close');
    };

    $('#__etraxis_modal')
        .html(message)
        .dialog({
            title: title,
            autoOpen: true,
            modal: true,
            resizable: false,
            buttons: buttons
        });
};
