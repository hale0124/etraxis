<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use eTraxis\Tests\BaseTestCase;

class CreateDurationFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $this->assertNotNull($state);

        $command = new CreateDurationFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Duration',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'minValue'     => '0:00',
            'maxValue'     => '168:00',
            'default'      => '48:00',
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => $command->name]);

        $this->assertInstanceOf('\eTraxis\Entity\Field', $field);
        $this->assertEquals(Field::TYPE_DURATION, $field->getType());
        $this->assertEquals(0, $field->getParameter1());
        $this->assertEquals(10080, $field->getParameter2());
        $this->assertEquals(2880, $field->getDefaultValue());
    }

    /**
     * @expectedException \eTraxis\SimpleBus\Middleware\ValidationException
     * @expectedExceptionMessage Maximum value should be greater then minimum one.
     */
    public function testMinMaxValues()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $this->assertNotNull($state);

        $command = new CreateDurationFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Duration',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'minValue'     => '168:00',
            'maxValue'     => '0:00',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\SimpleBus\Middleware\ValidationException
     * @expectedExceptionMessage Default value should be in range from 0:00 to 168:00.
     */
    public function testDefaultValue()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $this->assertNotNull($state);

        $command = new CreateDurationFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Duration',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'minValue'     => '0:00',
            'maxValue'     => '168:00',
            'default'      => '240:00',
        ]);

        $this->command_bus->handle($command);
    }
}
