/*!
 *  Copyright (C) 2014-2016 Artem Rodygin
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the file. If not, see <http://www.gnu.org/licenses/>.
 */

var RecordApp = (function() {

    var TAB_DETAILS = 0;
    var TAB_HISTORY = 1;

    var $tabs = $('#tabs-record');

    /**
     * Reloads current tab to refresh its content.
     */
    var reloadTab = function() {
        var current = $tabs.tabs('option', 'active');
        $tabs.tabs('load', current);
    };

    /**
     * Change current number in the text of specified tab.
     *
     * @param {number} tab   Tab index (zero-based).
     * @param {number} delta Increment for the current number (may be negative).
     */
    var changeTabNumber = function(tab, delta) {
        var $tab = $('ul.ui-tabs-nav li:eq(' + tab + ') a', $tabs);
        var matches = /(.+)\((\d+)\)/.exec($tab.text());
        if (matches !== null && matches.length === 3) {
            var number = parseInt(matches[2]) + delta;
            $tab.text(matches[1] + '(' + number + ')');
        }
    };

    // Initializes current tab after it's been loaded.
    $tabs.on('tabsload', function(e, ui) {
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
                        success: function() {
                            changeTabNumber(TAB_HISTORY, 1);
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
                    $.post({
                        url: eTraxis.route('web_delete_file', { id: id }),
                        complete: function() {
                            var url = eTraxis.route('web_partial_attachments', { id: $('#record-id').val() });
                            $attachments.load(url + ' .fieldset', function() {
                                $(this).initUI();
                                eTraxis.unblock();
                            });
                        },
                        success: function() {
                            changeTabNumber(TAB_HISTORY, 1);
                        },
                        error: function(xhr) {
                            eTraxis.alert(eTraxis.i18n['error'], xhr.status === 400 ? xhr.responseText : xhr.statusText);
                        }
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
