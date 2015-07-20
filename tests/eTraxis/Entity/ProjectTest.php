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

namespace eTraxis\Entity;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    /** @var Project */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Project();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        $this->assertEquals($expected, $this->object->getName());
    }

    public function testDescription()
    {
        $expected = 'Description';
        $this->object->setDescription($expected);
        $this->assertEquals($expected, $this->object->getDescription());
    }

    public function testCreatedAt()
    {
        $expected = time();
        $this->object->setCreatedAt($expected);
        $this->assertEquals($expected, $this->object->getCreatedAt());
    }

    public function testIsSuspended()
    {
        $this->object->setSuspended(false);
        $this->assertFalse($this->object->isSuspended());

        $this->object->setSuspended(true);
        $this->assertTrue($this->object->isSuspended());
    }

    public function testGroups()
    {
        $this->assertCount(0, $this->object->getGroups());

        $this->object->addGroup($group = new Group());
        $this->assertCount(1, $this->object->getGroups());

        $this->object->removeGroup($group);
        $this->assertCount(0, $this->object->getGroups());
    }

    public function testTemplates()
    {
        $this->assertCount(0, $this->object->getTemplates());

        $this->object->addTemplate($template = new Template());
        $this->assertCount(1, $this->object->getTemplates());

        $this->object->removeTemplate($template);
        $this->assertCount(0, $this->object->getTemplates());
    }
}
