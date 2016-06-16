/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var RecordsApp = (function() {

    // Initialize records list.
    var $table = $('#records').table({
        checkboxes: 'records'
    });

    // Some buttons are disabled if no checkbox is ticked.
    $table.on('checkbox.click', function(e, data) {
        $('#btn-read').disable(data.count == 0);
        $('#btn-unread').disable(data.count == 0);
    });

    return {

        /**
         * Marks selected records as read.
         */
        read: function() {

            var ids = [];

            $('input[name="records"]:checked', $table).each(function() {
                ids.push($(this).val());
            });

            if (ids.length) {
                $.post(eTraxis.route('web_read_records'), { records: ids }, function() {
                    $table.api().draw(false);
                });
            }
        },

        /**
         * Marks selected records as unread.
         */
        unread: function() {

            var ids = [];

            $('input[name="records"]:checked', $table).each(function() {
                ids.push($(this).val());
            });

            if (ids.length) {
                $.post(eTraxis.route('web_unread_records'), { records: ids }, function() {
                    $table.api().draw(false);
                });
            }
        },

        /**
         * Invokes "Export as CSV" dialog.
         */
        export: function() {
            eTraxis.modal({
                url: eTraxis.route('dlg_export'),
                title: eTraxis.i18n['button.export'],
                success: function() {
                    var params = $('form').serialize() + '&' + $.param($table.api().ajax.params());
                    window.location.assign(eTraxis.route('web_records_csv') + '?' + params);
                    return true;
                }
            });
        }
    };
})();
