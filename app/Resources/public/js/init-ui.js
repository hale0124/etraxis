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
     * Initializes jQuery UI widgets.
     *
     * @returns {$.fn}
     */
    $.fn.initUI = function() {

        // Add default classes.
        $('fieldset', this).addClass('ui-widget-content ui-corner-all');
        $('input[type=text]', this).addClass('ui-widget-content ui-corner-all');
        $('input[type=password]', this).addClass('ui-widget-content ui-corner-all');
        $('textarea', this).addClass('ui-widget-content ui-corner-all');
        $('select', this).addClass('ui-widget-content ui-corner-all');

        // Initialize jQuery UI widgets.
        $('button', this).button();                 // regular buttons
        $('input[type=button]', this).button();     // regular buttons
        $('input[type=submit]', this).button();     // submit buttons
        $('input[type=reset]', this).button();      // reset buttons
        $('.buttonset', this).buttonset();          // buttonsets
        $('.date', this).datepicker();              // datepickers
        $('.hint', this).tooltip();                 // tooltips binded with labels

        // Highlight inline messages.
        $('.highlight:not(.ui-state-highlight)').addClass('ui-state-highlight ui-widget-content ui-corner-all');

        // Error inline messages.
        $('.error:not(.ui-state-error)').addClass('ui-state-error ui-widget-content ui-corner-all');

        // Buttons with dropdown menu.
        $('.dropdown').each(function() {

            var $button = $('button', this);
            var $menu   = $('ul', this);

            $button
                .button({
                    icons: {
                        secondary: 'ui-icon-triangle-1-s'
                    }
                })
                .click(function() {
                    $menu.toggle();
                });

            $menu
                .hide()
                .menu()
                .addClass('ui-front')
                .css('min-width', $button.css('width'))
                .mouseleave(function() {
                    $menu.hide();
                })
                .click(function() {
                    $menu.hide();
                });
        });

        // Tabs.
        $('.tabs', this).each(function() {

            var active = (typeof ($(this).data('tab')) === 'undefined') ? 0 : $(this).data('tab');

            $(this).tabs({

                active: active,

                // Display "please wait" inside the tab while it's being loaded.
                beforeLoad: function(event, ui) {

                    $(ui.panel).html(eTraxis.i18n.PleaseWait);

                    // Display error message inside the tab if load has failed.
                    ui.ajaxSettings.error = function(jqXHR, textStatus) {
                        $(ui.panel).html(textStatus == 'error' ? eTraxis.i18n.Error : textStatus);
                    };
                },

                // Initialize all custom widgets in the just loaded content of the tab.
                load: function(event, ui) {
                    $(ui.panel).initUI();
                }
            });
        });

        // Data tables.
        $('.datatable', this).each(function() {

            var $table = $(this);
            var hasCheckboxes = ($('input[type="checkbox"]', this).length != 0);
            var checkboxName = $('input[type="checkbox"]', this).prop('name');

            $table.dataTable({

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

                order: [
                    hasCheckboxes ? 1 : 0,
                    'asc'
                ],

                columnDefs: [{
                    searchable: !hasCheckboxes,
                    orderable: !hasCheckboxes,
                    targets: 0
                }],

                language: datatables_language,

                createdRow: function(row, data) {
                    if (hasCheckboxes) {
                        $('td', row).first().html('<input type="checkbox" name="' + checkboxName + '[]" value="' + data[0] + '">');
                    }
                }
            });

            $('thead input[type="checkbox"]', this).click(function() {
                $('tbody input[type="checkbox"]', $table).prop('checked', $(this).prop('checked'));
            });

            if ($table.hasClass('hover')) {

                $table.on('draw.dt', function() {

                    $('tbody tr', this).click(function() {
                        $table.trigger('row.click', { id: $(this).data('id') });
                    });

                    $('tbody tr input[type="checkbox"]', this).click(function(e) {
                        e.stopPropagation();
                        return true;
                    });
                });
            }
        });

        return this;
    };

}(jQuery));
