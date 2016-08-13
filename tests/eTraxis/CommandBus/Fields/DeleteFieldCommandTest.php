<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Fields;

use eTraxis\Entity\Field;
use eTraxis\Tests\TransactionalTestCase;

class DeleteFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy([
            'name'      => 'Crew',
            'removedAt' => null,
        ]);

        self::assertNotNull($field);

        $command = new DeleteFieldCommand(['id' => $field->getId()]);
        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->findOneBy([
            'name'      => 'Crew',
            'removedAt' => null,
        ]);

        self::assertNull($field);
    }

    public function testReorder()
    {
        /** @var Field $field1 */
        /** @var Field $field2 */
        /** @var Field $field3 */
        /** @var Field $field4 */
        $field1 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);
        $field2 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Delivery to', 'state' => $field1->getState()]);
        $field3 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Delivery at', 'state' => $field1->getState()]);
        $field4 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Notes',       'state' => $field1->getState()]);

        self::assertEquals(1, $field1->getOrder());
        self::assertEquals(2, $field2->getOrder());
        self::assertEquals(3, $field3->getOrder());
        self::assertEquals(4, $field4->getOrder());

        self::assertFalse($field2->isRemoved());

        $command = new DeleteFieldCommand(['id' => $field2->getId()]);
        $this->command_bus->handle($command);

        $field1 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);
        $field2 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Delivery to', 'state' => $field1->getState()]);
        $field3 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Delivery at', 'state' => $field1->getState()]);
        $field4 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Notes',       'state' => $field1->getState()]);

        self::assertEquals(1, $field1->getOrder());
        self::assertEquals(0, $field2->getOrder());
        self::assertEquals(2, $field3->getOrder());
        self::assertEquals(3, $field4->getOrder());

        self::assertTrue($field2->isRemoved());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testNotFound()
    {
        $command = new DeleteFieldCommand(['id' => self::UNKNOWN_ENTITY_ID]);
        $this->command_bus->handle($command);
    }
}
