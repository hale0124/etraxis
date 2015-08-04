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

namespace eTraxis\CommandBus;

use eTraxis\Tests\BaseTestCase;
use Symfony\Component\Validator\Constraints as Assert;

class TestCommand
{
    /**
     * @Assert\NotBlank()
     */
    public $numerator;

    /**
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value="0")
     */
    public $denominator;
}

class TestCommandHandler
{
    public function handle(TestCommand $command)
    {
        return intval($command->numerator / $command->denominator);
    }
}

class CommandBusTest extends BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->client->getContainer()->set('test.command', new TestCommandHandler());

        /** @var \eTraxis\CommandBus\CommandBus $command_bus */
        $command_bus = $this->command_bus;
        $command_bus->addHandler('test.command', 'eTraxis\CommandBus\TestCommand');
    }

    public function testSuccess()
    {
        $command = new TestCommand();

        $command->numerator   = 12;
        $command->denominator = 3;

        $this->assertEquals(4, $this->command_bus->handle($command));
    }

    /**
     * @expectedException \eTraxis\CommandBus\ValidationException
     * @expectedExceptionMessage This value should not be equal to "0".
     */
    public function testFailure()
    {
        $command = new TestCommand();

        $command->numerator   = 12;
        $command->denominator = 0;

        $this->command_bus->handle($command);
    }
}
