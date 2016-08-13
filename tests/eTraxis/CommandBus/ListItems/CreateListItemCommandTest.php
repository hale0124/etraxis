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

class CreateListItemCommandTest extends TransactionalTestCase
{
    /**
     * @return  \eTraxis\Entity\Field
     */
    private function getField()
    {
        return $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);
    }

    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->getField();
        $value = 8;
        $text  = 'Season 8';

        $command = new CreateListItemCommand([
            'field' => $field->getId(),
            'value' => $value,
            'text'  => $text,
        ]);

        $this->commandbus->handle($command);

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'field' => $field,
            'value' => $value,
        ]);

        self::assertInstanceOf(ListItem::class, $item);
        self::assertEquals($field->getId(), $item->getField()->getId());
        self::assertEquals($value, $item->getValue());
        self::assertEquals($text, $item->getText());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testUnknownField()
    {
        $command = new CreateListItemCommand([
            'field' => self::UNKNOWN_ENTITY_ID,
            'value' => 8,
            'text'  => 'Season 8',
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Item with entered value already exists.
     */
    public function testKeyConflict()
    {
        $command = new CreateListItemCommand([
            'field' => $this->getField()->getId(),
            'value' => 1,
            'text'  => 'Season 8',
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Item with entered text already exists.
     */
    public function testValueConflict()
    {
        $command = new CreateListItemCommand([
            'field' => $this->getField()->getId(),
            'value' => 8,
            'text'  => 'Season 1',
        ]);

        $this->commandbus->handle($command);
    }
}
