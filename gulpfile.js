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
var exec     = require('gulp-exec');
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
        // First sequence.
        [
            'jquery-ui:stylesheets',    // assemble jQuery UI stylesheets into single "jquery-ui.css" file
            'jquery-ui:javascripts',    // assemble jQuery UI sources into single "jquery-ui.js" script
            'datatables:translations',  // convert required DataTables translation (JSON) files into JavaScript files
            'etraxis:translations',     // convert eTraxis translation YAML files into JavaScript files
            'etraxis:routes',           // generate a JavaScript file with all existing eTraxis routes
            'etraxis:themes'            // install jQuery UI themes to "web/css/" folder
        ],
        // Second sequence.
        [
            'vendor:css',       // install vendor CSS files as one combined "web/css/vendor.min.css" asset
            'vendor:js',        // install vendor JavaScript files as one combined "web/js/vendor.min.js" asset
            'etraxis:core',     // install eTraxis core JavaScript files as one combined "web/js/etraxis.min.js" asset
            'etraxis:app',      // install eTraxis application JavaScript files to "web/js/" folder
            'etraxis:i18n'      // install all translation JavaScript files to "web/js/" folder
        ]
    );
});

/**
 * Watchs for changes in eTraxis files and updates affected assets when necessary.
 */
gulp.task('watch', function() {
    watch(['app/Resources/public/less/*.less', 'app/Resources/public/less/*/**.less'], function() {
        gulp.start('etraxis:themes');
    });
    watch(['app/Resources/public/js/*.js'], function() {
        gulp.start('etraxis:core');
    });
    watch(['app/Resources/public/js/*/**.js'], function() {
        gulp.start('etraxis:app');
    });
});

/**
 * Assembles jQuery UI stylesheets into single "jquery-ui.css" file.
 */
gulp.task('jquery-ui:stylesheets', function() {

    var files = [
        // UI Core
        'vendor/bower/jquery.ui/themes/base/core.css',
        // Interactions
        'vendor/bower/jquery.ui/themes/base/draggable.css',
        'vendor/bower/jquery.ui/themes/base/resizable.css',
        'vendor/bower/jquery.ui/themes/base/sortable.css',
        // Widgets
        'vendor/bower/jquery.ui/themes/base/button.css',
        'vendor/bower/jquery.ui/themes/base/datepicker.css',
        'vendor/bower/jquery.ui/themes/base/dialog.css',
        'vendor/bower/jquery.ui/themes/base/menu.css',
        'vendor/bower/jquery.ui/themes/base/progressbar.css',
        'vendor/bower/jquery.ui/themes/base/tabs.css',
        'vendor/bower/jquery.ui/themes/base/tooltip.css'
    ];

    return gulp.src(files)
        .pipe(gulpif(argv.production, minify()))
        .pipe(concat('jquery-ui.css'))
        .pipe(gulp.dest('vendor/bower/jquery.ui/themes/'));
});

/**
 * Assembles jQuery UI sources into single "jquery-ui.js" script.
 */
gulp.task('jquery-ui:javascripts', function() {

    var files = [
        // UI Core
        'vendor/bower/jquery.ui/ui/core.js',
        'vendor/bower/jquery.ui/ui/widget.js',
        'vendor/bower/jquery.ui/ui/mouse.js',
        'vendor/bower/jquery.ui/ui/position.js',
        // Interactions
        'vendor/bower/jquery.ui/ui/draggable.js',
        'vendor/bower/jquery.ui/ui/resizable.js',
        'vendor/bower/jquery.ui/ui/sortable.js',
        // Widgets
        'vendor/bower/jquery.ui/ui/button.js',
        'vendor/bower/jquery.ui/ui/datepicker.js',
        'vendor/bower/jquery.ui/ui/dialog.js',
        'vendor/bower/jquery.ui/ui/menu.js',
        'vendor/bower/jquery.ui/ui/progressbar.js',
        'vendor/bower/jquery.ui/ui/tabs.js',
        'vendor/bower/jquery.ui/ui/tooltip.js'
    ];

    return gulp.src(files)
        .pipe(concat('jquery-ui.js'))
        .pipe(gulp.dest('vendor/bower/jquery.ui/ui/'));
});

/**
 * Converts required DataTables translation (JSON) files into JavaScript files.
 */
