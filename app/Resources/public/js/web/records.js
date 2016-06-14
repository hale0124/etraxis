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

    return {

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
