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
use eTraxis\Security\CurrentUser;
use eTraxis\Tests\TransactionalTestCase;

class TemplateTest extends TransactionalTestCase
{
    /** @var Template */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(Template::class)->findOneBy([
            'name' => 'Delivery',
        ]);
    }

    public function testConstruct()
    {
        $template = new Template($project = new Project());
        self::assertEquals($project, $template->getProject());
    }

    public function testId()
    {
        $template = new Template($this->object->getProject());
        self::assertNull($template->getId());
        self::assertNotNull($this->object->getId());
    }

    public function testProject()
    {
        $expected = 'Planet Express';
        self::assertEquals($expected, $this->object->getProject()->getName());
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
        $expected = random_int(1, PHP_INT_MAX);
        $this->object->setCriticalAge($expected);
        self::assertEquals($expected, $this->object->getCriticalAge());
    }

    public function testFrozenTime()
    {
        $expected = random_int(1, PHP_INT_MAX);
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

    public function testInitialState()
    {
        self::assertEquals('New', $this->object->getInitialState()->getName());
    }

    public function testStates()
    {
        self::assertCount(2, $this->object->getStates());
    }

    public function testAnyoneRolePermissions()
    {
        $permissions = [
            TemplatePermission::CREATE_RECORDS,
            TemplatePermission::ADD_COMMENTS,
            'wtf',
        ];

        $expected = [
            TemplatePermission::CREATE_RECORDS,
            TemplatePermission::ADD_COMMENTS,
        ];

        $this->object->setRolePermissions(SystemRole::ANYONE, $permissions);
        self::assertArraysByValues($expected, $this->object->getRolePermissions(SystemRole::ANYONE));
    }

    public function testAuthorRolePermissions()
    {
        $permissions = [
            TemplatePermission::CREATE_RECORDS,
            TemplatePermission::ADD_COMMENTS,
        ];

        $expected = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::ADD_COMMENTS,
        ];

        $this->object->setRolePermissions(SystemRole::AUTHOR, $permissions);
        self::assertArraysByValues($expected, $this->object->getRolePermissions(SystemRole::AUTHOR));
    }

    public function testResponsibleRolePermissions()
    {
        $permissions = [
            TemplatePermission::CREATE_RECORDS,
            TemplatePermission::ADD_COMMENTS,
        ];

        $expected = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::ADD_COMMENTS,
        ];

        $this->object->setRolePermissions(SystemRole::RESPONSIBLE, $permissions);
        self::assertArraysByValues($expected, $this->object->getRolePermissions(SystemRole::RESPONSIBLE));
    }

    public function testGroupPermissions()
    {
        $default = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::ADD_COMMENTS,
            TemplatePermission::PRIVATE_COMMENTS,
        ];

        $permissions = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::ATTACH_FILES,
            TemplatePermission::DELETE_FILES,
            'wtf',
        ];

        $expected = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::ATTACH_FILES,
            TemplatePermission::DELETE_FILES,
        ];

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);

        self::assertArraysByValues($default, $this->object->getGroupPermissions($group));

        $this->object->setGroupPermissions($group, $permissions);
        self::assertArraysByValues($expected, $this->object->getGroupPermissions($group));
    }

    public function testIsRoleGranted()
    {
        self::assertFalse($this->object->isRoleGranted(SystemRole::ANYONE, TemplatePermission::VIEW_RECORDS));

        self::assertTrue($this->object->isRoleGranted(SystemRole::AUTHOR, TemplatePermission::VIEW_RECORDS));
        self::assertFalse($this->object->isRoleGranted(SystemRole::AUTHOR, TemplatePermission::DELETE_RECORDS));

        self::assertTrue($this->object->isRoleGranted(SystemRole::RESPONSIBLE, TemplatePermission::VIEW_RECORDS));
        self::assertFalse($this->object->isRoleGranted(SystemRole::RESPONSIBLE, TemplatePermission::DELETE_RECORDS));
    }

    public function testIsGroupGranted()
    {
        $user = new CurrentUser($this->findUser('fry'));

        self::assertTrue($this->object->isUserGranted($user, TemplatePermission::ADD_COMMENTS));
        self::assertFalse($this->object->isUserGranted($user, TemplatePermission::REASSIGN_RECORDS));
    }

    public function testToString()
    {
        self::assertRegExp('/^template\#(\d+)$/', (string) $this->object);
    }

    public function testJsonSerialize()
    {
        $expected = [
            'id'          => $this->object->getId(),
            'project'     => $this->object->getProject()->getId(),
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
