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

class FieldGroupPermissionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $state      = new State(new Template(new Project()), StateType::IS_INTERIM);
        $field      = new Field($state, FieldType::STRING);
        $group      = new Group();
        $permission = FieldPermission::READ_ONLY;

        $object = new FieldGroupPermission($field, $group, $permission);

        self::assertEquals($field, $object->getField());
        self::assertEquals($group, $object->getGroup());
        self::assertEquals($permission, $object->getPermission());
    }
}
