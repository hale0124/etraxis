/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var RecordApp = (function() {

    /**
     * Reloads current tab to refresh its content.
     */
    var reloadTab = function() {
        var $tabs = $('#tabs-record');
        var current = $tabs.tabs('option', 'active');
        $tabs.tabs('load', current);
    };

    // Initializes current tab after it's been loaded.
    $('#tabs-record').on('tabsload', function(e, ui) {
        var init = $(ui.tab).data('init');
        if (typeof init !== 'undefined') {
            eval(init);
        }
    });

    return {

        /**
         * Initializes the first tab.
         */
        initDetails: function() {

            var $attachments = $('#attachments');

            // Attach new file.
            $attachments.on('click', '#button-file-attach', function(e) {
                e.preventDefault();

                if ($('#attachment_file').val().length !== 0) {

                    var $form          = $('#attachment-form');
                    var $progresslabel = $('#progressbar');
                    var $progressbar   = $progresslabel.parent();

                    $form.ajaxSubmit({

                        beforeSend: function() {
                            $form.remove();
                            $progressbar.progressbar();
                        },

                        complete: function() {
                            var url = eTraxis.route('web_partial_attachments', {id: $('#record-id').val()});
                            $attachments.load(url + ' .fieldset', function () {
                                $(this).initUI();
                            });
                        },

                        error: function(xhr) {
                            eTraxis.alert(eTraxis.i18n['error'], xhr.status === 400 ? xhr.responseText : xhr.statusText);
                        },

                        uploadProgress: function(event, position, total, percentComplete) {
                            $progressbar.progressbar('option', 'value', percentComplete);
                            $progresslabel.text(percentComplete + '%');
                        }
                    });
                }
            });

            // Delete existing file.
            $attachments.on('click', '.button-file-delete', function(e) {
                var $this = $(this);
                e.preventDefault();

                eTraxis.confirm(eTraxis.i18n['button.delete'], eTraxis.i18n['file.confirm.delete'], function() {
                    eTraxis.block();
                    var id = $this.data('id');
                    $.post(eTraxis.route('web_delete_file', { id: id }))
                        .always(function() {
                            var url = eTraxis.route('web_partial_attachments', { id: $('#record-id').val() });
                            $attachments.load(url + ' .fieldset', function() {
                                $(this).initUI();
                                eTraxis.unblock();
                            });
                        })
                        .fail(function(xhr) {
                            eTraxis.alert(eTraxis.i18n['error'], xhr.status === 400 ? xhr.responseText : xhr.statusText);
                        });
                });
            });
        },

        /**
         * Initializes the "History" tab.
         */
        initHistory: function() {
            $('#history').table({
                serverSide: false,
                tableOnly: true
            });
        },

        /**
         * Redirects back to records list.
         */
        back: function() {
            window.location.assign(eTraxis.route('web_records'));
        }
    };
})();
