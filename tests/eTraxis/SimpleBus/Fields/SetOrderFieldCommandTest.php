<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use eTraxis\Tests\BaseTestCase;

class SetOrderFieldCommandTest extends BaseTestCase
{
    /**
     * @param   int $stateId
     *
     * @return  array
     */
    private function getFields($stateId)
    {
        /** @var \eTraxis\Entity\Field[] $fields */
        $fields = $this->doctrine->getRepository('eTraxis:Field')->findBy([
            'stateId'   => $stateId,
            'removedAt' => 0,
        ], ['indexNumber' => 'ASC']);

        $result = [];

        foreach ($fields as $field) {
            $result[] = $field->getName();
        }

        return $result;
    }

    public function testSuccessUp()
    {
        $expected = [
            'Crew',
            'Delivery at',
            'Delivery to',
            'Notes',
        ];

        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy([
            'name'      => 'Delivery at',
            'removedAt' => 0,
        ]);

        $this->assertNotNull($field);

        $command = new SetOrderFieldCommand([
            'id'    => $field->getId(),
            'order' => $field->getIndexNumber() - 1,
        ]);
        $this->command_bus->handle($command);

        $this->assertEquals($expected, $this->getFields($field->getStateId()));
    }

    public function testSuccessDown()
    {
        $expected = [
            'Crew',
            'Delivery at',
            'Delivery to',
            'Notes',
        ];

        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy([
            'name'      => 'Delivery to',
            'removedAt' => 0,
        ]);

        $this->assertNotNull($field);

        $command = new SetOrderFieldCommand([
            'id'    => $field->getId(),
            'order' => $field->getIndexNumber() + 1,
        ]);
        $this->command_bus->handle($command);

        $this->assertEquals($expected, $this->getFields($field->getStateId()));
    }

    public function testSuccessTop()
    {
        $expected = [
            'Delivery at',
            'Crew',
            'Delivery to',
            'Notes',
        ];

        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy([
            'name'      => 'Delivery at',
            'removedAt' => 0,
        ]);

        $this->assertNotNull($field);

        $command = new SetOrderFieldCommand([
            'id'    => $field->getId(),
            'order' => 1,
        ]);
        $this->command_bus->handle($command);

        $this->assertEquals($expected, $this->getFields($field->getStateId()));
    }

    public function testSuccessBottom()
    {
        $expected = [
            'Crew',
            'Delivery at',
            'Notes',
            'Delivery to',
        ];

        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy([
            'name'      => 'Delivery to',
            'removedAt' => 0,
        ]);

        $this->assertNotNull($field);

        $command = new SetOrderFieldCommand([
            'id'    => $field->getId(),
            'order' => $this->getMaxId(),
        ]);
        $this->command_bus->handle($command);

        $this->assertEquals($expected, $this->getFields($field->getStateId()));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown field.
     */
    public function testNotFound()
    {
        $command = new SetOrderFieldCommand([
            'id'    => $this->getMaxId(),
            'order' => 1,
        ]);

        $this->command_bus->handle($command);
    }
}
