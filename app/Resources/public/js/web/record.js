/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var RecordApp = (function() {

    /**
     * Reloads current tab to refresh its content.
     */
    var reloadTab = function() {
        var $tabs = $('#tabs-record');
        var current = $tabs.tabs('option', 'active');
        $tabs.tabs('load', current);
    };

    // Initializes current tab after it's been loaded.
    $('#tabs-record').on('tabsload', function(e, ui) {
        var init = $(ui.tab).data('init');
        if (typeof init !== 'undefined') {
            eval(init);
        }
    });

    return {

        /**
         * Initializes the first tab.
         */
        initDetails: function() {
        },

        /**
         * Initializes the "History" tab.
         */
        initHistory: function() {
            $('#history').table({
                serverSide: false,
                tableOnly: true
            });
        },

        /**
         * Redirects back to records list.
         */
        back: function() {
            window.location.assign(eTraxis.route('web_records'));
        }
    };
})();
