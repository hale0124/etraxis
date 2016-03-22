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
use eTraxis\Entity\TextValue;
use eTraxis\Tests\BaseTestCase;

class CreateTextFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        $this->assertNotNull($state);

        $command = new CreateTextFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Comment',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'maxLength'    => 1000,
            'defaultValue' => 'N/A',
            'regexCheck'   => '(\d{3})-(\d{3})-(\d{4})',
            'regexSearch'  => '(\d{3})-(\d{3})-(\d{4})',
            'regexReplace' => '($1) $2-$3',
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => $command->name]);

        /** @var TextValue $default */
        $default = $this->doctrine->getRepository(TextValue::class)->find($field->getDefaultValue());

        $this->assertInstanceOf(Field::class, $field);
        $this->assertEquals(Field::TYPE_TEXT, $field->getType());
        $this->assertEquals(1000, $field->getParameter1());
        $this->assertEquals('N/A', $default->getValue());
        $this->assertEquals('(\d{3})-(\d{3})-(\d{4})', $field->getRegexCheck());
        $this->assertEquals('(\d{3})-(\d{3})-(\d{4})', $field->getRegexSearch());
        $this->assertEquals('($1) $2-$3', $field->getRegexReplace());
    }
}
