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

            var sections = parseInt($(this.element).data('sections'), 10);

            if (!sections || sections < 1) {
                sections = 1;
            }

            for (var i = 1; i <= sections;  i++) {
                this.element.append('<div class="panel-body" data-id="' + i + '"><p>—</p></div>');
            }

            this.element.addClass('ui-widget-content ui-corner-all');

            $('.panel-heading', this.element)
                .addClass('ui-state-default ui-corner-top')
                .prepend('<span class="ui-icon ui-icon-circle-triangle-n panel-toggle" title="' + eTraxis.i18n['button.collapse'] + '"></span>')
            ;

            $('.panel-heading', this.element).on('click', '> div', function(e) {
                e.preventDefault();
                $('.panel-toggle', $(this).parent()).click();
            });

            $('.panel-heading', this.element).on('click', '.panel-toggle', function(e) {
                e.preventDefault();

                if ($(this).hasClass('ui-icon-circle-triangle-n')) {
                    $(this)
                        .removeClass('ui-icon-circle-triangle-n')
                        .addClass('ui-icon-circle-triangle-s')
                        .attr('title', eTraxis.i18n['button.expand'])
                    ;
                    $('.panel-body', $(this).closest('.panel')).hide();
                }
                else {
                    $(this)
                        .removeClass('ui-icon-circle-triangle-s')
                        .addClass('ui-icon-circle-triangle-n')
                        .attr('title', eTraxis.i18n['button.collapse'])
                    ;
                    $('.panel-body', $(this).closest('.panel')).show();
                }
            });

            $('.panel-body', this.element).on('click', 'a', function(e) {
                e.preventDefault();
                $(this).trigger('panel.item.click', $(this).data('id'));
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
                .attr('title', eTraxis.i18n['button.expand'])
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
                .attr('title', eTraxis.i18n['button.collapse'])
            ;

            $('.panel-body', this.element).show();
        },

        /**
         * Appends new item.
         *
         * @param {string} id
         * @param {string} text
         * @param {bool}   [highlighted]
         * @param {int}    [section]
         */
        append: function(id, text, highlighted, section) {

            if (!section) {
                section = 1;
            }

            $('.panel-body[data-id="' + section + '"] > p', this.element).remove();

            $('.panel-body[data-id="' + section + '"]', this.element).append(
                '<a data-id="' + id + '" href="#"' + (highlighted ? ' class="highlighted">' : '>') +
                '<span class="ui-icon ui-icon-none"></span>' +
                '<span class="panel-item">' + text + '</span>' +
                '</a>'
            );
        },

        /**
         * Removes specified item.
         *
         * @param {string} id
         */
        remove: function(id) {

            $('.panel-body a[data-id="' + id + '"]', this.element).remove();

            if ($('.panel-body > a', this.element).length == 0) {
                $('.panel-body', this.element).append('<p>—</p>');
            }
        },

        /**
         * Removes all items.
         */
        clear: function() {

            $('.panel-body', this.element)
                .empty()
                .append('<p>—</p>')
            ;
        },

        /**
         * Gets specified item by its ID.
         *
         * @param {string} id
         * @returns {string} text
         */
        get: function(id) {

            return $('.panel-body a[data-id="' + id + '"] .panel-item', this.element).text();
        },

        /**
         * Finds specified item by its text.
         *
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
         *
         * @param {string} id
         */
        select: function(id) {

            $('.panel-body span.ui-icon-check', this.element)
                .removeClass('ui-icon-check')
                .addClass('ui-icon-none')
            ;

            $('.panel-body a[data-id="' + id + '"] span.ui-icon-none', this.element)
                .removeClass('ui-icon-none')
                .addClass('ui-icon-check')
            ;

            $('.panel-body a[data-id="' + id + '"]', this.element).trigger('panel.item.select', id);
        },

        /**
         * Finds currently selected item.
         *
         * @returns {string} id
         */
        selected: function() {

            var $selected = $('.panel-body span.ui-icon-check', this.element);

            return $selected.length == 0 ? '' : $selected.parent().data('id');
        }
    });

}(jQuery));
