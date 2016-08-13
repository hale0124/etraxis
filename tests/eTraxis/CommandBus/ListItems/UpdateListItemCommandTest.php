<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\ListItems;

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
            'value' => 1,
        ]);

        self::assertEquals(1, $item->getValue());
        self::assertEquals('Season 1', $item->getText());

        $command = new UpdateListItemCommand([
            'field' => $field->getId(),
            'value' => $item->getValue(),
            'text'  => 'Season 0',
        ]);

        $this->command_bus->handle($command);

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'field' => $field,
            'value' => 1,
        ]);

        self::assertEquals('Season 0', $item->getText());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testUnknownItemByField()
    {
        $command = new UpdateListItemCommand([
            'field' => self::UNKNOWN_ENTITY_ID,
            'value' => 1,
            'text'  => 'Season 0',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown list item.
     */
    public function testUnknownItemByValue()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        $command = new UpdateListItemCommand([
            'field' => $field->getId(),
            'value' => 8,
            'text'  => 'Season 8',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Item with entered text already exists.
     */
    public function testTextConflict()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        $command = new UpdateListItemCommand([
            'field' => $field->getId(),
            'value' => 1,
            'text'  => 'Season 2',
        ]);

        $this->command_bus->handle($command);
    }
}
