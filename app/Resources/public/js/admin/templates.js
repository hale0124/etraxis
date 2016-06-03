/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var TemplatesApp = (function() {

    // Templates panel.
    var $templates = $('#templates');

    // Template was clicked.
    $templates.on('panel.item.click', function(e, data) {
        TemplatesApp.select(data);
    });

    // First time initialization.
    $(function() {
        $(document).on('tabsload', '#tabs-template', function() {
            var $tabs = $('#tabs-template');
            $('#group', $tabs).change();
        });
    });

    return {

        /**
         * Reloads list of templates in the panel.
         *
         * @param {number} id Project ID.
         * @param {function} [callback] Optional function to call after the list is loaded.
         */
        reload: function(id, callback) {
            StatesApp.reset();
            $templates.panel('clear').show();
            $.getJSON(eTraxis.route('admin_templates_list', { id: id }), function(data) {
                $(data).each(function(index, item) {
                    $templates.panel('append', item['id'], item['name'], item['isLocked']);
                });
                if (typeof callback === 'function') {
                    callback();
                }
            });
        },

        /**
         * Clears and hides the panel.
         */
        reset: function() {
            StatesApp.reset();
            $templates.panel('clear').hide();
        },

        /**
         * Selects specified template in the panel.
         *
         * @param {number} id Template ID.
         */
        select: function(id) {
            $('#content').load(eTraxis.route('admin_view_template', { id: id }), function() {
                $(this).initUI();
                $templates.panel('select', id);
                StatesApp.reload(id);
            });
        },

        /**
         * Returns ID of the currently selected template.
         *
         * @return {number} Template ID.
         */
        selected: function() {
            return $templates.panel('selected') || 0;
        },

        /**
         * Invokes "New template" dialog.
         */
        create: function() {
            var id = ProjectsApp.selected();
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_new_template', { id: id }),
                title: eTraxis.i18n['template.new'],
                success: function() {
                    var name = $('#template_name').val();
                    TemplatesApp.reload(id, function() {
                        $templates.panel('expand');
                        TemplatesApp.select($templates.panel('find', name));
                    });
                    return true;
                }
            });
        },

        /**
         * Invokes "Edit template" dialog.
         *
         * @param {number} id Template ID.
         */
        edit: function(id) {
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_edit_template', { id: id }),
                title: $templates.panel('get', id),
                success: function() {
                    TemplatesApp.reload(ProjectsApp.selected(), function() {
                        TemplatesApp.select(id);
                    });
                    return true;
                }
            });
        },

        /**
         * Deletes template after confirmation.
         *
         * @param {number} id Template ID.
         */
        delete: function(id) {
            eTraxis.confirm(eTraxis.i18n['button.delete'], eTraxis.i18n['template.confirm.delete'], function() {
                $.post(eTraxis.route('admin_delete_template', { id: id }), function() {
                    $('#content').html(null);
                    ProjectsApp.select(ProjectsApp.selected());
                });
            });
        },

        /**
         * Locks template.
         *
         * @param {number} id Template ID.
         */
        lock: function(id) {
            $.post(eTraxis.route('admin_lock_template', { id: id }), function() {
                TemplatesApp.reload(ProjectsApp.selected(), function() {
                    TemplatesApp.select(id);
                });
            });
        },

        /**
         * Unlocks template.
         *
         * @param {number} id Template ID.
         */
        unlock: function(id) {
            $.post(eTraxis.route('admin_unlock_template', { id: id }), function() {
                TemplatesApp.reload(ProjectsApp.selected(), function() {
                    TemplatesApp.select(id);
                });
            });
        },

        /**
         * Loads permissions for specified template.
         *
         * @param {number} id Template ID.
         */
        loadPermissions: function(id) {
            var $tabs = $('#tabs-template');
            var group = $('#group', $tabs).val();

            var url = isNaN(parseInt(group))
                ? eTraxis.route('admin_templates_load_role_permissions', { id: id, role: group })
                : eTraxis.route('admin_templates_load_group_permissions', { id: id, group: group });

            $.get(url, function(data) {

                if (group == 'author' || group == 'responsible') {
                    $('#template_permission_view_records', $tabs).disable(true).prop('checked', true);
                    $('#template_permission_create_records', $tabs).disable(true).prop('checked', false);
                }
                else {
                    $('#template_permission_view_records, #template_permission_create_records', $tabs).disable(false);
                }

                $('input[type="checkbox"].permissions:not(:disabled)', $tabs).prop('checked', false);

                $(data).each(function(index, item) {
                    $('input[type="checkbox"][value="' + item + '"].permissions:not(:disabled)', $tabs).prop('checked', true);
                });
            });
        },

        /**
         * Saves permissions for specified template.
         *
         * @param {number} id Template ID.
         */
        savePermissions: function(id) {
            var $tabs = $('#tabs-template');
            var group = $('#group', $tabs).val();
            var permissions = [];

            $('input[type="checkbox"].permissions:checked', $tabs).each(function() {
                permissions.push($(this).val());
            });

            var url = isNaN(parseInt(group))
                ? eTraxis.route('admin_templates_save_role_permissions', { id: id, role: group })
                : eTraxis.route('admin_templates_save_group_permissions', { id: id, group: group });

            $.post(url, { permissions: permissions }, function() {
                eTraxis.alert(eTraxis.i18n['permissions'], eTraxis.i18n['changes_saved']);
            });
        },

        /**
         * Selects all permission checkboxes in the UI.
         */
        selectAllPermissions: function() {
            var $tabs = $('#tabs-template');
            $('input[type="checkbox"].permissions:not(:disabled)', $tabs).prop('checked', true);
        }
    };
})();
