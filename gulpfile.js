/*!
 *  Copyright (C) 2015 Artem Rodygin
 *
 *  This file is part of eTraxis.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
 */

var gulp   = require('gulp');
var concat = require('gulp-concat');
var minify = require('gulp-minify-css');
var rename = require('gulp-rename');
var strip  = require('gulp-strip-json-comments');
var uglify = require('gulp-uglify');
var watch  = require('gulp-watch');

/**
 * Performs all installation tasks in one.
 */
gulp.task('default', [
    'stylesheets:libs',
    'javascripts:libs',
    'javascripts:datatables',
    'javascripts:i18n',
    'javascripts:etraxis'
]);

/**
 * Watchs for changes in eTraxis files and updates affected assets when necessary.
 */
gulp.task('watch', function() {
    watch('app/Resources/public/js/**', function() {
        gulp.start('javascripts:i18n');
        gulp.start('javascripts:etraxis');
    });
});

/**
 * Installs vendors CSS files as one combined "web/css/libs.min.css" asset.
 */
gulp.task('stylesheets:libs', function() {

    var files = [
        'vendor/bower/normalize.css/normalize.css',
        'app/Resources/public/css/jquery-ui.structure.css',
        'vendor/bower/datatables/media/css/jquery.dataTables_themeroller.css'
    ];

    gulp.src(files)
        .pipe(minify())
        .pipe(concat('libs.min.css'))
        .pipe(gulp.dest('web/css/'));
});

/**
 * Installs vendors JavaScript files as one combined "web/js/libs.min.js" asset.
 */
gulp.task('javascripts:libs', function() {

    var files = [
        'vendor/bower/jquery/dist/jquery.js',
        'app/Resources/public/js/jquery-ui.js',
        'vendor/bower/jquery-cookie/jquery.cookie.js',
        'vendor/bower/blockui/jquery.blockUI.js',
        'vendor/bower/jquery-form/jquery.form.js',
        'vendor/bower/datatables/media/js/jquery.dataTables.js'
    ];

    gulp.src(files)
        .pipe(uglify())
        .pipe(concat('libs.min.js'))
        .pipe(gulp.dest('web/js/'));

    gulp.src('vendor/bower/html5shiv/dist/html5shiv.min.js')
        .pipe(gulp.dest('web/js/'));
});

/**
 * Installs required DataTables translation (JSON) files to "web/js/datatables/" folder.
 */
gulp.task('javascripts:datatables', function() {

    var i18n = [
        'vendor/bower/datatables-plugins/i18n/Bulgarian.lang',
        'vendor/bower/datatables-plugins/i18n/Czech.lang',
        'vendor/bower/datatables-plugins/i18n/Dutch.lang',
        'vendor/bower/datatables-plugins/i18n/English.lang',
        'vendor/bower/datatables-plugins/i18n/French.lang',
        'vendor/bower/datatables-plugins/i18n/German.lang',
        'vendor/bower/datatables-plugins/i18n/Hungarian.lang',
        'vendor/bower/datatables-plugins/i18n/Italian.lang',
        'vendor/bower/datatables-plugins/i18n/Japanese.lang',
        'vendor/bower/datatables-plugins/i18n/Latvian.lang',
        'vendor/bower/datatables-plugins/i18n/Polish.lang',
        'vendor/bower/datatables-plugins/i18n/Portuguese-Brasil.lang',
        'vendor/bower/datatables-plugins/i18n/Romanian.lang',
        'vendor/bower/datatables-plugins/i18n/Russian.lang',
        'vendor/bower/datatables-plugins/i18n/Spanish.lang',
        'vendor/bower/datatables-plugins/i18n/Swedish.lang',
        'vendor/bower/datatables-plugins/i18n/Turkish.lang'
    ];

    gulp.src(i18n)
        .pipe(rename(function(path) {

            var i18n = {
                'Bulgarian': 'bg',
                'Czech': 'cs',
                'Dutch': 'nl',
                'English': 'en',
                'French': 'fr',
                'German': 'de',
                'Hungarian': 'hu',
                'Italian': 'it',
                'Japanese': 'ja',
                'Latvian': 'lv',
                'Polish': 'pl',
                'Portuguese-Brasil': 'pt_BR',
                'Romanian': 'ro',
                'Russian': 'ru',
                'Spanish': 'es',
                'Swedish': 'sv',
                'Turkish': 'tr'
            };

            path.basename = 'datatables-' + i18n[path.basename];
            path.extname = '.json';
        }))
        .pipe(strip())
        .pipe(gulp.dest('web/js/datatables/'));
});

/**
 * Installs translation JavaScript files from all vendors and from eTraxis to "web/js/" folder.
 */
gulp.task('javascripts:i18n', function() {

    var i18n = [
        'bg',
        'cs',
        'de',
        'en',
        'es',
        'fr',
        'hu',
        'it',
        'ja',
        'lv',
        'nl',
        'pl',
        'pt-BR',
        'ro',
        'ru',
        'sv',
        'tr'
    ];

    i18n.forEach(function(locale) {

        var files = [
            'vendor/bower/jquery.ui/ui/i18n/datepicker-' + (locale == 'en' ? 'en-GB' : locale) + '.js',
            'app/Resources/public/js/i18n/etraxis-' + locale + '.js'
        ];

        gulp.src(files)
            .pipe(uglify())
            .pipe(concat('etraxis-' + locale.replace('-', '_') + '.min.js'))
            .pipe(gulp.dest('web/js/'));
    });
});

/**
 * Installs eTraxis JavaScript files as one combined "web/js/etraxis.min.js" asset.
 */
gulp.task('javascripts:etraxis', function() {

    var files = [
        'app/Resources/public/js/etraxis.js'
    ];

    gulp.src(files)
        .pipe(uglify())
        .pipe(concat('etraxis.min.js'))
        .pipe(gulp.dest('web/js/'));
});
