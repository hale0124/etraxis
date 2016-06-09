<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Fields;

use eTraxis\Dictionary\FieldType;
use eTraxis\Dictionary\StateType;
use eTraxis\Entity\Field;
use eTraxis\Entity\Project;
use eTraxis\Entity\State;
use eTraxis\Entity\Template;

class AbstractFieldTest extends \PHPUnit_Framework_TestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $state = new State(new Template(new Project()), StateType::INTERIM);

        $this->object = new Field($state, FieldType::NUMBER);
        $this->object->asNumber()
            ->setMinValue(NumberField::MIN_VALUE)
            ->setMaxValue(NumberField::MAX_VALUE)
        ;
    }

    public function testOffsetExists()
    {
        $field = $this->object->asNumber();

        self::assertTrue($field->offsetExists('defaultValue'));
        self::assertFalse($field->offsetExists('unknownValue'));
    }

    public function testOffsetGet()
    {
        $field = $this->object->asNumber();

        self::assertEquals(NumberField::MIN_VALUE, $field->offsetGet('minValue'));
        self::assertEquals(NumberField::MAX_VALUE, $field->offsetGet('maxValue'));
    }

    public function testOffsetSet()
    {
        $field = $this->object->asNumber();

        $expected = random_int(NumberField::MIN_VALUE, NumberField::MAX_VALUE);

        self::assertNull($field->getDefaultValue());

        $field->offsetSet('defaultValue', $expected);
        self::assertEquals($expected, $field->getDefaultValue());
    }

    public function testOffsetUnset()
    {
        $field = $this->object->asNumber();

        $field->setDefaultValue(random_int(NumberField::MIN_VALUE, NumberField::MAX_VALUE));
        self::assertNotNull($field->getDefaultValue());

        $field->offsetUnset('defaultValue');
        self::assertNull($field->getDefaultValue());
    }
}
