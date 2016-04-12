<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class FieldGroupAccessTest extends \PHPUnit_Framework_TestCase
{
    /** @var FieldGroupAccess */
    private $object;

    protected function setUp()
    {
        $this->object = new FieldGroupAccess();
    }

    public function testField()
    {
        $this->object->setField($field = new Field());
        self::assertEquals($field, $this->object->getField());
    }

    public function testGroup()
    {
        $this->object->setGroup($group = new Group());
        self::assertEquals($group, $this->object->getGroup());
    }

    public function testAccess()
    {
        $expected = Field::ACCESS_READ_ONLY;

        $this->object->setAccess($expected);
        self::assertEquals($expected, $this->object->getAccess());
    }
}
