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
            processing: false,
            serverSide: true,
            checkboxes: false,
            tableOnly: false,

            columnDefs: [],

            ajax: {
                url: $(this).data('src'),
                error: function(xhr) {
                    eTraxis.alert(eTraxis.i18n.Error, xhr.statusText);
                }
            },

            language: datatables_language
        };

        var settings = $.extend(true, defaults, options);

        // Disable header and footer.
        if (settings.tableOnly) {
            settings.dom = 't';
        }

        // Timer to block the table while AJAX request is being processed.
        var blockTimer = null;

        // ID of the last AJAX request.
        // First AJAX request doesn't produce "preXhr.dr" event, but does produce the "xhr.dt" with "1" as draw number.
        var drawNumber = 1;

        // Block the table before AJAX request is sent.
        function tableBlock($table) {

            // Most requests are supposed to be processed within few milliseconds.
            // To avoid visual flickering we initially block with invisible overlay.
            $table.closest('.dataTables_wrapper').block({
                message: null,
                title: null,
                theme: false,
                overlayCSS: {
                    opacity: 0
                }
            });

            // If response is not received for a long, re-block with visible overlay.
            blockTimer = setTimeout(function() {
                $table.closest('.dataTables_wrapper').block({
                    message: eTraxis.i18n.PleaseWait,
                    title: null,
                    theme: true,
                    themedCSS: {
                        padding: '10px'
                    }
                });
            }, 500);
        }

        // Unblock the table when server response is received.
        function tableUnblock($table) {
            clearTimeout(blockTimer);
            $table.closest('.dataTables_wrapper').unblock();
        }

        // Make table width autoadjustable.
        $(this).prop('width', '100%');

        // In case of "checkboxes" feature...
        if (settings.checkboxes) {

            // ...prepend the header with one more column.
            $('thead tr', this).prepend('<th><input type="checkbox"></th>');

            // Custom rendering of the first column to convert data into value of a checkbox.
            settings.columnDefs.push({
                targets: 0,
                orderable: false,
                searchable: false,
                render: function(data) {
                    return '<input type="checkbox" name="' + settings.checkboxes + '[]" value="' + data + '">';
                }
            });
        }

        // Call DataTables plugin.
        var $table = $(this).dataTable(settings);

        // In case of "checkboxes" feature...
        if (settings.checkboxes) {

            // ... implement "check all"/"uncheck all" ability for the checkbox in the header.
            $table.on('click', 'thead input[type="checkbox"]', function() {
                $('tbody input[type="checkbox"]', $table).prop('checked', $(this).prop('checked'));
            });

            // Avoid "click" event on the first column.
            $table.on('click', 'tbody tr td:first-child', function(e) {
                e.stopPropagation();
            });
        }

        // Before each AJAX request block the table until server response.
        $table.on('preXhr.dt', function(e, settings, data) {
            drawNumber = data.draw;
            tableBlock($table);
        });

        // When server responded unblock the table.
        $table.on('xhr.dt', function(e, settings, json) {
            // In case of race condition we can receive a response on previous request,
            // while recent request is still under processing.
            // Unblock only if no more responses are expected.
            if (json.draw == drawNumber) {
                tableUnblock($table);
            }
        });

        // Block the table while first AJAX request is under progress.
        tableBlock($table);

        return $table;
    };

}(jQuery));
