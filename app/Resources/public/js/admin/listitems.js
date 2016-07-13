/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var ListItemsApp = (function() {

    /**
     * Reloads current tab to refresh its content.
     */
    var reloadTab = function(value) {
        var $tabs = $('#tabs-field');
        var current = $tabs.tabs('option', 'active');
        if (value !== undefined) {
            $tabs.one('tabsload', function() {
                $('#listitems').val(value).change();
            });
        }
        $tabs.tabs('load', current);
    };

    return {

        /**
         * Called when a list item was selected.
         */
        onSelect: function() {
            var value = $('#listitems').val();
            $('#listitem-edit, #listitem-delete').disable(value === null);
        },

        /**
         * Invokes "New list item" dialog.
         *
         * @param {number} id Field ID.
         */
        create: function(id) {
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_new_listitem', { id: id }),
                open: function() {
                    var $dialog = $('#listitem_value').closest('.ui-dialog');
                    $('.ui-dialog-titlebar', $dialog).remove();
                },
                success: function() {
                    reloadTab();
                    return true;
                }
            });
        },

        /**
         * Invokes "Edit list item" dialog.
         *
         * @param {number} id Field ID.
         */
        edit: function(id) {
            var value = $('#listitems').val();
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_edit_listitem', { id: id, value: value }),
                open: function() {
                    var $dialog = $('#listitem_text').closest('.ui-dialog');
                    $('.ui-dialog-titlebar', $dialog).remove();
                },
                success: function() {
                    reloadTab(value);
                    return true;
                }
            });
        },

        /**
         * Deletes current list item after confirmation.
         *
         * @param {number} id Field ID.
         */
        delete: function(id) {
            var value = $('#listitems').val();
            eTraxis.confirm(eTraxis.i18n['button.delete'], eTraxis.i18n['listitem.confirm.delete'], function() {
                $.post(eTraxis.route('admin_delete_listitem', { id: id, value: value }), function() {
                    reloadTab();
                });
            });
        }
    };
})();
