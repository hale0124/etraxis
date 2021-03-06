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
use eTraxis\Entity\StringValue;
use eTraxis\Tests\TransactionalTestCase;

class CreateStringFieldCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateStringFieldCommand([
            'state'        => $state->getId(),
            'name'         => 'Client',
            'required'     => true,
            'maxLength'    => 100,
            'defaultValue' => 'N/A',
            'pcreCheck'    => '(\d{3})-(\d{3})-(\d{4})',
            'pcreSearch'   => '(\d{3})-(\d{3})-(\d{4})',
            'pcreReplace'  => '($1) $2-$3',
        ]);

        $this->commandbus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => $command->name]);

        /** @var StringValue $default */
        $default = $this->doctrine->getRepository(StringValue::class)->find($field->getParameters()->getDefaultValue());

        self::assertInstanceOf(Field::class, $field);
        self::assertEquals(FieldType::STRING, $field->getType());
        self::assertEquals(100, $field->getParameters()->getParameter1());
        self::assertEquals('N/A', $default->getValue());
        self::assertEquals('(\d{3})-(\d{3})-(\d{4})', $field->getPCRE()->getCheck());
        self::assertEquals('(\d{3})-(\d{3})-(\d{4})', $field->getPCRE()->getSearch());
        self::assertEquals('($1) $2-$3', $field->getPCRE()->getReplace());
    }
}
