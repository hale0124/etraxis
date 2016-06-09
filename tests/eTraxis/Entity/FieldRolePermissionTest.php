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
use eTraxis\Dictionary\FieldType;
use eTraxis\Dictionary\StateType;
use eTraxis\Dictionary\SystemRole;

class FieldRolePermissionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $state      = new State(new Template(new Project()), StateType::INTERIM);
        $field      = new Field($state, FieldType::STRING);
        $role       = SystemRole::AUTHOR;
        $permission = FieldPermission::READ_ONLY;

        $object = new FieldRolePermission($field, $role, $permission);

        self::assertEquals($field, $object->getField());
        self::assertEquals($role, $object->getRole());
        self::assertEquals($permission, $object->getPermission());
    }
}
