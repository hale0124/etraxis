/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var RecordsApp = (function() {

    // Initialize records list.
    var $table = $('#records').table({
        checkboxes: 'records',
        columns: [
            { data: 'id' },
            { data: 'record' },
            { data: 'project' },
            { data: 'state' },
            { data: 'subject' },
            { data: 'author' },
            { data: 'responsible' },
            { data: 'age' }
        ],
        columnDefs: [
            {
                targets: [1, 2, 3, 5, 6, 7],
                className: 'nowrap'
            },
            {
                targets: 4,
                width: '100%'
            }
        ]
    });

    // Some buttons are disabled if no checkbox is ticked.
    $table.on('checkbox.click', function(e, data) {
        $('#btn-read').disable(data.count == 0);
        $('#btn-unread').disable(data.count == 0);
    });

    // Click on a record in the list.
    $table.on('click', 'tbody tr', function(e) {
        var url = eTraxis.route('web_view_record', { id: $(this).data('id') });
        window.open(url, e.ctrlKey ? '_blank' : '_parent');
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
