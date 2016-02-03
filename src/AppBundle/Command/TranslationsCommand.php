<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to update all translation files at once.
 */
class TranslationsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('etraxis:translations')
            ->setDescription('Updates all translation files after they have been downloaded from Crowdin')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locales = [
            'en_AU',
            'en_CA',
            'en_GB',
            'en_NZ',
            'en_US',
            'pt_BR',
        ];

        // Rename ISO 15897 into ISO 639-1.
        foreach (scandir('app/Resources/translations') as $entry) {

            if (substr($entry, 0, 9) != 'messages.' || substr($entry, -4) != '.yml') {
                continue;
            }

            if (strpos($entry, '_') === false) {
                continue;
            }

            if (is_file("app/Resources/translations/{$entry}")) {

                $locale = substr($entry, 9, -4);

                if (in_array($locale, $locales)) {
                    continue;
                }

                $newname = str_replace($locale, substr($locale, 0, 2), $entry);

                rename("app/Resources/translations/{$entry}", "app/Resources/translations/{$newname}");
            }
        }

        // Update translation files.
        foreach (scandir('app/Resources/translations') as $entry) {

            if (substr($entry, 0, 9) != 'messages.' || substr($entry, -4) != '.yml') {
                continue;
            }

            if (is_file("app/Resources/translations/{$entry}")) {

                $output->writeln("Updating {$entry}");

                $locale = substr($entry, 9, -4);

                exec("php ./bin/console translation:update --force --no-backup --quiet --no-interaction {$locale}");

                $contents = file_get_contents("app/Resources/translations/{$entry}");

                if ($locale != 'en') {
                    $contents = "# This file is auto-generated using Crowdin.\n" . $contents;
                }

                file_put_contents("app/Resources/translations/{$entry}", $contents);
            }
        }
    }
}
