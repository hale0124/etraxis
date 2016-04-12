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

use AltrEgo\AltrEgo;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /** @var Group */
    private $object;

    protected function setUp()
    {
        $this->object = new Group();
    }

    public function testId()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected   = mt_rand(1, PHP_INT_MAX);
        $object->id = $expected;
        self::assertEquals($expected, $this->object->getId());
    }

    public function testProject()
    {
        $this->object->setProject($project = new Project());
        self::assertEquals($project, $this->object->getProject());

        $this->object->setProject();
        self::assertNull($this->object->getProject());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        self::assertEquals($expected, $this->object->getName());
    }

    public function testDescription()
    {
        $expected = 'Description';
        $this->object->setDescription($expected);
        self::assertEquals($expected, $this->object->getDescription());
    }

    public function testIsGlobal()
    {
        self::assertTrue($this->object->isGlobal());
    }

    public function testMembers()
    {
        self::assertCount(0, $this->object->getMembers());

        $this->object->addMember($user = new User());
        self::assertCount(1, $this->object->getMembers());

        $this->object->removeMember($user);
        self::assertCount(0, $this->object->getMembers());
    }

    public function testJsonSerialize()
    {
        $expected = [
            'id',
            'project',
            'name',
            'description',
        ];

        self::assertEquals($expected, array_keys($this->object->jsonSerialize()));
    }
}
