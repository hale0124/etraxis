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

class DeleteListItemCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        $item = new ListItem();

        $item
            ->setField($field)
            ->setKey(8)
            ->setValue('Season 8')
        ;

        $this->doctrine->getManager()->persist($item);
        $this->doctrine->getManager()->flush();

        $this->loginAs('hubert');

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'field' => $item->getField(),
            'key'   => $item->getKey(),
        ]);
        self::assertNotNull($item);

        $command = new DeleteListItemCommand([
            'field' => $item->getField()->getId(),
            'key'   => $item->getKey(),
        ]);
        $this->command_bus->handle($command);

        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'field' => $item->getField(),
            'key'   => $item->getKey(),
        ]);
        self::assertNull($item);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        $this->loginAs('hubert');

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'field' => $field,
            'key'   => 1,
        ]);

        $command = new DeleteListItemCommand([
            'field' => $item->getField()->getId(),
            'key'   => $item->getKey(),
        ]);
        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testNotFoundByField()
    {
        $this->loginAs('hubert');

        $command = new DeleteListItemCommand([
            'field' => self::UNKNOWN_ENTITY_ID,
            'key'   => self::UNKNOWN_ENTITY_ID,
        ]);
        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown list item.
     */
    public function testNotFoundByKey()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        $this->loginAs('hubert');

        $command = new DeleteListItemCommand([
            'field' => $field->getId(),
            'key'   => self::UNKNOWN_ENTITY_ID,
        ]);
        $this->command_bus->handle($command);
    }
}
