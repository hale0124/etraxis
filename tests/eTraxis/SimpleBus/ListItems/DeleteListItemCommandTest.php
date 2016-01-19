<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\ListItems;

use eTraxis\Entity\ListItem;
use eTraxis\Tests\BaseTestCase;

class DeleteListItemCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'Season']);

        $item = new ListItem();

        $item
            ->setFieldId($field->getId())
            ->setKey(8)
            ->setValue('Season 8')
            ->setField($field)
        ;

        $this->doctrine->getManager()->persist($item);
        $this->doctrine->getManager()->flush();

        $this->loginAs('hubert');

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository('eTraxis:ListItem')->findOneBy([
            'fieldId' => $item->getFieldId(),
            'key'     => $item->getKey(),
        ]);
        $this->assertNotNull($item);

        $command = new DeleteListItemCommand([
            'field' => $item->getFieldId(),
            'key'   => $item->getKey(),
        ]);
        $this->command_bus->handle($command);

        $item = $this->doctrine->getRepository('eTraxis:ListItem')->findOneBy([
            'fieldId' => $item->getFieldId(),
            'key'     => $item->getKey(),
        ]);
        $this->assertNull($item);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'Season']);

        $this->loginAs('hubert');

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository('eTraxis:ListItem')->findOneBy([
            'fieldId' => $field->getId(),
            'key'     => 1,
        ]);
        $this->assertNotNull($item);

        $command = new DeleteListItemCommand([
            'field' => $item->getFieldId(),
            'key'   => $item->getKey(),
        ]);
        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown list item.
     */
    public function testNotFound()
    {
        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'Season']);

        $this->loginAs('hubert');

        $command = new DeleteListItemCommand([
            'field' => $field->getId(),
            'key'   => $this->getMaxId(),
        ]);
        $this->command_bus->handle($command);
    }
}
