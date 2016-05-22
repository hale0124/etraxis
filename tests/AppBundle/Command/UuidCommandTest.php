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

use eTraxis\Tests\TransactionalTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class UuidCommandTest extends TransactionalTestCase
{
    public function testUuid()
    {
        $application = new Application(self::$kernel);
        $application->add(new UuidCommand());

        $commandTester = new CommandTester($application->find('etraxis:uuid'));
        $commandTester->execute([]);

        self::assertRegExp('/^([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}[\r\n]{1}$)/', $commandTester->getDisplay());
    }

    public function testUuidHexOnly()
    {
        $application = new Application(self::$kernel);
        $application->add(new UuidCommand());

        $commandTester = new CommandTester($application->find('etraxis:uuid'));
        $commandTester->execute(['--hex-only' => true]);

        self::assertRegExp('/^([0-9a-f]{32}[\r\n]{1}$)/', $commandTester->getDisplay());
    }
}
