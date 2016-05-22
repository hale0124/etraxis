<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\ListItems;

use eTraxis\Entity\Field;
use eTraxis\Entity\ListItem;
use eTraxis\Tests\TransactionalTestCase;

class UpdateListItemCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'field' => $field,
            'key'   => 1,
        ]);

        self::assertEquals(1, $item->getKey());
        self::assertEquals('Season 1', $item->getValue());

        $command = new UpdateListItemCommand([
            'field' => $field->getId(),
            'key'   => $item->getKey(),
            'value' => 'Season 0',
        ]);

        $this->command_bus->handle($command);

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'field' => $field,
            'key'   => 1,
        ]);

        self::assertEquals('Season 0', $item->getValue());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testUnknownItemByField()
    {
        $command = new UpdateListItemCommand([
            'field' => self::UNKNOWN_ENTITY_ID,
            'key'   => 1,
            'value' => 'Season 0',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown list item.
     */
    public function testUnknownItemByKey()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        $command = new UpdateListItemCommand([
            'field' => $field->getId(),
            'key'   => 8,
            'value' => 'Season 8',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Item with entered value already exists.
     */
    public function testValueConflict()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        $command = new UpdateListItemCommand([
            'field' => $field->getId(),
            'key'   => 1,
            'value' => 'Season 2',
        ]);

        $this->command_bus->handle($command);
    }
}
