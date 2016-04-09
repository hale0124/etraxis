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

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    /** @var Project */
    private $object;

    protected function setUp()
    {
        $this->object = new Project();
    }

    public function testId()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected   = mt_rand(1, PHP_INT_MAX);
        $object->id = $expected;
        self::assertEquals($expected, $this->object->getId());
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

    public function testCreatedAt()
    {
        $expected = time();
        $this->object->setCreatedAt($expected);
        self::assertEquals($expected, $this->object->getCreatedAt());
    }

    public function testIsSuspended()
    {
        $this->object->setSuspended(false);
        self::assertFalse($this->object->isSuspended());

        $this->object->setSuspended(true);
        self::assertTrue($this->object->isSuspended());
    }

    public function testGroups()
    {
        self::assertCount(0, $this->object->getGroups());

        $this->object->addGroup($group = new Group());
        self::assertCount(1, $this->object->getGroups());

        $this->object->removeGroup($group);
        self::assertCount(0, $this->object->getGroups());
    }

    public function testTemplates()
    {
        self::assertCount(0, $this->object->getTemplates());

        $this->object->addTemplate($template = new Template());
        self::assertCount(1, $this->object->getTemplates());

        $this->object->removeTemplate($template);
        self::assertCount(0, $this->object->getTemplates());
    }

    public function testJsonSerialize()
    {
        $expected = [
            'id',
            'name',
            'description',
            'isSuspended',
        ];

        self::assertEquals($expected, array_keys($this->object->jsonSerialize()));
    }
}
