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

            ajax: {
                url: $(this).data('src'),
                error: function(xhr) {
                    eTraxis.alert(eTraxis.i18n.Error, xhr.statusText);
                }
            },

            language: datatables_language
        };

        var settings = $.extend(defaults, options);

        var $table = $(this).dataTable(settings);

        return $table;
    };

}(jQuery));
