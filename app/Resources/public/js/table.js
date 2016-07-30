/*!
 *  Copyright (C) 2012-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var datatables_language = window.datatables_language || {};

(function($) {

    /**
     * Extended DataTable.
     *
     * @param {object} [options]
     *
     * @returns {jQuery}
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
                ajax: null,
                language: datatables_language
            };

            var settings = $.extend(true, defaults, options);

            // Strip HTML from user data to avoid XSS vulnerabilities.
            $.extend(true, $.fn.dataTable.defaults, {
                column: {
                    render: $.fn.dataTable.render.text()
                }
            });

            // Retrieve data endpoint in case of server-side processing.
            if (settings.serverSide) {
                settings.ajax = {
                    url: $(this).data('src'),
                    error: function(xhr) {
                        tableUnblock($table);
                        eTraxis.alert(eTraxis.i18n['error'], xhr.responseText);
                    }
                };
            }

            // Disable header and footer.
            if (settings.tableOnly) {
                settings.paging = false;
                settings.dom = 't';
            }

            // Timer to block the table while AJAX request is being processed.
            var blockTimer = null;

            // Whether the table is currently blocked.
            var isBlocked = false;

            // Timers to make a delay between requests when searching by columns.
            var searchTimers = [];

            // Current values of searching by columns.
            var searchValues = [];

            for (var i = $('thead th', this).length; i >= 0; i--) {
                searchTimers.push(null);
                searchValues.push('');
            }

            // ID of the last AJAX request.
            // First AJAX request doesn't produce "preXhr.dr" event, but does produce the "xhr.dt" with "1" as draw number.
            var drawNumber = 1;

            // Block the table before AJAX request is sent.
            function tableBlock($table) {

                if (isBlocked) {
                    return;
                }

                isBlocked = true;

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
                        message: eTraxis.i18n['please_wait'],
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

                if (!isBlocked) {
                    return;
                }

                clearTimeout(blockTimer);
                blockTimer = null;
                $table.closest('.dataTables_wrapper').unblock();
                isBlocked = false;
            }

            // Make table width autoadjustable.
            $(this).prop('width', '100%');

            // In case of "checkboxes" feature...
            if (settings.checkboxes) {

                // ... prepend header and footer with one more column.
                $('thead tr', this).prepend('<th></th>');
                $('tfoot tr', this).prepend('<td></td>');

                // If filtering row is absent.
                if ($('tfoot', this).length == 0) {
                    $('thead th:first', this).prepend('<input type="checkbox" class="checkall">');
                }
                else {
                    $('tfoot td:first', this).prepend('<input type="checkbox" class="checkall">');
                }

                // Custom rendering of the first column to convert data into value of a checkbox.
                settings.columnDefs.push({
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    render: function(data, type) {
                        return (type == 'display')
                            ? '<input type="checkbox" name="' + settings.checkboxes + '" value="' + data + '">'
                            : data;
                    }
                });
            }

            // If filtering row is present.
            if ($('tfoot', this).length != 0) {
                $('tfoot td', this).addClass('ui-state-default');
                $('tfoot select', this).prepend('<option></option>').val(null);
            }

            // Call DataTables plugin.
            $table = $(this).dataTable(settings);

            $('input[type="search"]:not([maxlength])', $table.parent()).prop('maxlength', 100);

            // In case of "checkboxes" feature...
            if (settings.checkboxes) {

                // ... implement "check all"/"uncheck all" ability for the checkbox in the header.
                $table.on('click', 'input[type="checkbox"].checkall', function() {
                    $('tbody input[type="checkbox"]', $table).prop('checked', $(this).prop('checked'));

                    // Notify about the event.
                    $table.trigger('checkbox.click', {
                        value: null,
                        count: $('tbody tr td:first-child input[type="checkbox"]:checked', $table).length
                    });
                });

                // Toggle "Check all" if another checkbox is clicked.
                $table.on('click', 'tbody tr td:first-child input[type="checkbox"]', function(e) {

                    var checked = $('tbody tr td:first-child input[type="checkbox"]:checked', $table).length;

                    // If no checkbox is ticked, untick "Select all" checkbox.
                    if (checked == 0) {
                        $('input[type="checkbox"].checkall', $table).prop('checked', false);
                    }

                    // If all checkboxes are ticked, tick "Select all" checkbox.
                    if (checked == $table.api().page.len()) {
                        $('input[type="checkbox"].checkall', $table).prop('checked', true);
                    }

                    // Notify about the event.
                    $table.trigger('checkbox.click', {
                        value: $(this).val(),
                        count: checked
                    });

                    e.stopPropagation();
                });

                // Simulate checkbox click when clicking on the first column.
                $table.on('click dblclick', 'tbody tr td:first-child', function(e) {
                    $('input[type="checkbox"]', this).click();
                    e.stopPropagation();
                });
            }

            // Filter controls.
            $('tfoot input[type="text"]:not([maxlength])', $table).prop('maxlength', 100);
            $('tfoot input[type="text"], tfoot select', $table)
                // Restore saved search values.
                .each(function() {

                    var visibleIndex = $(this).closest('td').index();
                    var index = $table.api().column.index('fromVisible', visibleIndex);
                    var value = $table.api().column(index).search();

                    $(this).val(value);

                    searchValues[index] = value;
                })
                // Re-draw the table when a filter value is changed.
                .on('keyup change', function() {

                    var visibleIndex = $(this).closest('td').index();
                    var index = $table.api().column.index('fromVisible', visibleIndex);
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
                var menuId = 'menu-' + Date.now() + '-' + $table.prop('id');
                $('body > ul.context-menu').remove();
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
                        $table.trigger('contextmenu.click', {
                            id: $(this).parent().data('id'),
                            item: $(this).data('id')
                        });
                    }
                });
            }

            // Destruct the table.
            $table.one('destroy.dt', function() {
                // Unbind all custom handlers.
                $table.off();

                // In case of "checkboxes" feature remove extra column.
                if (settings.checkboxes) {
                    $('thead th:first', this).remove();
                    $('tfoot td:first', this).remove();
                }

                // If filtering row is present, revert filter controls to their initial state.
                if ($('tfoot', this).length != 0) {
                    $('tfoot td', this).removeClass('ui-state-default');
                    $('option:first', $('tfoot select', this)).remove();
                }
            });

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
                    $('input[type="checkbox"].checkall', $table).prop('checked', false);
                    $table.trigger('checkbox.click', {
                        value: null,
                        count: 0
                    });
                    tableUnblock($table);
                }
            });

            // Block the table while first AJAX request is under progress.
            if (settings.serverSide) {
                tableBlock($table);
            }
        });

        return $table;
    };

}(jQuery));
