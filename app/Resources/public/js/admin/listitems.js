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
    var reloadTab = function(key) {
        var $tabs = $('#tabs-field');
        var current = $tabs.tabs('option', 'active');
        if (key !== undefined) {
            $tabs.one('tabsload', function() {
                $('#listitems').val(key).change();
            });
        }
        $tabs.tabs('load', current);
    };

    return {

        /**
         * Called when a list item was selected.
         */
        onSelect: function() {
            var key = $('#listitems').val();
            $('#listitem-edit, #listitem-delete').disable(key === null);
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
                    var $dialog = $('#listitem_key').closest('.ui-dialog');
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
            var key = $('#listitems').val();
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_edit_listitem', { id: id, key: key }),
                open: function() {
                    var $dialog = $('#listitem_value').closest('.ui-dialog');
                    $('.ui-dialog-titlebar', $dialog).remove();
                },
                success: function() {
                    reloadTab(key);
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
            var key = $('#listitems').val();
            eTraxis.confirm(eTraxis.i18n['button.delete'], eTraxis.i18n['listitem.confirm.delete'], function() {
                $.post(eTraxis.route('admin_delete_listitem', { id: id, key: key }), function() {
                    reloadTab();
                });
            });
        }
    };
})();
