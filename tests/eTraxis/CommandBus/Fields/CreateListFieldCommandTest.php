<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Fields;

use eTraxis\Dictionary\FieldType;
use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use eTraxis\Tests\TransactionalTestCase;

class CreateListFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateListFieldCommand([
            'state'    => $state->getId(),
            'name'     => 'Priority',
            'required' => true,
        ]);

        $this->commandbus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => $command->name]);

        self::assertInstanceOf(Field::class, $field);
        self::assertEquals(FieldType::LIST, $field->getType());
        self::assertNull($field->getParameters()->getParameter1());
        self::assertNull($field->getParameters()->getParameter2());
        self::assertNull($field->getParameters()->getDefaultValue());
    }
}
