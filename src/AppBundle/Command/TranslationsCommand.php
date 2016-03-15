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
        $this->rename('messages');
        $this->rename('validators');

        $this->update($output);
    }

    /**
     * Renames specified translation files from ISO 15897 to ISO 639-1.
     *
     * @param   string $basename Base filename of translation files.
     */
    protected function rename($basename)
    {
        $locales = [
            'en_AU',
            'en_CA',
            'en_GB',
            'en_NZ',
            'en_US',
            'pt_BR',
        ];

        $basename .= '.';

        foreach (scandir('app/Resources/translations') as $entry) {

            if (!preg_match("/^{$basename}(.)+\\.yml$/", $entry)) {
                continue;
            }

            if (strpos($entry, '_') === false) {
                continue;
            }

            if (is_file("app/Resources/translations/{$entry}")) {

                $locale = substr($entry, strlen($basename), -4);

                if (in_array($locale, $locales)) {
                    continue;
                }

                $newname = str_replace($locale, substr($locale, 0, 2), $entry);

                rename("app/Resources/translations/{$entry}", "app/Resources/translations/{$newname}");
            }
        }
    }

    /**
     * Updates translation files.
     *
     * @param   OutputInterface $output
     */
    protected function update(OutputInterface $output)
    {
        foreach (scandir('app/Resources/translations') as $entry) {

            if (!preg_match('/^messages\.(.)+\.yml$/', $entry)) {
                continue;
            }

            if (is_file("app/Resources/translations/{$entry}")) {

                $locale = substr($entry, 9, -4);

                $output->writeln("Updating {$locale}");

                exec("php ./bin/console translation:update --force --no-backup --quiet --no-interaction {$locale}");

                if ($locale != 'en') {
                    $this->comment("messages.{$locale}.yml");
                    $this->comment("validators.{$locale}.yml");
                }
            }
        }
    }

    /**
     * Appends specified translation file with comment that it was auto-generated.
     *
     * @param   string $filename
     */
    protected function comment($filename)
    {
        if (is_file("app/Resources/translations/{$filename}")) {

            $contents = file_get_contents("app/Resources/translations/{$filename}");
            $contents = "# This file is auto-generated using Crowdin.\n" . $contents;

            file_put_contents("app/Resources/translations/{$filename}", $contents);
        }
    }
}
