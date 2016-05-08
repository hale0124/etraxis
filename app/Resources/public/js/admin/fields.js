/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var FieldsApp = (function() {

    // Fields panel.
    var $fields = $('#fields');

    // Field was clicked.
    $fields.on('panel.item.click', function(e, data) {
        FieldsApp.select(data);
    });

    // First time initialization.
    $(function() {
        $(document).on('tabsload', '#tabs-field', function() {
            var $tabs = $('#tabs-field');
            $('#group', $tabs).change();
        });
    });

    /**
     * Reloads current tab to refresh its content.
     */
    var reloadTab = function() {
        var $tabs = $('#tabs-field');
        var current = $tabs.tabs('option', 'active');
        $tabs.tabs('load', current);
    };

    return {

        /**
         * Reloads list of fields in the panel.
         *
         * @param {number} id State ID.
         * @param {function} [callback] Optional function to call after the list is loaded.
         */
        reload: function(id, callback) {
            $fields.panel('clear').show();
            $.getJSON(eTraxis.route('admin_fields_list', { id: id }), function(data) {
                $(data).each(function(index, item) {
                    $fields.panel('append', item.id, item.name);
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
            $fields.panel('clear').hide();
        },

        /**
         * Selects specified field in the panel.
         *
         * @param {number} id Field ID.
         */
        select: function(id) {
            $('#content').load(eTraxis.route('admin_view_field', { id: id }), function() {
                $(this).initUI();
                $fields.panel('select', id);
            });
        },

        /**
         * Returns ID of the currently selected field.
         *
         * @return {number} Field ID.
         */
        selected: function() {
            return $fields.panel('selected') || 0;
        },

        /**
         * Invokes "New field" dialog.
         */
        create: function() {
            var id = StatesApp.selected();
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_new_field', { id: id }),
                title: eTraxis.i18n['field.new'],
                open: function() {
                    var $dialog = $('form[name=field]').closest('.ui-dialog');
                    var top = parseInt($dialog.css('top'), 10);
                    $dialog.css('top', top + 25);
                    $('#field_type').change(function() {
                        var value = $(this).val();
                        var type = 'as' + value.charAt(0).toUpperCase() + value.slice(1);

                        if (type == 'asCheckbox') {
                            $('#field_required').parent().parent().hide();
                        }
                        else {
                            $('#field_required').parent().parent().show();
                        }

                        $('.field-type-specific').disable(true).parent().parent().hide();
                        $('.field-type-specific[name^="field[' + type + ']"]').disable(false).parent().parent().show();
                    }).change();
                },
                success: function() {
                    var name = $('#field_name').val();
                    FieldsApp.reload(id, function() {
                        $fields.panel('expand');
                        FieldsApp.select($fields.panel('find', name));
                    });
                    return true;
                }
            });
        },

        /**
         * Invokes "Edit field" dialog.
         *
         * @param {number} id Field ID.
         */
        edit: function(id) {
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_edit_field', { id: id }),
                title: $fields.panel('get', id),
                success: function() {
                    FieldsApp.reload(StatesApp.selected(), function() {
                        FieldsApp.select(id);
                    });
                    return true;
                }
            });
        },

        /**
         * Invokes "PCRE" dialog.
         *
         * @param {number} id Field ID.
         */
        regex: function(id) {
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_regex_field', { id: id }),
                title: 'PCRE',
                success: function() {
                    reloadTab();
                    return true;
                }
            });
        },

        /**
         * Deletes field after confirmation.
         *
         * @param {number} id Field ID.
         */
        delete: function(id) {
            eTraxis.confirm(eTraxis.i18n['button.delete'], eTraxis.i18n['field.confirm.delete'], function() {
                $.post(eTraxis.route('admin_delete_field', { id: id }), function() {
                    $('#content').html(null);
                    StatesApp.select(StatesApp.selected());
                });
            });
        },

        /**
         * Loads permissions for specified field.
         *
         * @param {number} id Field ID.
         */
        loadPermissions: function(id) {
            var $tabs = $('#tabs-field');
            var group = $('#group', $tabs).val();

            var url = (group < 0)
                ? eTraxis.route('admin_fields_load_role_permissions', { id: id, role: group })
                : eTraxis.route('admin_fields_load_group_permissions', { id: id, group: group });

            $.get(url, function(data) {
                $('input[name="permission"][value="' + data + '"]', $tabs).prop('checked', true);
            });
        },

        /**
         * Saves permissions for specified field.
         *
         * @param {number} id Field ID.
         */
        savePermissions: function(id) {
            var $tabs = $('#tabs-field');
            var group = $('#group', $tabs).val();
            var permission = $('input[name="permission"]:checked', $tabs).val();

            var url = (group < 0)
                ? eTraxis.route('admin_fields_save_role_permissions', { id: id, role: group })
                : eTraxis.route('admin_fields_save_group_permissions', { id: id, group: group });

            $.post(url, { permission: permission }, function() {
                eTraxis.alert(eTraxis.i18n['permissions'], eTraxis.i18n['changes_saved']);
            });
        }
    };
})();
