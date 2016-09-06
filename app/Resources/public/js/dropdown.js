/*!
 *  Copyright (C) 2012 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

(function($) {

    /**
     * Button with dropdown menu.
     *
     * @returns {*}
     */
    $.fn.dropdown = function() {

        return this.each(function() {

            var $button = $('button', this);
            var $menu   = $('ul', this);

            $button.button({
                icons: {
                    secondary: 'ui-icon-triangle-1-s'
                }
            });

            $button.click(function(e) {
                e.preventDefault();

                $menu.toggle();

                $(document).one('click', function() {
                    $menu.hide();
                });

                return false;
            });

            $menu.hide();
            $menu.menu();
            $menu.addClass('ui-front');
            $menu.css('min-width', $button.css('width'));
        });
    };

}(jQuery));
