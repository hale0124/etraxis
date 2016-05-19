<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use eTraxis\Entity\StringValue;
use eTraxis\Tests\BaseTestCase;

class CreateStringFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $command = new CreateStringFieldCommand([
            'state'        => $state->getId(),
            'name'         => 'Client',
            'required'     => true,
            'showInEmails' => false,
            'maxLength'    => 100,
            'defaultValue' => 'N/A',
            'regexCheck'   => '(\d{3})-(\d{3})-(\d{4})',
            'regexSearch'  => '(\d{3})-(\d{3})-(\d{4})',
            'regexReplace' => '($1) $2-$3',
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => $command->name]);

        /** @var StringValue $default */
        $default = $this->doctrine->getRepository(StringValue::class)->find($field->getParameters()->getDefaultValue());

        self::assertInstanceOf(Field::class, $field);
        self::assertEquals(Field::TYPE_STRING, $field->getType());
        self::assertEquals(100, $field->getParameters()->getParameter1());
        self::assertEquals('N/A', $default->getValue());
        self::assertEquals('(\d{3})-(\d{3})-(\d{4})', $field->getRegex()->getCheck());
        self::assertEquals('(\d{3})-(\d{3})-(\d{4})', $field->getRegex()->getSearch());
        self::assertEquals('($1) $2-$3', $field->getRegex()->getReplace());
    }
}
