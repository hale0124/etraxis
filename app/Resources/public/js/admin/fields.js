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
        }
    };
})();
