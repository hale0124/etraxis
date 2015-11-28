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

class UpdateIssueFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'Delivery']);

        $this->assertEquals(Field::TYPE_ISSUE, $field->getType());
        $this->assertEquals('Delivery', $field->getName());
        $this->assertNull($field->getDescription());
        $this->assertFalse($field->isRequired());
        $this->assertTrue($field->hasGuestAccess());
        $this->assertFalse($field->getShowInEmails());

        $command = new UpdateIssueFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Delivery #',
            'description'  => 'ID of the delivery task',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => true,
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository('eTraxis:Field')->find($field->getId());

        $this->assertEquals(Field::TYPE_ISSUE, $field->getType());
        $this->assertEquals('Delivery #', $field->getName());
        $this->assertEquals('ID of the delivery task', $field->getDescription());
        $this->assertTrue($field->isRequired());
        $this->assertFalse($field->hasGuestAccess());
        $this->assertTrue($field->getShowInEmails());
    }
}
