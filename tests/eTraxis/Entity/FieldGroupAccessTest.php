<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class FieldGroupAccessTest extends \PHPUnit_Framework_TestCase
{
    /** @var FieldGroupAccess */
    private $object = null;

    protected function setUp()
    {
        $this->object = new FieldGroupAccess();
    }

    public function testFieldId()
    {
        $this->assertNull($this->object->getFieldId());
    }

    public function testGroupId()
    {
        $this->assertNull($this->object->getGroupId());
    }

    public function testAccess()
    {
        $expected = Field::ACCESS_READ_ONLY;

        $this->object->setAccess($expected);
        $this->assertEquals($expected, $this->object->getAccess());
    }

    public function testField()
    {
        $this->object->setField($field = new Field());
        $this->assertSame($field, $this->object->getField());
    }

    public function testGroup()
    {
        $this->object->setGroup($group = new Group());
        $this->assertSame($group, $this->object->getGroup());
    }
}
