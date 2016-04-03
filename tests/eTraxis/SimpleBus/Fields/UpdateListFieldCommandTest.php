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
use eTraxis\Tests\BaseTestCase;

class UpdateListFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        self::assertEquals(Field::TYPE_LIST, $field->getType());
        self::assertEquals('Season', $field->getName());
        self::assertNull($field->getDescription());
        self::assertTrue($field->isRequired());
        self::assertTrue($field->hasGuestAccess());
        self::assertFalse($field->getShowInEmails());
        self::assertNull($field->getParameters()->getDefaultValue());

        $command = new UpdateListFieldCommand([
            'id'           => $field->getId(),
            'name'         => 'Season #',
            'description'  => 'Season number',
            'required'     => false,
            'guestAccess'  => false,
            'showInEmails' => true,
            'defaultValue' => 7,
        ]);

        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->find($field->getId());

        self::assertEquals(Field::TYPE_LIST, $field->getType());
        self::assertEquals('Season #', $field->getName());
        self::assertEquals('Season number', $field->getDescription());
        self::assertFalse($field->isRequired());
        self::assertFalse($field->hasGuestAccess());
        self::assertTrue($field->getShowInEmails());
        self::assertEquals(7, $field->getParameters()->getDefaultValue());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown list item.
     */
    public function testItemNotFound()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        $command = new UpdateListFieldCommand([
            'id'           => $field->getId(),
            'name'         => $field->getName(),
            'required'     => $field->isRequired(),
            'guestAccess'  => $field->hasGuestAccess(),
            'showInEmails' => $field->getShowInEmails(),
            'defaultValue' => 8,
        ]);

        $this->command_bus->handle($command);
    }
}
