/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var UsersApp = (function() {

    // Initialize users list.
    var $table = $('#users').table({
        checkboxes: 'users'
    });

    // Some buttons are disabled if no checkbox is ticked.
    $table.on('checkbox.click', function(e, data) {
        $('#btn-disable').disable(data.count == 0);
        $('#btn-enable').disable(data.count == 0);
    });

    // Click on a user in the list.
    $table.on('click', 'tbody tr', function(e) {
        var url = eTraxis.route('admin_view_user', { id: $(this).data('id') });
        window.open(url, e.ctrlKey ? '_blank' : '_parent');
    });

    return {

        /**
         * Invokes "New user" dialog.
         */
        create: function() {
            eTraxis.modal({
                url: eTraxis.route('admin_dlg_new_user'),
                title: eTraxis.i18n['user.new'],
                success: function() {
                    $table.api().draw(false);
                    return true;
                }
            });
        },

        /**
         * Disables selected users.
         */
        disable: function() {

            var ids = [];

            $('input[name="users"]:checked', $table).each(function() {
                if ($(this).val() != eTraxis.getUserId()) {
                    ids.push($(this).val());
                }
            });

            if (ids.length) {
                $.post(eTraxis.route('admin_disable_user'), { ids: ids }, function() {
                    $table.api().draw(false);
                });
            }
        },

        /**
         * Enables selected users.
         */
        enable: function() {

            var ids = [];

            $('input[name="users"]:checked', $table).each(function() {
                if ($(this).val() != eTraxis.getUserId()) {
                    ids.push($(this).val());
                }
            });

            if (ids.length) {
                $.post(eTraxis.route('admin_enable_user'), { ids: ids }, function() {
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
                    window.location.assign(eTraxis.route('admin_users_csv') + '?' + params);
                    return true;
                }
            });
        }
    };
})();
