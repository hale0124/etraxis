/*!
 *  Copyright (C) 2012-2015 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

(function($) {

    /**
     * Initializes jQuery UI widgets.
     *
     * @returns {*}
     */
    $.fn.initUI = function() {

        return this.each(function() {

            // Add default classes.
            $('fieldset', this).addClass('ui-corner-all');
            $('.fieldset', this).addClass('ui-widget-content ui-corner-all');
            $('input[type=text]', this).addClass('ui-widget-content ui-corner-all');
            $('input[type=email]', this).addClass('ui-widget-content ui-corner-all');
            $('input[type=password]', this).addClass('ui-widget-content ui-corner-all');
            $('textarea', this).addClass('ui-widget-content ui-corner-all');
            $('select', this).addClass('ui-widget-content ui-corner-all');

            // Initialize jQuery UI widgets.
            $('button', this).button();                 // regular buttons
            $('input[type=button]', this).button();     // regular buttons
            $('input[type=submit]', this).button();     // submit buttons
            $('input[type=reset]', this).button();      // reset buttons
            $('.button', this).button();                // regular buttons
            $('.dropdown', this).dropdown();            // buttons with dropdown menu
            $('.buttonset', this).buttonset();          // buttonsets
            $('.date', this).datepicker();              // datepickers
            $('.hint', this).tooltip();                 // tooltips binded with labels
            $('.panel', this).panel();                  // panels

            // Highlight inline messages.
            $('.highlight:not(.ui-state-highlight)', this).addClass('ui-state-highlight ui-widget-content ui-corner-all');

            // Error inline messages.
            $('.error:not(.ui-state-error)', this).addClass('ui-state-error ui-widget-content ui-corner-all');

            // Tabs.
            $('.tabs', this).each(function() {

                var active = (typeof ($(this).data('tab')) === 'undefined') ? 0 : $(this).data('tab');

                $(this).tabs({

                    active: active,

                    // Display "please wait" inside the tab while it's being loaded.
                    beforeLoad: function(event, ui) {

                        $(ui.panel).html('<div class="grid-row"><div class="grid-100">' + eTraxis.i18n['please_wait'] + '</div></div>');

                        // Display error message inside the tab if load has failed.
                        ui.ajaxSettings.error = function(jqXHR, textStatus) {
                            $(ui.panel).html('<div class="grid-row"><div class="grid-100">' + (textStatus == 'error' ? eTraxis.i18n['error'] : textStatus) + '</div></div>');
                        };
                    },

                    // Initialize all custom widgets in the just loaded content of the tab.
                    load: function(event, ui) {
                        $(ui.panel).initUI();
                    }
                });
            });
        });
    };

}(jQuery));
