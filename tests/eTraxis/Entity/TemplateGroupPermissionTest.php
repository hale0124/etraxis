<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class TemplateGroupPermissionTest extends \PHPUnit_Framework_TestCase
{
    /** @var TemplateGroupPermission */
    private $object = null;

    protected function setUp()
    {
        $this->object = new TemplateGroupPermission();
    }

    public function testGroupId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setGroupId($expected);
        $this->assertEquals($expected, $this->object->getGroupId());
    }

    public function testTemplateId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setTemplateId($expected);
        $this->assertEquals($expected, $this->object->getTemplateId());
    }

    public function testPermission()
    {
        $expected = Template::PERMIT_CREATE_ISSUE;
        $this->object->setPermission($expected);
        $this->assertEquals($expected, $this->object->getPermission());
    }

    public function testGroup()
    {
        $this->object->setGroup($group = new Group());
        $this->assertSame($group, $this->object->getGroup());
    }

    public function testTemplate()
    {
        $this->object->setTemplate($template = new Template());
        $this->assertSame($template, $this->object->getTemplate());
    }
}
