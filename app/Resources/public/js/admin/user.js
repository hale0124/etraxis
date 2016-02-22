/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var UserApp = (function() {

    // User's ID.
    var userId = $('#tabs-user').data('user-id');

    /**
     * Reloads current tab to refresh its content.
     */
    var reloadTab = function() {
        var $tabs = $('#tabs-user');
        var current = $tabs.tabs('option', 'active');
        $tabs.one('tabsload', function() {
            var name = $('#user-details').data('name');
            $('ul.ui-tabs-nav li[tabindex=0] a', $tabs).text(name);
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
         */
        edit: function() {
            if (userId == eTraxis.getUserId()) {
                $('#form_admin').disable(true);
                $('#form_disabled').disable(true);
            }

            eTraxis.modal({
                url: eTraxis.route('admin_edit_user', { id: userId }),
                title: $('#user-details').data('name'),
                success: function() {
                    if (userId == eTraxis.getUserId()) {
                        window.location.assign(eTraxis.route('admin_view_user', {id: userId}));
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
         */
        delete: function() {
            eTraxis.confirm(eTraxis.i18n['button.delete'], eTraxis.i18n['user.confirm.delete'], function() {
                $.post(eTraxis.route('admin_delete_user', { id: userId }), function() {
                    window.location.assign(eTraxis.route('admin_users'));
                });
            });
        },

        /**
         * Disables user.
         */
        disable: function() {
            $.post(eTraxis.route('admin_disable_user'), { ids: [userId] }, reloadTab);
        },

        /**
         * Enables user.
         */
        enable: function() {
            $.post(eTraxis.route('admin_enable_user'), { ids: [userId] }, reloadTab);
        },

        /**
         * Unlocks user.
         */
        unlock: function() {
            $.post(eTraxis.route('admin_unlock_user', { id: userId }), reloadTab);
        },

        /**
         * Adds the user to specified groups.
         */
        addToGroup: function() {
            var groups = $('#others').val();
            if (groups) {
                $.post(eTraxis.route('admin_users_add_groups', { id: userId }), { groups: groups }, function() {
                    reloadTab();
                });
            }
        },

        /**
         * Removes the user from specified groups.
         */
        removeFromGroup: function() {
            var groups = $('#groups').val();
            if (groups) {
                $.post(eTraxis.route('admin_users_remove_groups', { id: userId }), { groups: groups }, function() {
                    reloadTab();
                });
            }
        }
    };
})();
