<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Composer;

/**
 * Custom scripts for Composer.
 */
class ScriptHandler
{
    /**
     * Installs required npm modules.
     */
    public static function installNpmModules()
    {
        if (!getenv('TRAVIS')) {

            $modules = [
                'gulp',
                'gulp-add-src',
                'gulp-concat',
                'gulp-if',
                'gulp-insert',
                'gulp-less',
                'gulp-minify-css',
                'gulp-plumber',
                'gulp-rename',
                'gulp-strip-json-comments',
                'gulp-uglify',
                'gulp-watch',
                'gulp-yaml',
                'fs',
                'merge-stream',
                'run-sequence',
                'yargs',
            ];

            echo PHP_EOL . 'Installing npm modules' . PHP_EOL;
            system('npm install ' . implode(' ', $modules));
        }
    }

    /**
     * Makes Bower to install front-end libraries, then executes Gulp to process them.
     */
    public static function installAssets()
    {
        if (!getenv('TRAVIS')) {

            echo PHP_EOL . 'Installing assets' . PHP_EOL;
            system('bower install');

            echo PHP_EOL . 'Processing assets' . PHP_EOL;
            system('gulp --production');

            echo PHP_EOL . 'If you work in development environment, please don\'t forget to run "gulp" now.' . PHP_EOL;
        }
    }

    /**
     * Makes Bower to update front-end libraries, then executes Gulp to process them.
     */
    public static function updateAssets()
    {
        if (!getenv('TRAVIS')) {

            echo PHP_EOL . 'Updating assets' . PHP_EOL;
            system('bower update');

            echo PHP_EOL . 'Processing assets' . PHP_EOL;
            system('gulp --production');

            echo PHP_EOL . 'If you work in development environment, please don\'t forget to run "gulp" now.' . PHP_EOL;
        }
    }
}
