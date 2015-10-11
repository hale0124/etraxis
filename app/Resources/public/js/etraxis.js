/*!
 *  Copyright (C) 2006-2015 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var eTraxis = window.eTraxis || {};

/**
 * Blocks UI with specified message.
 *
 * @param message Blocking message.
 */
eTraxis.block = function(message) {
    $.blockUI({
        theme: true,
        title: null,
        message: message ? message : eTraxis.i18n.PleaseWait,
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
 * @param title   Dialog title.
 * @param message Dialog message.
 * @param onClose Optional handler to call when dialog is closed.
 */
eTraxis.alert = function(title, message, onClose) {

    var buttons = {};

    buttons[eTraxis.i18n.Close] = function() {
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
 * @param title     Dialog title.
 * @param message   Dialog message.
 * @param onConfirm Optional handler to call when dialog is closed with confirmation (via "Yes" button).
 */
eTraxis.confirm = function(title, message, onConfirm) {

    var buttons = {};

    buttons[eTraxis.i18n.Yes] = function() {
        $(this).dialog('close');
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    };

    buttons[eTraxis.i18n.No] = function() {
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
