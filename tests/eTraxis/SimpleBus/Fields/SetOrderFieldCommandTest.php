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
use eTraxis\Tests\TransactionalTestCase;

class SetOrderFieldCommandTest extends TransactionalTestCase
{
    /**
     * @param   State $state
     *
     * @return  array
     */
    private function getFields(State $state)
    {
        /** @var Field[] $fields */
        $fields = $this->doctrine->getRepository(Field::class)->findBy([
            'state'     => $state,
            'removedAt' => 0,
        ], ['indexNumber' => 'ASC']);

        $result = [];

        foreach ($fields as $field) {
            $result[] = $field->getName();
        }

        return $result;
    }

    public function testSuccessUp()
    {
        $expected = [
            'Crew',
            'Delivery at',
            'Delivery to',
            'Notes',
        ];

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy([
            'name'      => 'Delivery at',
            'removedAt' => 0,
        ]);

        $command = new SetOrderFieldCommand([
            'id'    => $field->getId(),
            'order' => $field->getIndexNumber() - 1,
        ]);

        $this->command_bus->handle($command);

        self::assertEquals($expected, $this->getFields($field->getState()));
    }

    public function testSuccessDown()
    {
        $expected = [
            'Crew',
            'Delivery at',
            'Delivery to',
            'Notes',
        ];

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy([
            'name'      => 'Delivery to',
            'removedAt' => 0,
        ]);

        $command = new SetOrderFieldCommand([
            'id'    => $field->getId(),
            'order' => $field->getIndexNumber() + 1,
        ]);

        $this->command_bus->handle($command);

        self::assertEquals($expected, $this->getFields($field->getState()));
    }

    public function testSuccessTop()
    {
        $expected = [
            'Delivery at',
            'Crew',
            'Delivery to',
            'Notes',
        ];

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy([
            'name'      => 'Delivery at',
            'removedAt' => 0,
        ]);

        $command = new SetOrderFieldCommand([
            'id'    => $field->getId(),
            'order' => 1,
        ]);

        $this->command_bus->handle($command);

        self::assertEquals($expected, $this->getFields($field->getState()));
    }

    public function testSuccessBottom()
    {
        $expected = [
            'Crew',
            'Delivery at',
            'Notes',
            'Delivery to',
        ];

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy([
            'name'      => 'Delivery to',
            'removedAt' => 0,
        ]);

        $command = new SetOrderFieldCommand([
            'id'    => $field->getId(),
            'order' => PHP_INT_MAX,
        ]);

        $this->command_bus->handle($command);

        self::assertEquals($expected, $this->getFields($field->getState()));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testNotFound()
    {
        $command = new SetOrderFieldCommand([
            'id'    => self::UNKNOWN_ENTITY_ID,
            'order' => 1,
        ]);

        $this->command_bus->handle($command);
    }
}
