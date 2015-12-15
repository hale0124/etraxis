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

class UpdateListFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'Season']);

        $this->assertEquals(Field::TYPE_LIST, $field->getType());
        $this->assertEquals('Season', $field->getName());
        $this->assertNull($field->getDescription());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->hasGuestAccess());
        $this->assertFalse($field->getShowInEmails());
        $this->assertNull($field->getDefaultValue());

        $command = new UpdateListFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Season #',
            'description'  => 'Season number',
            'required'     => false,
            'guestAccess'  => false,
            'showInEmails' => true,
            'default'      => 7,
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository('eTraxis:Field')->find($field->getId());

        $this->assertEquals(Field::TYPE_LIST, $field->getType());
        $this->assertEquals('Season #', $field->getName());
        $this->assertEquals('Season number', $field->getDescription());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->hasGuestAccess());
        $this->assertTrue($field->getShowInEmails());
        $this->assertEquals(7, $field->getDefaultValue());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown list item.
     */
    public function testItemNotFound()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'Season']);

        $command = new UpdateListFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'guestAccess'  => $field->hasGuestAccess(),
            'showInEmails' => $field->getShowInEmails(),
            'default'      => 8,
        ]);

        $this->command_bus->handle($command);
    }
}
