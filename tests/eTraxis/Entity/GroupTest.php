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

use eTraxis\Tests\TransactionalTestCase;

class GroupTest extends TransactionalTestCase
{
    /** @var Group */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(Group::class)->findOneBy([
            'name' => 'Crew',
        ]);
    }

    public function testId()
    {
        $group = new Group();
        self::assertNull($group->getId());
        self::assertNotNull($this->object->getId());
    }

    public function testProjectOnNew()
    {
        $group = new Group();

        $group->setProject($project = new Project());
        self::assertEquals($project, $group->getProject());

        $group->setProject();
        self::assertNull($group->getProject());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Can't change project of existing group.
     */
    public function testProjectOnExisting()
    {
        $this->object->setProject();
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
        self::assertFalse($this->object->isGlobal());
    }

    public function testMembers()
    {
        $user = $this->findUser('artem');
        self::assertCount(5, $this->object->getMembers());

        $this->object->addMember($user);
        self::assertCount(6, $this->object->getMembers());

        $this->object->removeMember($user);
        self::assertCount(5, $this->object->getMembers());
    }

    public function testNonMembers()
    {
        $user = $this->findUser('artem');
        self::assertCount(18, $this->object->getNonMembers());

        $this->object->addMember($user);
        self::assertCount(17, $this->object->getNonMembers());

        $this->object->removeMember($user);
        self::assertCount(18, $this->object->getNonMembers());
    }

    public function testToString()
    {
        self::assertRegExp('/^group\#(\d+)$/', (string) $this->object);
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
