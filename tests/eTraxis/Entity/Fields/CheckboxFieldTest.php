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

use eTraxis\Entity\Field;
use eTraxis\Tests\BaseTestCase;

class CheckboxFieldTest extends BaseTestCase
{
    /** @var Field */
    private $object = null;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Field();
        $this->object->setType(Field::TYPE_CHECKBOX);
    }

    public function testDefaultValue()
    {
        $field = $this->object->asCheckbox();

        $field->setDefaultValue(true);
        $this->assertTrue($field->getDefaultValue());
        $this->assertEquals(1, $this->object->getDefaultValue());

        $field->setDefaultValue(false);
        $this->assertFalse($field->getDefaultValue());
        $this->assertEquals(0, $this->object->getDefaultValue());
    }
}
