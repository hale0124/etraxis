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

class CreateNumberFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $this->assertNotNull($state);

        $command = new CreateNumberFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Month',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'minValue'     => 1,
            'maxValue'     => 12,
            'default'      => 2,
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => $command->name]);

        $this->assertInstanceOf(Field::class, $field);
        $this->assertEquals(Field::TYPE_NUMBER, $field->getType());
        $this->assertEquals(1, $field->getParameter1());
        $this->assertEquals(12, $field->getParameter2());
        $this->assertEquals(2, $field->getDefaultValue());
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

        $command = new CreateNumberFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Month',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'minValue'     => 12,
            'maxValue'     => 1,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\SimpleBus\Middleware\ValidationException
     * @expectedExceptionMessage Default value should be in range from 1 to 12.
     */
    public function testDefaultValue()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $this->assertNotNull($state);

        $command = new CreateNumberFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Month',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'minValue'     => 1,
            'maxValue'     => 12,
            'default'      => 13,
        ]);

        $this->command_bus->handle($command);
    }
}
