<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2009-2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to check system requirements.
 */
class CheckRequirementsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('etraxis:check:requirements')
            ->setDescription('Check system requirements')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkConfiguration($output);
        $this->checkExtensions($output);
    }

    /**
     * Outputs specified report.
     *
     * @param   OutputInterface $output
     * @param   array           $lines
     */
    protected function outputReport(OutputInterface $output, $lines)
    {
        $max = max(array_map(function ($s) {
            return strlen($s);
        }, array_keys($lines)));

        foreach ($lines as $option => $result) {
            $option = str_pad($option, $max);
            $output->writeln("<info>{$option}</info>  {$result}");
        }

        $output->writeln('');
    }

    /**
     * Checks PHP configuration.
     *
     * @param   OutputInterface $output
     */
    protected function checkConfiguration(OutputInterface $output)
    {
        $output->writeln('<comment>Check PHP configuration</comment>');
        $output->writeln('');

        $report = [];

        // default_charset
        $default_charset = ini_get('default_charset');

        if (strlen($default_charset) == 0 || strtolower($default_charset) == 'utf-8') {
            $report['default_charset'] = 'OK (' . (strlen($default_charset) == 0 ? 'empty' : $default_charset) . ')';
        }
        else {
            $report['default_charset'] = '<error>FAIL</error> (should be either commented, or set to "UTF-8")';
        }

        // date.timezone
        $timezone = ini_get('date.timezone');

        if (strlen($timezone) == 0) {
            $report['date.timezone'] = '<error>FAIL</error> (undefined, should be set to one of supported timezones enumerated at <http://www.php.net/manual/timezones.php>)';
        }
        else {
            $report['date.timezone'] = 'OK (' . $timezone . ')';
        }

        $this->outputReport($output, $report);
    }

    /**
     * Checks for available PHP extensions.
     *
     * @param   OutputInterface $output
     */
    protected function checkExtensions(OutputInterface $output)
    {
        $extensions = [
            'ctype',        // also required by Symfony
            'json',         // also required by Symfony
            'SimpleXML',    // also required by Symfony
            'pcre',         // also required by Symfony
            'iconv',
            'mbstring',
        ];

        $database_driver = $this->getContainer()->getParameter('database_driver');

        switch ($database_driver) {

            // MySQL
            case 'pdo_mysql':
                $extensions[] = 'mysqli';
                $extensions[] = 'pdo_mysql';
                break;

            // PostgreSQL
            case 'pdo_pgsql':
                $extensions[] = 'pgsql';
                $extensions[] = 'pdo_pgsql';
                break;

            // Microsoft SQL Server
            case 'pdo_sqlsrv':
                $extensions[] = 'sqlsrv';
                $extensions[] = 'pdo_sqlsrv';
                break;

            // Oracle
            case 'oci8':
                $extensions[] = 'oci8';
                break;

            default:
                $output->writeln(sprintf('<error>ERROR: unsupported database driver (%s).</error>', $database_driver));
                $output->writeln('');
        }

        if ($this->getContainer()->getParameter('ldap_host')) {
            $extensions[] = 'ldap';
        }

        sort($extensions, SORT_STRING | SORT_FLAG_CASE);

        $output->writeln('<comment>Check PHP extensions</comment>');
        $output->writeln('');

        $report = [];

        foreach ($extensions as $extension) {
            $report[$extension] = extension_loaded($extension) ? 'OK' : '<error>FAIL</error>';
        }

        $this->outputReport($output, $report);
    }
}
