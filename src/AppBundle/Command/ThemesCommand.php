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

use eTraxis\Dictionary\Theme;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to download and update jQuery UI themes.
 */
class ThemesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('etraxis:themes')
            ->setDescription('Downloads and updates jQuery UI themes')
            ->addArgument('version', InputArgument::REQUIRED, 'jQuery UI version (e.g. "1.10.4")')
            ->addArgument('theme', InputArgument::OPTIONAL, 'Update specified theme only')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extensions = [
            'curl',
            'zip',
        ];

        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                $this->showError($output, "You need '{$extension}' PHP extension installed (see <http://php.net/{$extension}> for details).");

                return;
            }
        }

        $version = strtolower($input->getArgument('version'));
        $theme   = strtolower($input->getArgument('theme'));

        if ($theme) {
            if (Theme::has($theme)) {
                $this->update($output, $theme, $version);
                $output->writeln('Done.');
            }
            else {
                $this->showError($output, "Unknown theme '{$theme}'.");
            }
        }
        else {
            foreach (Theme::keys() as $theme) {
                $this->update($output, $theme, $version);
            }
            $output->writeln('Done.');
        }
    }

    /**
     * Updates specified theme.
     *
     * @param   OutputInterface $output
     * @param   string          $theme
     * @param   string          $version
     *
     * @return  bool
     */
    protected function update(OutputInterface $output, string $theme, string $version)
    {
        $output->writeln("Updating {$theme} to {$version}");

        $root_dir   = dirname($this->getContainer()->getParameter('kernel.root_dir'));
        $zip_file   = "{$root_dir}/var/{$theme}.zip";
        $theme_dir  = "{$root_dir}/var/jquery-ui-{$version}.custom";
        $assets_dir = "{$root_dir}/app/Resources/public/css/{$theme}";

        // Get ThemeRoller parameters.

        $content = file_get_contents("{$assets_dir}/jquery-ui.theme.css");

        if (!preg_match('/jqueryui\.com\/themeroller\/\?(.+)\n/miU', $content, $matches)) {
            $this->showError($output, 'Failed to get ThemeRoller parameters.');

            return false;
        }

        $roller = $matches[1];

        // Prepare POST request to ThemeRoller.

        $components = [
            // UI Core
            'Core',
            'Widget',
            'Mouse',
            'Position',
            // Interactions
            'Draggable',
            'Resizable',
            'Sortable',
            // Widgets
            'Button',
            'Datepicker',
            'Dialog',
            'Menu',
            'Progressbar',
            'Tabs',
            'Tooltip',
        ];

        $query = [
            'version=' . urlencode($version),
            'theme=' . urlencode($roller),
            'theme-folder-name=custom-theme',
            'scope=',
        ];

        foreach ($components as $component) {
            $query[] = strtolower("{$component}=on");
        }

        // Download theme's Zip file via POST request.

        $curl = curl_init();

        if (!$curl) {
            $this->showError($output, 'CURL initialization failure.');

            return false;
        }

        curl_setopt($curl, CURLOPT_URL, 'http://download.jqueryui.com/download');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, implode('&', $query));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);

        file_put_contents($zip_file, $result);

        curl_close($curl);

        // Unzip theme's file.

        $zip = new \ZipArchive();

        if ($zip->open($zip_file) !== true) {
            $this->showError($output, 'Failed to read downloaded zip file.');

            return false;
        }

        $zip->extractTo(dirname($theme_dir));
        $zip->close();

        // Update theme assets.

        array_map('unlink', glob("{$assets_dir}/images/ui-*"));

        array_map(function ($file) use ($assets_dir) {
            copy($file, sprintf('%s/images/%s', $assets_dir, basename($file)));
        }, glob("{$theme_dir}/images/*"));

        copy("{$theme_dir}/jquery-ui.theme.css", "{$assets_dir}/jquery-ui.theme.css");

        // Cleanup.

        $rmdir = function ($dir) use (&$rmdir) {

            foreach (scandir($dir) as $entry) {

                if ($entry === '.' || $entry === '..') {
                    continue;
                }

                $entry = $dir . '/' . $entry;

                if (is_dir($entry)) {
                    $rmdir($entry);
                }
                else {
                    unlink($entry);
                }
            }
            rmdir($dir);
        };

        $rmdir($theme_dir);

        return true;
    }

    /**
     * Outputs specified error message.
     *
     * @param   OutputInterface $output
     * @param   string          $message
     */
    protected function showError(OutputInterface $output, string $message)
    {
        $line = str_repeat(' ', mb_strlen($message));

        $output->writeln('');
        $output->writeln("<error>  {$line}  </error>");
        $output->writeln("<error>  {$message}  </error>");
        $output->writeln("<error>  {$line}  </error>");
        $output->writeln('');
    }
}
