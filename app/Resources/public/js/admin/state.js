/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var StateApp = (function() {

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
