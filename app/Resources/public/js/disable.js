/*!
 *  Copyright (C) 2015 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

(function($) {

    /**
     * Shortcut to disable UI widgets.
     *
     * @param {bool} state New state of target widgets.
     *
     * @returns {*}
     */
    $.fn.disable = function(state) {

        return this.each(function() {

            $(this).prop('disabled', state);

            if (state) {
                $('label[for="' + $(this).prop('id') + '"]').addClass('ui-state-disabled');
                $(this).addClass('ui-state-disabled');

                if ($(this).hasClass('ui-button')) {
                    $(this).button('disable');
                }
            }
            else {
                $('label[for="' + $(this).prop('id') + '"]').removeClass('ui-state-disabled');
                $(this).removeClass('ui-state-disabled');

                if ($(this).hasClass('ui-button')) {
                    $(this).button('enable');
                }
            }
        });
    };

}(jQuery));
