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

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /** @var Template */
    private $object;

    protected function setUp()
    {
        $this->object = new Template();
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
        self::assertSame($project, $this->object->getProject());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        self::assertEquals($expected, $this->object->getName());
    }

    public function testPrefix()
    {
        $expected = 'Prefix';
        $this->object->setPrefix($expected);
        self::assertEquals($expected, $this->object->getPrefix());
    }

    public function testCriticalAge()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setCriticalAge($expected);
        self::assertEquals($expected, $this->object->getCriticalAge());
    }

    public function testFrozenTime()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setFrozenTime($expected);
        self::assertEquals($expected, $this->object->getFrozenTime());
    }

    public function testDescription()
    {
        $expected = 'Description';
        $this->object->setDescription($expected);
        self::assertEquals($expected, $this->object->getDescription());
    }

    public function testIsLocked()
    {
        $this->object->setLocked(false);
        self::assertFalse($this->object->isLocked());

        $this->object->setLocked(true);
        self::assertTrue($this->object->isLocked());
    }

    public function testHasGuestAccess()
    {
        $this->object->setGuestAccess(false);
        self::assertFalse($this->object->hasGuestAccess());

        $this->object->setGuestAccess(true);
        self::assertTrue($this->object->hasGuestAccess());
    }

    public function testRegisteredPermissions()
    {
        $expected = Template::PERMIT_CREATE_RECORD | Template::PERMIT_ADD_COMMENT;
        $this->object->setRegisteredPermissions($expected);
        self::assertEquals($expected, $this->object->getRegisteredPermissions());
    }

    public function testAuthorPermissions()
    {
        $expected = Template::PERMIT_CREATE_RECORD | Template::PERMIT_ADD_COMMENT;
        $this->object->setAuthorPermissions($expected);
        self::assertEquals($expected, $this->object->getAuthorPermissions());
    }

    public function testResponsiblePermissions()
    {
        $expected = Template::PERMIT_CREATE_RECORD | Template::PERMIT_ADD_COMMENT;
        $this->object->setResponsiblePermissions($expected);
        self::assertEquals($expected, $this->object->getResponsiblePermissions());
    }

    public function testStates()
    {
        self::assertCount(0, $this->object->getStates());

        $this->object->addState($state = new State());
        self::assertCount(1, $this->object->getStates());

        $this->object->removeState($state);
        self::assertCount(0, $this->object->getStates());
    }

    public function testFields()
    {
        self::assertCount(0, $this->object->getFields());

        $this->object->addField($field = new Field());
        self::assertCount(1, $this->object->getFields());

        $this->object->removeField($field);
        self::assertCount(0, $this->object->getFields());
    }

    public function testJsonSerialize()
    {
        $expected = [
            'id',
            'name',
            'prefix',
            'criticalAge',
            'frozenTime',
            'description',
            'isLocked',
        ];

        self::assertEquals($expected, array_keys($this->object->jsonSerialize()));
    }
}
