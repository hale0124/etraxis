<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Command;

use eTraxis\Tests\BaseTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CheckRequirementsCommandTest extends BaseTestCase
{
    public function testOK()
    {
        $expected = <<<OUT
Check PHP configuration

default_charset  OK (UTF-8)
date.timezone    OK (Pacific/Auckland)

Check PHP extensions

ctype      OK
iconv      OK
json       OK
mbstring   OK
mysqli     OK
pcre       OK
SimpleXML  OK


OUT;

        $application = new Application(self::$kernel);
        $application->add(new CheckRequirementsCommand());

        $commandTester = new CommandTester($application->find('etraxis:check:requirements'));
        $commandTester->execute([]);

        $this->assertEquals($expected, $commandTester->getDisplay());
    }

    public function testDefaultCharset()
    {
        $expected = 'default_charset  FAIL (should be either commented, or set to "UTF-8")';

        ini_set('default_charset', 'Windows-1251');

        $application = new Application(self::$kernel);
        $application->add(new CheckRequirementsCommand());

        $commandTester = new CommandTester($application->find('etraxis:check:requirements'));
        $commandTester->execute([]);

        $this->assertContains($expected, $commandTester->getDisplay());
    }
}
