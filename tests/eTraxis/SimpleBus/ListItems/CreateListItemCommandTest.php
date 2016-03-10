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
use eTraxis\Tests\BaseTestCase;

class CreateListItemCommandTest extends BaseTestCase
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
        $key   = 8;
        $value = 'Season 8';

        $command = new CreateListItemCommand([
            'field' => $field->getId(),
            'key'   => $key,
            'value' => $value,
        ]);

        $this->command_bus->handle($command);

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'fieldId' => $field->getId(),
            'key'     => $key,
        ]);

        $this->assertInstanceOf(ListItem::class, $item);
        $this->assertEquals($field->getId(), $item->getField()->getId());
        $this->assertEquals($key, $item->getKey());
        $this->assertEquals($value, $item->getValue());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testUnknownField()
    {
        $command = new CreateListItemCommand([
            'field' => $this->getMaxId(),
            'key'   => 8,
            'value' => 'Season 8',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Item with entered key already exists.
     */
    public function testKeyConflict()
    {
        $command = new CreateListItemCommand([
            'field' => $this->getField()->getId(),
            'key'   => 1,
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
        $command = new CreateListItemCommand([
            'field' => $this->getField()->getId(),
            'key'   => 8,
            'value' => 'Season 1',
        ]);

        $this->command_bus->handle($command);
    }
}
