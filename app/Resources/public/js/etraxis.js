/*!
 *  Copyright (C) 2006-2015 Artem Rodygin
 *
 *  This file is part of eTraxis.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
 */

var eTraxis = window.eTraxis || {};

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
