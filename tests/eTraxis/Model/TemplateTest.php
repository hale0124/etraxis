<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Model;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /** @var Template */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Template();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testProjectId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setProjectId($expected);
        $this->assertEquals($expected, $this->object->getProjectId());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        $this->assertEquals($expected, $this->object->getName());
    }

    public function testPrefix()
    {
        $expected = 'Prefix';
        $this->object->setPrefix($expected);
        $this->assertEquals($expected, $this->object->getPrefix());
    }

    public function testCriticalAge()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setCriticalAge($expected);
        $this->assertEquals($expected, $this->object->getCriticalAge());
    }

    public function testFrozenTime()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setFrozenTime($expected);
        $this->assertEquals($expected, $this->object->getFrozenTime());
    }

    public function testDescription()
    {
        $expected = 'Description';
        $this->object->setDescription($expected);
        $this->assertEquals($expected, $this->object->getDescription());
    }

    public function testIsLocked()
    {
        $this->object->setLocked(false);
        $this->assertFalse($this->object->isLocked());

        $this->object->setLocked(true);
        $this->assertTrue($this->object->isLocked());
    }

    public function testHasGuestAccess()
    {
        $this->object->setGuestAccess(false);
        $this->assertFalse($this->object->hasGuestAccess());

        $this->object->setGuestAccess(true);
        $this->assertTrue($this->object->hasGuestAccess());
    }

    public function testRegisteredPermissions()
    {
        $expected = Template::PERMIT_CREATE_ISSUE | Template::PERMIT_ADD_COMMENT;
        $this->object->setRegisteredPermissions($expected);
        $this->assertEquals($expected, $this->object->getRegisteredPermissions());
    }

    public function testAuthorPermissions()
    {
        $expected = Template::PERMIT_CREATE_ISSUE | Template::PERMIT_ADD_COMMENT;
        $this->object->setAuthorPermissions($expected);
        $this->assertEquals($expected, $this->object->getAuthorPermissions());
    }

    public function testResponsiblePermissions()
    {
        $expected = Template::PERMIT_CREATE_ISSUE | Template::PERMIT_ADD_COMMENT;
        $this->object->setResponsiblePermissions($expected);
        $this->assertEquals($expected, $this->object->getResponsiblePermissions());
    }

    public function testProject()
    {
        $this->object->setProject($project = new Project());
        $this->assertSame($project, $this->object->getProject());
    }

    public function testStates()
    {
        $this->assertCount(0, $this->object->getStates());

        $this->object->addState($state = new State());
        $this->assertCount(1, $this->object->getStates());

        $this->object->removeState($state);
        $this->assertCount(0, $this->object->getStates());
    }

    public function testFields()
    {
        $this->assertCount(0, $this->object->getFields());

        $this->object->addField($field = new Field());
        $this->assertCount(1, $this->object->getFields());

        $this->object->removeField($field);
        $this->assertCount(0, $this->object->getFields());
    }
}
