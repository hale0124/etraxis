<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
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
                'gulp-insert',
                'gulp-less',
                'gulp-minify-css',
                'gulp-plumber',
                'gulp-rename',
                'gulp-strip-json-comments',
                'gulp-uglify',
                'gulp-watch',
                'fs',
                'merge-stream',
                'run-sequence',
            ];

            echo "\nInstalling npm modules\n";
            system('npm install ' . implode(' ', $modules));
        }
    }

    /**
     * Makes Bower to install front-end libraries, then executes Gulp to process them.
     */
    public static function installAssets()
    {
        if (!getenv('TRAVIS')) {

            echo "\nInstalling assets\n";
            system('bower install');

            echo "\nProcessing assets\n";
            system('gulp');
        }
    }

    /**
     * Makes Bower to update front-end libraries, then executes Gulp to process them.
     */
    public static function updateAssets()
    {
        if (!getenv('TRAVIS')) {

            echo "\nUpdating assets\n";
            system('bower update');

            echo "\nProcessing assets\n";
            system('gulp');
        }
    }
}
