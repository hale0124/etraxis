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

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /** @var Group */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Group();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testProjectId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setProjectId($expected);
        $this->assertEquals($expected, $this->object->getProjectId());
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

    public function testProject()
    {
        $this->object->setProject($project = new Project());
        $this->assertSame($project, $this->object->getProject());

        $this->object->setProject();
        $this->assertNull($this->object->getProject());
    }

    public function testUsers()
    {
        $this->assertCount(0, $this->object->getUsers());

        $this->object->addUser($user = new User());
        $this->assertCount(1, $this->object->getUsers());

        $this->object->removeUser($user);
        $this->assertCount(0, $this->object->getUsers());
    }

    public function testIsGlobal()
    {
        $this->assertTrue($this->object->isGlobal());
    }
}
