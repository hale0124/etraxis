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

use eTraxis\Entity\Field;
use eTraxis\Entity\StringValue;
use eTraxis\Tests\BaseTestCase;

class CreateStringFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);

        $this->assertNotNull($state);

        $command = new CreateStringFieldCommand([
            'template'     => $state->getTemplateId(),
            'state'        => $state->getId(),
            'name'         => 'Client',
            'required'     => true,
            'guestAccess'  => false,
            'showInEmails' => false,
            'maxLength'    => 100,
            'default'      => 'N/A',
            'regexCheck'   => '(\d{3})-(\d{3})-(\d{4})',
            'regexSearch'  => '(\d{3})-(\d{3})-(\d{4})',
            'regexReplace' => '($1) $2-$3',
        ]);

        $this->command_bus->handle($command);

        /** @var Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy(['name' => $command->name]);

        /** @var StringValue $default */
        $default = $this->doctrine->getRepository('eTraxis:StringValue')->find($field->getDefaultValue());

        $this->assertInstanceOf('\eTraxis\Entity\Field', $field);
        $this->assertEquals(Field::TYPE_STRING, $field->getType());
        $this->assertEquals(100, $field->getParameter1());
        $this->assertEquals('N/A', $default->getValue());
        $this->assertEquals('(\d{3})-(\d{3})-(\d{4})', $field->getRegexCheck());
        $this->assertEquals('(\d{3})-(\d{3})-(\d{4})', $field->getRegexSearch());
        $this->assertEquals('($1) $2-$3', $field->getRegexReplace());
    }
}