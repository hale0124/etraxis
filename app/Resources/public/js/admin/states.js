/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var StatesApp = (function() {

    // States panel.
    var $states = $('#states');

    // State was clicked.
    $states.on('panel.item.click', function(e, data) {
        StatesApp.select(data);
    });

    // First time initialization.
    $(function() {
        $(document).on('tabsload', '#tabs-state', function() {
            var $tabs = $('#tabs-state');
            $('#group', $tabs).change();
        });
    });

    /**
     * Reloads current tab to refresh its content.
     */
    var reloadTab = function() {
        var $tabs = $('#tabs-state');
        var current = $tabs.tabs('option', 'active');
        $tabs.tabs('load', current);
    };

    return {

        /**
         * Reloads list of states in the panel.
         *
         * @param {number} id Template ID.
         * @param {function} [callback] Optional function to call after the list is loaded.
         */
        reload: function(id, callback) {
            FieldsApp.reset();
            $states.panel('clear').show();
            $.getJSON(eTraxis.route('admin_states_list', { id: id }), function(data) {
                $(data).each(function(index, item) {
                    $states.panel('append', item.id, item.name, false, item.type);
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
            FieldsApp.reset();
            $states.panel('clear').hide();
        },

        /**
         * Selects specified state in the panel.
         *
         * @param {number} id State ID.
         */
        select: function(id) {
            $('#content').load(eTraxis.route('admin_view_state', { id: id }), function() {
                $(this).initUI();
                $states.panel('select', id);
                FieldsApp.reload(id);
            });
        },

        /**
         * Returns ID of the currently selected state.
         *
         * @return {number} State ID.
         */
        selected: function() {
            return $states.panel('selected') || 0;
        },

        /**
         * Invokes "New state" dialog.
         */
        create: function() {
            var id = TemplatesApp.selected();
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_new_state', { id: id }),
                title: eTraxis.i18n['state.new'],
                open: function() {
                    $('#state_type').change(function() {
                        var isFinal = ($('#state_type').val() == 3);
                        $('#state_responsible').disable(isFinal);
                    });
                },
                success: function() {
                    var name = $('#state_name').val();
                    StatesApp.reload(id, function() {
                        $states.panel('expand');
                        StatesApp.select($states.panel('find', name));
                    });
                    return true;
                }
            });
        },

        /**
         * Invokes "Edit state" dialog.
         *
         * @param {number} id State ID.
         */
        edit: function(id) {
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_edit_state', { id: id }),
                title: $states.panel('get', id),
                success: function() {
                    StatesApp.reload(TemplatesApp.selected(), function() {
                        StatesApp.select(id);
                    });
                    return true;
                }
            });
        },

        /**
         * Deletes state after confirmation.
         *
         * @param {number} id State ID.
         */
        delete: function(id) {
            eTraxis.confirm(eTraxis.i18n['button.delete'], eTraxis.i18n['state.confirm.delete'], function() {
                $.post(eTraxis.route('admin_delete_state', { id: id }), function() {
                    $('#content').html(null);
                    TemplatesApp.select(TemplatesApp.selected());
                });
            });
        },

        /**
         * Sets state as initial.
         *
         * @param {number} id State ID.
         */
        initial: function(id) {
            $.post(eTraxis.route('admin_initial_state', { id: id }), function() {
                StatesApp.reload(TemplatesApp.selected(), function() {
                    StatesApp.select(id);
                });
            });
        },

        /**
         * Loads transitions for specified state.
         *
         * @param {number} id State ID.
         */
        loadTransitions: function(id) {
            var $tabs = $('#tabs-state');
            var group = $('#group', $tabs).val();

            var url = (group < 0)
                ? eTraxis.route('admin_states_load_role_transitions', { id: id, role: group })
                : eTraxis.route('admin_states_load_group_transitions', { id: id, group: group });

            $.get(url, function(data) {

                $('input[type="checkbox"].transitions').prop('checked', false);

                $(data).each(function(index, item) {
                    $('#transition-' + item.id).prop('checked', true);
                });
            });
        },

        /**
         * Saves transitions for specified state.
         *
         * @param {number} id State ID.
         */
        saveTransitions: function(id) {
            var $tabs = $('#tabs-state');
            var group = $('#group', $tabs).val();
            var transitions = [];

            $('input[type="checkbox"].transitions:checked', $tabs).each(function() {
                transitions.push($(this).val());
            });

            var url = (group < 0)
                ? eTraxis.route('admin_states_save_role_transitions', { id: id, role: group })
                : eTraxis.route('admin_states_save_group_transitions', { id: id, group: group });

            $.post(url, { transitions: transitions }, function() {
                eTraxis.alert(eTraxis.i18n['state.transitions'], eTraxis.i18n['changes_saved']);
            });
        },

        /**
         * Selects all transition checkboxes in the UI.
         */
        selectAllTransitions: function() {
            var $tabs = $('#tabs-state');
            $('input[type="checkbox"].transitions:not(:disabled)', $tabs).prop('checked', true);
        },

        /**
         * Adds selected responsible groups to the state.
         *
         * @param {number} id State ID.
         */
        addGroups: function(id) {
            var groups = $('#notResponsibleGroup').val();
            if (groups) {
                $.post(eTraxis.route('admin_states_add_responsibles', { id: id }), { groups: groups }, function() {
                    reloadTab();
                });
            }
        },

        /**
         * Removes selected responsible groups from the state.
         *
         * @param {number} id State ID.
         */
        removeGroups: function(id) {
            var groups = $('#responsibleGroup').val();
            if (groups) {
                $.post(eTraxis.route('admin_states_remove_responsibles', { id: id }), { groups: groups }, function() {
                    reloadTab();
                });
            }
        }
    };
})();
