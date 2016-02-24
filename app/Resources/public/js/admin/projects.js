/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var ProjectsApp = (function() {

    // Projects panel.
    var $projects = $('#projects');

    // Project was clicked.
    $projects.on('panel.item.click', function(e, data) {
        ProjectsApp.select(data);
    });

    // First time initialization.
    $(function() {
        ProjectsApp.reload(function() {
            ProjectsApp.first();
        });
    });

    return {

        /**
         * Reloads list of projects in the panel.
         *
         * @param {function} [callback] Optional function to call after the list is loaded.
         */
        reload: function(callback) {
            TemplatesApp.reset();
            $projects.panel('clear');
            $.getJSON(eTraxis.route('admin_projects_list'), function(data) {
                $(data).each(function(index, item) {
                    $projects.panel('append', item['id'], item['name'], item['isSuspended']);
                });
                if (typeof callback === 'function') {
                    callback();
                }
            });
        },

        /**
         * Selects specified project in the panel.
         *
         * @param {number} id Project ID.
         */
        select: function(id) {
            $('#content').load(eTraxis.route('admin_view_project', { id: id }), function() {
                $(this).initUI();
                $projects.panel('select', id);
                GroupsApp.reload(id);
                TemplatesApp.reload(id);
            });
        },

        /**
         * Returns ID of the currently selected project.
         *
         * @return {number} Project ID.
         */
        selected: function() {
            return $projects.panel('selected') || 0;
        },

        /**
         * Selects first project in the panel.
         */
        first: function() {
            var $a = $('a', $projects);
            if ($a.length == 0) {
                GroupsApp.reload();
            }
            else {
                $a.first().click();
            }
        },

        /**
         * Invokes "New project" dialog.
         */
        create: function() {
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_new_project'),
                title: eTraxis.i18n['project.new'],
                success: function() {
                    var name = $('#project_name').val();
                    ProjectsApp.reload(function() {
                        $projects.panel('expand');
                        ProjectsApp.select($projects.panel('find', name));
                    });
                    return true;
                }
            });
        },

        /**
         * Invokes "Edit project" dialog.
         *
         * @param {number} id Project ID.
         */
        edit: function(id) {
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_edit_project', { id: id }),
                title: $projects.panel('get', id),
                success: function() {
                    ProjectsApp.reload(function() {
                        ProjectsApp.select(id);
                    });
                    return true;
                }
            });
        },

        /**
         * Deletes project after confirmation.
         *
         * @param {number} id Project ID.
         */
        delete: function(id) {
            eTraxis.confirm(eTraxis.i18n['button.delete'], eTraxis.i18n['project.confirm.delete'], function() {
                $.post(eTraxis.route('admin_delete_project', { id: id }), function() {
                    ProjectsApp.reload(function() {
                        ProjectsApp.first();
                    });
                });
            });
        }
    };
})();
