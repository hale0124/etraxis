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

        var $table = this;

        this.each(function() {

            var defaults = {

                jQueryUI: true,
                stateSave: true,
                processing: false,
                serverSide: true,
                checkboxes: false,
                tableOnly: false,
                contextMenu: false,
                contextMenuCallback: null,

                columnDefs: [],

                ajax: {
                    url: $(this).data('src'),
                    error: function(xhr) {
                        tableUnblock($table);
                        eTraxis.alert(eTraxis.i18n.Error, xhr.responseText);
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

            // Timers to make a delay between requests when searching by columns.
            var searchTimers = [];

            // Current values of searching by columns.
            var searchValues = [];

            for (var i = $('thead:first th', this).length; i >= 0; i--) {
                searchTimers.push(null);
                searchValues.push('');
            }

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
                }, 400);
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

                // ... prepend the header with one more column.
                $('thead tr', this).prepend('<th></th>');
                $('thead:last th:first', this).prepend('<input type="checkbox">');

                // Custom rendering of the first column to convert data into value of a checkbox.
                settings.columnDefs.push({
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return '<input type="checkbox" name="' + settings.checkboxes + '" value="' + data + '">';
                    }
                });
            }

            // If filtering row is present.
            if ($('thead', this).length > 1) {
                $('thead:last').addClass('filter');
                $('thead.filter th').addClass('ui-state-default');
                $('thead.filter select').prepend('<option></option>').val(null);
            }

            // Call DataTables plugin.
            $table = $(this).dataTable(settings);

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

            // Filter controls.
            $('.filter input[type="text"], .filter select')
                // Restore saved search values.
                .each(function() {

                    var index = $(this).closest('th').index();
                    var value = $table.api().column(index).search();

                    $(this).val(value);

                    searchValues[index] = value;
                })
                // Re-draw the table when a filter value is changed.
                .on('keyup change', function() {

                    var index = $(this).closest('th').index();
                    var value = $(this).val();

                    if (searchValues[index] == value) {
                        return;
                    }

                    if (searchTimers[index]) {
                        clearTimeout(searchTimers[index]);
                    }

                    searchTimers[index] = null;
                    searchValues[index] = value;

                    searchTimers[index] = setTimeout(function() {
                        searchTimers[index] = null;
                        $table
                            .api()
                            .column(index)
                            .search(searchValues[index])
                            .draw();
                    }, 400);
                });

            // Prepare context menu.
            if (settings.contextMenu) {

                // Create new and unique context menu.
                var menuId = 'menu-' + Date.now();
                $('body').append('<ul id="' + menuId + '" class="ui-front context-menu"></ul>');
                var $menu = $('#' + menuId);

                // Append menu items.
                for (var id in settings.contextMenu) {
                    if (settings.contextMenu.hasOwnProperty(id)) {
                        $menu.append('<li data-id="' + id + '">' + settings.contextMenu[id] + '</li>');
                    }
                }

                // Initialize menu.
                $menu.menu().hide();

                // Right click on a row in the table.
                $table.on('contextmenu', 'tbody tr', function(e) {
                    $menu.data('id', $(this).data('id'));

                    if (typeof settings.contextMenuCallback === 'function') {
                        settings.contextMenuCallback($menu);
                    }

                    $menu.css('left', e.pageX);
                    $menu.css('top', e.pageY);
                    $menu.show();

                    $(document).one('click', function() {
                        $menu.hide();
                    });

                    return false;
                });

                // Click on an item in the context menu.
                $('li', $menu).click(function() {
                    if (!$(this).hasClass('ui-state-disabled')) {
                        $table.trigger('contextmenuitem', {
                            row: $(this).parent().data('id'),
                            action: $(this).data('id')
                        });
                    }
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
        });

        return $table;
    };

}(jQuery));
