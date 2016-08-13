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

class DeleteListItemCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        $item = new ListItem($field);

        $item
            ->setValue(8)
            ->setText('Season 8')
        ;

        $this->doctrine->getManager()->persist($item);
        $this->doctrine->getManager()->flush();

        $this->loginAs('hubert');

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'field' => $item->getField(),
            'value' => $item->getValue(),
        ]);
        self::assertNotNull($item);

        $command = new DeleteListItemCommand([
            'field' => $item->getField()->getId(),
            'value' => $item->getValue(),
        ]);
        $this->commandbus->handle($command);

        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'field' => $item->getField(),
            'value' => $item->getValue(),
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
            'value' => 1,
        ]);

        $command = new DeleteListItemCommand([
            'field' => $item->getField()->getId(),
            'value' => $item->getValue(),
        ]);
        $this->commandbus->handle($command);
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
            'value' => self::UNKNOWN_ENTITY_ID,
        ]);
        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown list item.
     */
    public function testNotFoundByValue()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Season']);

        $this->loginAs('hubert');

        $command = new DeleteListItemCommand([
            'field' => $field->getId(),
            'value' => self::UNKNOWN_ENTITY_ID,
        ]);
        $this->commandbus->handle($command);
    }
}
