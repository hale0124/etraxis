/*!
 *  Copyright (C) 2015-2016 Artem Rodygin
 *
 *  This file is part of eTraxis.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
 */

var gulp     = require('gulp');
var addsrc   = require('gulp-add-src');
var concat   = require('gulp-concat');
var gulpif   = require('gulp-if');
var insert   = require('gulp-insert');
var less     = require('gulp-less');
var minify   = require('gulp-minify-css');
var plumber  = require('gulp-plumber');
var rename   = require('gulp-rename');
var strip    = require('gulp-strip-json-comments');
var uglify   = require('gulp-uglify');
var watch    = require('gulp-watch');
var yaml     = require('gulp-yaml');
var fs       = require('fs');
var merge    = require('merge-stream');
var sequence = require('run-sequence');
var argv     = require('yargs').argv;

/**
 * Performs all installation tasks in one.
 */
gulp.task('default', function() {
    sequence(
        ['stylesheets:libs', 'stylesheets:themes'],
        ['javascripts:datatables', 'javascripts:translations'],
        ['javascripts:libs', 'javascripts:etraxis', 'javascripts:i18n']
    );
});

/**
 * Watchs for changes in eTraxis files and updates affected assets when necessary.
 */
gulp.task('watch', function() {
    watch(['app/Resources/public/js/**', 'app/Resources/public/less/**'], function() {
        gulp.start('stylesheets:themes');
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
        'vendor/bower/unsemantic/assets/stylesheets/unsemantic-grid-responsive-no-ie7.css',
        'app/Resources/public/css/jquery-ui.structure.css',
        'vendor/bower/datatables/media/css/jquery.dataTables_themeroller.css'
    ];

    return gulp.src(files)
        .pipe(gulpif(argv.production, minify()))
        .pipe(gulpif(argv.production, concat('libs.min.css')))
        .pipe(gulp.dest('web/css/'));
});

/**
 * Installs jQuery UI themes.
 */
gulp.task('stylesheets:themes', function() {

    gulp.src('app/Resources/public/css/*/images/*')
        .pipe(gulp.dest('web/css/'));

    var folders = fs.readdirSync('app/Resources/public/css')
        .filter(function(file) {
            return fs.statSync('app/Resources/public/css/' + file).isDirectory();
        });

    var tasks = folders.map(function(folder) {
        return gulp.src('app/Resources/public/less/themes/theme-' + folder + '.less')
            .pipe(plumber({
                errorHandler: function(error) {
                    console.log(error);
                    this.emit('end');
                }
            }))
            .pipe(less())
            .pipe(addsrc.prepend('app/Resources/public/css/' + folder + '/jquery-ui.theme.css'))
            .pipe(gulpif(argv.production, minify()))
            .pipe(concat(argv.production ? 'etraxis.min.css' : 'etraxis.css'))
            .pipe(gulp.dest('web/css/' + folder));
    });

    return merge(tasks);
});

/**
 * Converts required DataTables translation (JSON) files into JavaScript files.
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

    return gulp.src(i18n)
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
                'Portuguese-Brasil': 'pt-BR',
                'Romanian': 'ro',
                'Russian': 'ru',
                'Spanish': 'es',
                'Swedish': 'sv',
                'Turkish': 'tr'
            };

            path.basename = 'datatables-' + i18n[path.basename];
            path.extname = '.js';
        }))
        .pipe(strip())
        .pipe(insert.prepend('var datatables_language = window.datatables_language ||'))
        .pipe(insert.append(';'))
        .pipe(gulp.dest('vendor/bower/datatables-plugins/i18n/'));
});

/**
 * Converts eTraxis translation YAML files into JavaScript files.
 */
gulp.task('javascripts:translations', function() {

    return gulp.src('app/Resources/translations/messages.*.yml')
        .pipe(yaml({ space: 4 }))
        .pipe(insert.prepend('eTraxis.i18n = '))
        .pipe(insert.prepend('var eTraxis = window.eTraxis || {};\n\n'))
        .pipe(insert.append(';\n'))
        .pipe(rename(function(path) {
            path.basename = path.basename.replace('messages.', 'etraxis-');
            path.extname = '.js';
        }))
        .pipe(gulp.dest('vendor/bower/etraxis/i18n/'));
});

/**
 * Installs vendors JavaScript files as one combined "web/js/libs.min.js" asset.
 */
gulp.task('javascripts:libs', function() {

    var files = [
        'vendor/bower/jquery/dist/jquery.js',
        'app/Resources/public/js/jquery-ui.js',
        'vendor/bower/blockui/jquery.blockUI.js',
        'vendor/bower/jquery-form/jquery.form.js',
        'vendor/bower/datatables/media/js/jquery.dataTables.js'
    ];

    return gulp.src(files)
        .pipe(gulpif(argv.production, uglify()))
        .pipe(gulpif(argv.production, concat('libs.min.js')))
        .pipe(gulp.dest('web/js/'));
});

/**
 * Installs eTraxis JavaScript files as one combined "web/js/etraxis.min.js" asset.
 */
gulp.task('javascripts:etraxis', function() {

    var files = [
        'app/Resources/public/js/etraxis.js',
        'app/Resources/public/js/init-ui.js',
        'app/Resources/public/js/disable.js',
        'app/Resources/public/js/dropdown.js',
        'app/Resources/public/js/modal.js',
        'app/Resources/public/js/panel.js',
        'app/Resources/public/js/table.js'
    ];

    return gulp.src(files)
        .pipe(gulpif(argv.production, uglify()))
        .pipe(concat(argv.production ? 'etraxis.min.js' : 'etraxis.js'))
        .pipe(gulp.dest('web/js/'));
});

/**
 * Installs translation JavaScript files from all vendors (including eTraxis) to "web/js/" folder.
 */
gulp.task('javascripts:i18n', function() {

    var i18n = [
        'bg',
        'cs',
        'de',
        'en-AU',
        'en-CA',
        'en-GB',
        'en-NZ',
        'en-US',
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
            'vendor/bower/jquery.ui/ui/i18n/datepicker-' + (locale.substr(0, 2) == 'en' ? 'en-GB' : locale) + '.js',
            'vendor/bower/datatables-plugins/i18n/datatables-' + (locale.substr(0, 2) == 'en' ? 'en' : locale) + '.js',
            'vendor/bower/etraxis/i18n/etraxis-' + locale.replace('-', '_') + '.js'
        ];

        return gulp.src(files)
            .pipe(gulpif(argv.production, uglify()))
            .pipe(concat('etraxis-' + locale.replace('-', '_') + (argv.production ? '.min.js' : '.js')))
            .pipe(gulp.dest('web/js/'));
    });
});
