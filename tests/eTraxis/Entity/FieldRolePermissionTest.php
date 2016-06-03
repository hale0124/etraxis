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

use eTraxis\Dictionary\FieldPermission;
use eTraxis\Dictionary\SystemRole;

class FieldRolePermissionTest extends \PHPUnit_Framework_TestCase
{
    /** @var FieldRolePermission */
    private $object;

    protected function setUp()
    {
        $this->object = new FieldRolePermission();
    }

    public function testField()
    {
        $this->object->setField($field = new Field());
        self::assertEquals($field, $this->object->getField());
    }

    public function testRole()
    {
        $expected = SystemRole::AUTHOR;

        $this->object->setRole($expected);
        self::assertEquals($expected, $this->object->getRole());
    }

    public function testPermission()
    {
        $expected = FieldPermission::READ_ONLY;

        $this->object->setPermission($expected);
        self::assertEquals($expected, $this->object->getPermission());
    }
}
