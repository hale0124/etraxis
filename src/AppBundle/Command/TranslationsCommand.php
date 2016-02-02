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
            ->setDescription('Updates all translation files')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach (scandir('app/Resources/translations') as $entry) {

            if (substr($entry, 0, 9) != 'messages.' || substr($entry, -4) != '.yml') {
                continue;
            }

            if (strpos($entry, 'en_') !== false) {
                continue;
            }

            if (is_file("app/Resources/translations/{$entry}")) {

                $output->writeln("Updating {$entry}");

                $locale = substr($entry, 9, -4);

                exec("php ./bin/console translation:update --force --no-backup --quiet --no-interaction {$locale}");

                $contents = file_get_contents("app/Resources/translations/{$entry}");
                $contents = str_replace(': null', ': ~', $contents);

                file_put_contents("app/Resources/translations/{$entry}", $contents);
            }
        }
    }
}
