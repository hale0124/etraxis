/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var GroupsApp = (function() {

    // Panel sections.
    var SECTION_GLOBAL = 1;
    var SECTION_LOCAL  = 2;

    // Groups panel.
    var $groups = $('#groups');

    /**
     * Reloads current tab to refresh its content.
     */
    var reloadTab = function() {
        var $tabs = $('#tabs-group');
        var current = $tabs.tabs('option', 'active');
        $tabs.tabs('load', current);
    };

    // Group was clicked.
    $groups.on('panel.item.click', function(e, data) {
        GroupsApp.select(data);
    });

    return {

        /**
         * Reloads list of groups in the panel.
         *
         * @param {number} [id] Project ID.
         * @param {function} [callback] Optional function to call after the list is loaded.
         */
        reload: function(id, callback) {
            $groups.panel('clear');
            $.getJSON(eTraxis.route('admin_groups_list', { id: id }), function(data) {
                $(data).each(function(index, item) {
                    $groups.panel('append', item['id'], item['name'], false, item['projectId'] ? SECTION_LOCAL : SECTION_GLOBAL);
                });
                if (typeof callback === 'function') {
                    callback();
                }
            });
        },

        /**
         * Selects specified group in the panel.
         *
         * @param {number} id Group ID.
         */
        select: function(id) {
            $('#content').load(eTraxis.route('admin_view_group', { id: id }), function() {
                $(this).initUI();
            });
        },

        /**
         * Invokes "New group" dialog.
         */
        create: function() {
            var id = ProjectsApp.selected();
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_new_group', { id: id }),
                title: eTraxis.i18n['group.new'],
                success: function() {
                    var name = $('#group_name').val();
                    GroupsApp.reload(id, function() {
                        $groups.panel('expand');
                        GroupsApp.select($groups.panel('find', name));
                    });
                    return true;
                }
            });
        },

        /**
         * Invokes "Edit group" dialog.
         *
         * @param {number} id Group ID.
         */
        edit: function(id) {
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_edit_group', { id: id }),
                title: $groups.panel('get', id),
                success: function() {
                    GroupsApp.reload(ProjectsApp.selected(), function() {
                        GroupsApp.select(id);
                    });
                    return true;
                }
            });
        },

        /**
         * Deletes group after confirmation.
         *
         * @param {number} id Group ID.
         */
        delete: function(id) {
            eTraxis.confirm(eTraxis.i18n['button.delete'], eTraxis.i18n['group.confirm.delete'], function() {
                $.post(eTraxis.route('admin_delete_group', { id: id }), function() {
                    $('#content').html(null);
                    var projectId = ProjectsApp.selected();
                    if (projectId) {
                        ProjectsApp.select(projectId);
                    }
                    else {
                        ProjectsApp.first();
                    }
                });
            });
        },

        /**
         * Adds selected users to specified group.
         *
         * @param {number} id Group ID.
         */
        addUsers: function(id) {
            var users = $('#others').val();
            if (users) {
                $.post(eTraxis.route('admin_groups_add_users', { id: id }), { users: users }, function() {
                    reloadTab();
                });
            }
        },

        /**
         * Removes selected users from specified group.
         *
         * @param {number} id Group ID.
         */
        removeUsers: function(id) {
            var users = $('#users').val();
            if (users) {
                $.post(eTraxis.route('admin_groups_remove_users', { id: id }), { users: users }, function() {
                    reloadTab();
                });
            }
        }
    };
})();
