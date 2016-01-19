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

class UpdateListItemCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'Season']);

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository('eTraxis:ListItem')->findOneBy([
            'field' => $field->getId(),
            'key'   => 1,
        ]);

        $this->assertNotNull($item);
        $this->assertEquals(1, $item->getKey());
        $this->assertEquals('Season 1', $item->getValue());

        $command = new UpdateListItemCommand([
            'field' => $field->getId(),
            'key'   => $item->getKey(),
            'value' => 'Season 0',
        ]);

        $this->command_bus->handle($command);

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository('eTraxis:ListItem')->findOneBy([
            'field' => $field->getId(),
            'key'   => 1,
        ]);

        $this->assertEquals('Season 0', $item->getValue());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown list item.
     */
    public function testUnknownItemByField()
    {
        $command = new UpdateListItemCommand([
            'field' => $this->getMaxId(),
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
        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'Season']);

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
        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => 'Season']);

        $command = new UpdateListItemCommand([
            'field' => $field->getId(),
            'key'   => 1,
            'value' => 'Season 2',
        ]);

        $this->command_bus->handle($command);
    }
}
