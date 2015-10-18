/*!
 *  Copyright (C) 2015 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

(function($) {

    /**
     * Panel widget.
     */
    $.widget('etraxis.panel', {

        /**
         * @private Constructor.
         */
        _create: function() {
            this.element.append('<div class="panel-body"></div>');
            this.element.addClass('ui-widget-content ui-corner-all');
            $('.panel-heading', this.element).addClass('ui-state-default ui-corner-top');
            $('.panel-body', this.element).addClass('ui-corner-bottom');

            $('.panel-body', this.element).on('click', 'a', function() {
                $(this).trigger('panel.item.click', $(this).data('id'));
                return false;
            });
        },

        /**
         * @private Destructor.
         */
        _destroy: function() {
            this.element.removeClass('ui-widget-content ui-corner-all');
            $('.panel-heading', this.element).removeClass('ui-state-default ui-corner-top');
            $('.panel-body', this.element).remove();
        },

        /**
         * Appends new item.
         * @param id
         * @param text
         */
        append: function(id, text) {
            $('.panel-body', this.element).append(
                '<a data-id="' + id + '" href="#">' +
                '<span class="ui-icon ui-icon-none"></span>' +
                '<span class="panel-item">' + text + '</span>' +
                '</a>'
            );
        },

        /**
         * Removes specified item.
         * @param id
         */
        remove: function(id) {
            $('.panel-body a[data-id="' + id + '"]', this.element).remove();
        },

        /**
         * Removes all items.
         */
        clear: function() {
            $('.panel-body a', this.element).remove();
        },

        /**
         * Selects specified item.
         * @param id
         */
        select: function(id) {
            $('.panel-body span.ui-icon-check', this.element)
                .removeClass('ui-icon-check')
                .addClass('ui-icon-none')
            ;

            $('.panel-body a[data-id="' + id + '"] span.ui-icon', this.element)
                .removeClass('ui-icon-none')
                .addClass('ui-icon-check')
            ;

            $('.panel-body a[data-id="' + id + '"]', this.element).trigger('panel.item.select', id);
        }
    });

}(jQuery));
