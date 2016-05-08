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

class FieldGroupPermissionTest extends \PHPUnit_Framework_TestCase
{
    /** @var FieldGroupPermission */
    private $object;

    protected function setUp()
    {
        $this->object = new FieldGroupPermission();
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

    public function testPermission()
    {
        $expected = Field::ACCESS_READ_ONLY;

        $this->object->setPermission($expected);
        self::assertEquals($expected, $this->object->getPermission());
    }
}
