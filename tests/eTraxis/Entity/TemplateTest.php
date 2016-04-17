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

use eTraxis\Tests\BaseTestCase;

class TemplateTest extends BaseTestCase
{
    /** @var Template */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getManager()->getRepository(Template::class)->findOneBy([
            'name' => 'Delivery',
        ]);
    }

    public function testId()
    {
        $template = new Template();
        self::assertNull($template->getId());
        self::assertNotNull($this->object->getId());
    }

    public function testProject()
    {
        $this->object->setProject($project = new Project());
        self::assertEquals($project, $this->object->getProject());
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
        self::assertCount(2, $this->object->getStates());
    }

    public function testJsonSerialize()
    {
        $expected = [
            'id'          => $this->object->getId(),
            'name'        => $this->object->getName(),
            'prefix'      => $this->object->getPrefix(),
            'criticalAge' => $this->object->getCriticalAge(),
            'frozenTime'  => $this->object->getFrozenTime(),
            'description' => $this->object->getDescription(),
            'isLocked'    => $this->object->isLocked(),
        ];

        self::assertEquals($expected, $this->object->jsonSerialize());
    }
}
