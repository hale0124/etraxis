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

use eTraxis\Dictionary\SystemRole;
use eTraxis\Dictionary\TemplatePermission;

class TemplateRolePermissionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $project    = new Project();
        $template   = new Template($project);
        $role       = SystemRole::AUTHOR;
        $permission = TemplatePermission::CREATE_RECORDS;

        $object = new TemplateRolePermission($template, $role, $permission);

        self::assertEquals($template, $object->getTemplate());
        self::assertEquals($role, $object->getRole());
        self::assertEquals($permission, $object->getPermission());
    }
}