gulp.task('datatables:translations', function() {

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
gulp.task('etraxis:translations', function() {

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
 * Generates a JavaScript file with all existing eTraxis routes.
 */
gulp.task('etraxis:routes', function() {

    var options = {
        pipeStdout: true
    };

    return gulp.src('gulpfile.js')
        .pipe(exec('./bin/console etraxis:routes', options))
        .pipe(rename(function(path) {
            path.basename = 'routes';
        }))
        .pipe(gulp.dest('vendor/bower/etraxis/'));
});

/**
 * Installs jQuery UI themes to "web/css/" folder.
 */
gulp.task('etraxis:themes', function() {

    var folders = fs.readdirSync('app/Resources/public/css')
        .filter(function(file) {
            return fs.statSync('app/Resources/public/css/' + file).isDirectory();
        });

    var tasks = folders.map(function(folder) {
        return gulp.src('app/Resources/public/less/themes/theme-' + folder + '.less')
            .pipe(plumber())
            .pipe(less())
            .pipe(addsrc.prepend('app/Resources/public/css/' + folder + '/jquery-ui.theme.css'))
            .pipe(gulpif(argv.production, minify()))
            .pipe(concat(argv.production ? 'etraxis.min.css' : 'etraxis.css'))
            .pipe(gulp.dest('web/css/' + folder));
    });

    tasks.push(
        gulp.src('app/Resources/public/css/*/images/*')
            .pipe(gulp.dest('web/css/'))
    );
    
    return merge(tasks);
});

/**
 * Installs vendor CSS files as one combined "web/css/vendor.min.css" asset.
 */
gulp.task('vendor:css', function() {

    var files = [
        'vendor/bower/normalize.css/normalize.css',
        'vendor/bower/unsemantic/assets/stylesheets/unsemantic-grid-responsive-no-ie7.css',
        'vendor/bower/jquery.ui/themes/jquery-ui.css',
        'vendor/bower/datatables/media/css/jquery.dataTables_themeroller.css'
    ];

    return gulp.src(files)
        .pipe(gulpif(argv.production, minify()))
        .pipe(gulpif(argv.production, concat('vendor.min.css')))
        .pipe(gulp.dest('web/css/'));
});

/**
 * Installs vendor JavaScript files as one combined "web/js/vendor.min.js" asset.
 */
gulp.task('vendor:js', function() {

    var files = [
        'vendor/bower/jquery/dist/jquery.js',
        'vendor/bower/jquery.ui/ui/jquery-ui.js',
        'vendor/bower/blockui/jquery.blockUI.js',
        'vendor/bower/jquery-form/jquery.form.js',
        'vendor/bower/datatables/media/js/jquery.dataTables.js'
    ];

    return gulp.src(files)
        .pipe(gulpif(argv.production, uglify()))
        .pipe(gulpif(argv.production, concat('vendor.min.js')))
        .pipe(gulp.dest('web/js/'));
});

/**
 * Installs eTraxis core JavaScript files as one combined "web/js/etraxis.min.js" asset.
 */
gulp.task('etraxis:core', function() {

    var files = [
        // This file must go first as it defines the "eTraxis" object,
        // which is welcome to be used or extended in any file below.
        'app/Resources/public/js/etraxis.js',
        'app/Resources/public/js/init-ui.js',
        'app/Resources/public/js/disable.js',
        'app/Resources/public/js/dropdown.js',
        'app/Resources/public/js/modal.js',
        'app/Resources/public/js/panel.js',
        'app/Resources/public/js/table.js',
        'vendor/bower/etraxis/routes.js'
    ];

    return gulp.src(files)
        .pipe(plumber())
        .pipe(gulpif(argv.production, uglify()))
        .pipe(concat(argv.production ? 'etraxis.min.js' : 'etraxis.js'))
        .pipe(insert.prepend('"use strict";\n'))
        .pipe(gulp.dest('web/js/'));
});

/**
 * Installs eTraxis application JavaScript files to "web/js/" folder.
 */
gulp.task('etraxis:app', function() {

    return gulp.src('app/Resources/public/js/*/**.js')
        .pipe(plumber())
        .pipe(gulpif(argv.production, uglify()))
        .pipe(insert.prepend('"use strict";\n'))
        .pipe(gulp.dest('web/js/'));
});

/**
 * Installs translation JavaScript files from all vendors (including eTraxis) to "web/js/" folder.
 */
gulp.task('etraxis:i18n', function() {

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
