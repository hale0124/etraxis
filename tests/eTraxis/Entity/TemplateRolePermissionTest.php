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
    /** @var TemplateRolePermission */
    private $object;

    protected function setUp()
    {
        $this->object = new TemplateRolePermission();
    }

    public function testTemplate()
    {
        $this->object->setTemplate($template = new Template());
        self::assertEquals($template, $this->object->getTemplate());
    }

    public function testRole()
    {
        $expected = SystemRole::AUTHOR;
        $this->object->setRole($expected);
        self::assertEquals($expected, $this->object->getRole());
        $this->object->setRole('wtf');
        self::assertEquals($expected, $this->object->getRole());
    }

    public function testPermission()
    {
        $expected = TemplatePermission::CREATE_RECORDS;
        $this->object->setPermission($expected);
        self::assertEquals($expected, $this->object->getPermission());
        $this->object->setPermission('wtf');
        self::assertEquals($expected, $this->object->getPermission());
    }
}
