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

class DeleteFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy([
            'name'      => 'Crew',
            'removedAt' => 0,
        ]);

        self::assertNotNull($field);

        $command = new DeleteFieldCommand(['id' => $field->getId()]);
        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository(Field::class)->findOneBy([
            'name'      => 'Crew',
            'removedAt' => 0,
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

        self::assertEquals(1, $field1->getIndexNumber());
        self::assertEquals(2, $field2->getIndexNumber());
        self::assertEquals(3, $field3->getIndexNumber());
        self::assertEquals(4, $field4->getIndexNumber());

        $command = new DeleteFieldCommand(['id' => $field2->getId()]);
        $this->command_bus->handle($command);

        $field1 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);
        $field2 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Delivery to', 'state' => $field1->getState()]);
        $field3 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Delivery at', 'state' => $field1->getState()]);
        $field4 = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Notes',       'state' => $field1->getState()]);

        self::assertEquals(1, $field1->getIndexNumber());
        self::assertNull($field2);
        self::assertEquals(2, $field3->getIndexNumber());
        self::assertEquals(3, $field4->getIndexNumber());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testNotFound()
    {
        $command = new DeleteFieldCommand(['id' => PHP_INT_MAX]);
        $this->command_bus->handle($command);
    }
}
