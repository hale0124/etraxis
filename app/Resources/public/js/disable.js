/*!
 *  Copyright (C) 2015 Artem Rodygin
 *
 *  This file is part of eTraxis.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
 */

(function($) {

    /**
     * Shortcut to disable UI widgets.
     *
     * @param {bool} state New state of target widgets.
     *
     * @returns {$.fn}
     */
    $.fn.disable = function(state) {

        $(this).prop('disabled', state);

        if (state) {
            $('label[for="' + $(this).prop('id') + '"]').addClass('ui-state-disabled');
            $(this).addClass('ui-state-disabled');
        }
        else {
            $('label[for="' + $(this).prop('id') + '"]').removeClass('ui-state-disabled');
            $(this).removeClass('ui-state-disabled');
        }

        return this;
    };

}(jQuery));
