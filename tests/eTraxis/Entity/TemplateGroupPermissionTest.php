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
    /** @var TemplateGroupPermission */
    private $object;

    protected function setUp()
    {
        $this->object = new TemplateGroupPermission();
    }

    public function testTemplate()
    {
        $this->object->setTemplate($template = new Template());
        self::assertEquals($template, $this->object->getTemplate());
    }

    public function testGroup()
    {
        $this->object->setGroup($group = new Group());
        self::assertEquals($group, $this->object->getGroup());
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
