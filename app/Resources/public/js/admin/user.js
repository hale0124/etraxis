/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var UserApp = (function() {

    /**
     * Reloads current tab to refresh its content.
     */
    var reloadTab = function() {
        var $tabs = $('#tabs-user');
        var current = $tabs.tabs('option', 'active');
        $tabs.one('tabsload', function() {
            var name = $('#user-details').data('name');
            $('ul.ui-tabs-nav li:first a', $tabs).text(name);
        });
        $tabs.tabs('load', current);
    };

    return {

        /**
         * Redirects back to users list.
         */
        back: function() {
            window.location.assign(eTraxis.route('admin_users'));
        },

        /**
         * Invokes "Edit user" dialog.
         *
         * @param {number} id User ID.
         */
        edit: function(id) {
            if (id == eTraxis.getUserId()) {
                $('#form_admin').disable(true);
                $('#form_disabled').disable(true);
            }

            eTraxis.modal({
                url: eTraxis.route('admin_dlg_edit_user', { id: id }),
                title: $('#user-details').data('name'),
                success: function() {
                    if (id == eTraxis.getUserId()) {
                        window.location.assign(eTraxis.route('admin_view_user', { id: id }));
                    }
                    else {
                        reloadTab();
                    }
                    return true;
                }
            });
        },

        /**
         * Deletes user after confirmation.
         *
         * @param {number} id User ID.
         */
        delete: function(id) {
            eTraxis.confirm(eTraxis.i18n['button.delete'], eTraxis.i18n['user.confirm.delete'], function() {
                $.post(eTraxis.route('admin_delete_user', { id: id }), function() {
                    window.location.assign(eTraxis.route('admin_users'));
                });
            });
        },

        /**
         * Disables user.
         *
         * @param {number} id User ID.
         */
        disable: function(id) {
            $.post(eTraxis.route('admin_disable_user'), { ids: [id] }, reloadTab);
        },

        /**
         * Enables user.
         *
         * @param {number} id User ID.
         */
        enable: function(id) {
            $.post(eTraxis.route('admin_enable_user'), { ids: [id] }, reloadTab);
        },

        /**
         * Unlocks user.
         *
         * @param {number} id User ID.
         */
        unlock: function(id) {
            $.post(eTraxis.route('admin_unlock_user', { id: id }), reloadTab);
        },

        /**
         * Adds user to selected groups.
         *
         * @param {number} id User ID.
         */
        addToGroup: function(id) {
            var groups = $('#others').val();
            if (groups) {
                $.post(eTraxis.route('admin_users_add_groups', { id: id }), { groups: groups }, function() {
                    reloadTab();
                });
            }
        },

        /**
         * Removes user from selected groups.
         *
         * @param {number} id User ID.
         */
        removeFromGroup: function(id) {
            var groups = $('#groups').val();
            if (groups) {
                $.post(eTraxis.route('admin_users_remove_groups', { id: id }), { groups: groups }, function() {
                    reloadTab();
                });
            }
        }
    };
})();
