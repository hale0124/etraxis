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

            $('.panel-heading', this.element).prepend('<span class="ui-icon ui-icon-circle-triangle-n panel-toggle" title="' + eTraxis.i18n.Collapse + '"></span>');

            $('.panel-heading', this.element).on('click', '> div', function() {
                $('.panel-toggle', $(this).parent()).click();
                return false;
            });

            $('.panel-heading', this.element).on('click', '.panel-toggle', function() {
                if ($(this).hasClass('ui-icon-circle-triangle-n')) {
                    $(this)
                        .removeClass('ui-icon-circle-triangle-n')
                        .addClass('ui-icon-circle-triangle-s')
                        .attr('title', eTraxis.i18n.Expand)
                    ;
                    $('.panel-body', $(this).closest('.panel')).hide();
                }
                else {
                    $(this)
                        .removeClass('ui-icon-circle-triangle-s')
                        .addClass('ui-icon-circle-triangle-n')
                        .attr('title', eTraxis.i18n.Collapse)
                    ;
                    $('.panel-body', $(this).closest('.panel')).show();
                }
                return false;
            });

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
            $('.panel-heading .panel-toggle', this.element).remove();
            $('.panel-body', this.element).remove();
        },

        /**
         * Collapses panel items.
         */
        collapse: function() {
            $('.panel-heading .panel-toggle', this.element)
                .removeClass('ui-icon-circle-triangle-n')
                .addClass('ui-icon-circle-triangle-s')
                .attr('title', eTraxis.i18n.Expand)
            ;
            $('.panel-body', this.element).hide();
        },

        /**
         * Expands panel items.
         */
        expand: function() {
            $('.panel-heading .panel-toggle', this.element)
                .removeClass('ui-icon-circle-triangle-s')
                .addClass('ui-icon-circle-triangle-n')
                .attr('title', eTraxis.i18n.Collapse)
            ;
            $('.panel-body', this.element).show();
        },

        /**
         * Appends new item.
         * @param {string} id
         * @param {string} text
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
         * @param {string} id
         */
        remove: function(id) {
            $('.panel-body a[data-id="' + id + '"]', this.element).remove();
        },

        /**
         * Removes all items.
         */
        clear: function() {
            $('.panel-body', this.element).empty();
        },

        /**
         * Finds specified item by its text.
         * @param {string} text
         * @returns {string} id
         */
        find: function(text) {
            var result = '';

            $('.panel-item', this.element).each(function() {
                if ($(this).text() == text) {
                    result = $(this).closest('a').data('id');
                    return false;
                }
            });

            return result;
        },

        /**
         * Selects specified item.
         * @param {string} id
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
        },

        /**
         * Finds currently selected item.
         * @returns {string} id
         */
        selected: function() {
            var $selected = $('.panel-body span.ui-icon-check', this.element);
            return $selected.length == 0 ? '' : $selected.parent().data('id');
        }
    });

}(jQuery));
