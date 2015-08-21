<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
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

class UuidCommandTest extends BaseTestCase
{
    public function testUuid()
    {
        $application = new Application(self::$kernel);
        $application->add(new UuidCommand());

        $commandTester = new CommandTester($application->find('etraxis:uuid'));
        $commandTester->execute([]);

        $this->assertRegExp('/^([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}[\n]{1}$)/', $commandTester->getDisplay());
    }

    public function testUuidHexOnly()
    {
        $application = new Application(self::$kernel);
        $application->add(new UuidCommand());

        $commandTester = new CommandTester($application->find('etraxis:uuid'));
        $commandTester->execute(['--hex-only' => true]);

        $this->assertRegExp('/^([0-9a-f]{32}[\n]{1}$)/', $commandTester->getDisplay());
    }
}
