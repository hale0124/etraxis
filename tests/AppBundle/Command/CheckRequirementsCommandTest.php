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

use eTraxis\Dictionary\DatabasePlatform;
use eTraxis\Tests\BaseTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CheckRequirementsCommandTest extends BaseTestCase
{
    public function testOK()
    {
        $extensions = [
            'bcmath     OK',
            'ctype      OK',
            'iconv      OK',
            'json       OK',
            'mbstring   OK',
            'pcre       OK',
            'SimpleXML  OK',
        ];

        if ($this->client->getContainer()->getParameter('ldap_host')) {
            $extensions[] = 'ldap       OK';
        }

        $platform = $this->doctrine->getConnection()->getDatabasePlatform()->getName();

        switch ($platform) {

            case DatabasePlatform::MYSQL:
                $extensions[] = 'mysqli     OK';
                $extensions[] = 'pdo_mysql  OK';
                break;

            case DatabasePlatform::POSTGRESQL:
                $extensions[] = 'pgsql      OK';
                $extensions[] = 'pdo_pgsql  OK';
                break;

            case DatabasePlatform::MSSQL:
                $extensions[] = 'sqlsrv     OK';
                $extensions[] = 'pdo_sqlsrv OK';
                break;

            case DatabasePlatform::ORACLE:
                $extensions[] = 'oci8       OK';
                break;
        }

        sort($extensions, SORT_STRING | SORT_FLAG_CASE);

        $extensions = implode(PHP_EOL, $extensions);

        $expected = <<<OUT
Check PHP configuration

default_charset  OK (UTF-8)
date.timezone    OK (Pacific/Auckland)

Check PHP extensions

{$extensions}


OUT;

        $application = new Application(self::$kernel);
        $application->add(new CheckRequirementsCommand());

        $commandTester = new CommandTester($application->find('etraxis:check:requirements'));
        $commandTester->execute([]);

        self::assertEquals($expected, $commandTester->getDisplay());
    }

    public function testDefaultCharset()
    {
        $expected = 'default_charset  FAIL (should be either commented, or set to "UTF-8")';

        ini_set('default_charset', 'Windows-1251');

        $application = new Application(self::$kernel);
        $application->add(new CheckRequirementsCommand());

        $commandTester = new CommandTester($application->find('etraxis:check:requirements'));
        $commandTester->execute([]);

        self::assertContains($expected, $commandTester->getDisplay());
    }
}
