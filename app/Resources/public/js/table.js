/*!
 *  Copyright (C) 2012-2015 Artem Rodygin
 *
 *  This file is part of eTraxis.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
 */

var datatables_language = window.datatables_language || {};

(function($) {

    /**
     * Extended DataTable.
     *
     * @returns {$.fn}
     */
    $.fn.table = function(options) {

        var defaults = {

            jQueryUI: true,
            stateSave: true,
            processing: true,
            serverSide: true,
            checkboxes: false,

            columnDefs: [],

            ajax: {
                url: $(this).data('src'),
                error: function(xhr) {
                    eTraxis.alert(eTraxis.i18n.Error, xhr.statusText);
                }
            },

            language: datatables_language
        };

        var settings = $.extend(defaults, options);

        $(this).prop('width', '100%');

        if (settings.checkboxes) {

            settings.order = [1, 'asc'];

            $('thead tr', this).prepend('<th><input type="checkbox" name="characters"></th>');

            settings.columnDefs.push({
                targets: 0,
                orderable: false,
                searchable: false,
                render: function(data) {
                    return '<input type="checkbox" name="' + settings.checkboxes + '[]" value="' + data + '">';
                }
            });
        }

        var $table = $(this).dataTable(settings);

        if (settings.checkboxes) {

            $table.on('click', 'thead input[type="checkbox"]', function() {
                $('tbody input[type="checkbox"]', $table).prop('checked', $(this).prop('checked'));
            });

            $table.on('click', 'tbody tr td:first-child', function(e) {
                e.stopPropagation();
            });
        }

        return $table;
    };

}(jQuery));
