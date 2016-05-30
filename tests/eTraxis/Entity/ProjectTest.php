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

class ProjectTest extends TransactionalTestCase
{
    /** @var Project */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(Project::class)->findOneBy([
            'name' => 'Planet Express',
        ]);
    }

    public function testId()
    {
        $project = new Project();
        self::assertNull($project->getId());
        self::assertNotNull($this->object->getId());
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
        $expected = strtotime('1999-03-28');
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
        self::assertCount(3, $this->object->getGroups());
    }

    public function testTemplates()
    {
        self::assertCount(2, $this->object->getTemplates());
    }

    public function testToString()
    {
        self::assertEquals('Planet Express', (string) $this->object);
    }

    public function testJsonSerialize()
    {
        $expected = [
            'id'          => $this->object->getId(),
            'name'        => $this->object->getName(),
            'description' => $this->object->getDescription(),
            'createdAt'   => '1999-03-28',
            'isSuspended' => $this->object->isSuspended(),
        ];

        self::assertEquals($expected, $this->object->jsonSerialize());
    }
}
