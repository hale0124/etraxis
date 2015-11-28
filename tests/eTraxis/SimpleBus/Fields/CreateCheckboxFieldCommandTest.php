<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use eTraxis\Entity\Field;
use eTraxis\Tests\BaseTestCase;

class CreateCheckboxFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);

        $this->assertNotNull($state);

        $command = new CreateCheckboxFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Required',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'default'      => true,
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => $command->name]);

        $this->assertInstanceOf('\eTraxis\Entity\Field', $field);
        $this->assertEquals(Field::TYPE_CHECKBOX, $field->getType());
        $this->assertNull($field->getParameter1());
        $this->assertNull($field->getParameter2());
        $this->assertTrue($field->getDefaultValue());
    }
}
