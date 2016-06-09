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

use eTraxis\Dictionary\TemplatePermission;

class TemplateGroupPermissionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $project    = new Project();
        $template   = new Template($project);
        $group      = new Group();
        $permission = TemplatePermission::CREATE_RECORDS;

        $object = new TemplateGroupPermission($template, $group, $permission);

        self::assertEquals($template, $object->getTemplate());
        self::assertEquals($group, $object->getGroup());
        self::assertEquals($permission, $object->getPermission());
    }
}
