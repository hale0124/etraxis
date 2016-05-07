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
use eTraxis\Tests\BaseTestCase;

class StateTest extends BaseTestCase
{
    /** @var State */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getManager()->getRepository(State::class)->findOneBy([
            'name' => 'New',
        ]);
    }

    public function testId()
    {
        $state = new State();
        self::assertNull($state->getId());
        self::assertNotNull($this->object->getId());
    }

    public function testTemplate()
    {
        $this->object->setTemplate($template = new Template());
        self::assertEquals($template, $this->object->getTemplate());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        self::assertEquals($expected, $this->object->getName());
    }

    public function testAbbreviation()
    {
        $expected = 'Abbreviation';
        $this->object->setAbbreviation($expected);
        self::assertEquals($expected, $this->object->getAbbreviation());
    }

    public function testType()
    {
        $expected = State::TYPE_INTERIM;
        $this->object->setType($expected);
        self::assertEquals($expected, $this->object->getType());
        self::assertEquals(State::RESPONSIBLE_ASSIGN, $this->object->getResponsible());

        $expected = State::TYPE_FINAL;
        $this->object->setType($expected);
        self::assertEquals($expected, $this->object->getType());
        self::assertEquals(State::RESPONSIBLE_REMOVE, $this->object->getResponsible());
    }

    public function testResponsible()
    {
        $expected = State::RESPONSIBLE_KEEP;
        self::assertNotEquals($expected, $this->object->getResponsible());
        $this->object->setResponsible($expected);
        self::assertEquals($expected, $this->object->getResponsible());
    }

    public function testResponsibleOnFinal()
    {
        $expected = State::RESPONSIBLE_KEEP;
        self::assertNotEquals($expected, $this->object->getResponsible());

        $this->object->setType(State::TYPE_FINAL);
        $this->object->setResponsible($expected);
        self::assertNotEquals($expected, $this->object->getResponsible());
    }

    public function testNextState()
    {
        $state = new State();
        $this->object->setNextState($state);
        self::assertNotEquals($state, $this->object->getNextState());

        $state->setTemplate($this->object->getTemplate());
        $this->object->setNextState($state);
        self::assertEquals($state, $this->object->getNextState());

        $this->object->setType(State::TYPE_FINAL);
        self::assertNull($this->object->getNextState());
        $this->object->setNextState($state);
        self::assertNull($this->object->getNextState());
    }

    public function testFields()
    {
        self::assertCount(4, $this->object->getFields());
    }

    public function testGetRoleTransitions()
    {
        $repository = $this->doctrine->getManager()->getRepository(State::class);

        /** @var State $new */
        $new = $repository->findOneBy(['name' => 'New']);
        self::assertNotNull($new);

        /** @var State $delivered */
        $delivered = $repository->findOneBy(['name' => 'Delivered']);
        self::assertNotNull($delivered);

        $expected = [
            $delivered,
        ];

        self::assertEquals($expected, $new->getRoleTransitions(SystemRole::RESPONSIBLE));

        $new->setType(State::TYPE_FINAL);
        self::assertEmpty($new->getRoleTransitions(SystemRole::RESPONSIBLE));
    }

    public function testGetGroupTransitions()
    {
        $repository = $this->doctrine->getManager()->getRepository(State::class);

        /** @var Group $managers */
        $managers = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);
        self::assertNotNull($managers);

        /** @var State $new */
        $new = $repository->findOneBy(['name' => 'New']);
        self::assertNotNull($new);

        /** @var State $delivered */
        $delivered = $repository->findOneBy(['name' => 'Delivered']);
        self::assertNotNull($delivered);

        $expected = [
            $delivered,
        ];

        self::assertEquals($expected, $new->getGroupTransitions($managers));

        $new->setType(State::TYPE_FINAL);
        self::assertEmpty($new->getGroupTransitions($managers));
    }

    public function testGetResponsibleGroups()
    {
        /** @var Group $crew */
        $crew = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);
        self::assertNotNull($crew);

        $expected = [
            $crew,
        ];

        self::assertEquals($expected, $this->object->getResponsibleGroups());

        $this->object->setType(State::TYPE_FINAL);
        self::assertEmpty($this->object->getResponsibleGroups());
    }

    public function testGetNotResponsibleGroups()
    {
        $expected = array_filter($this->doctrine->getRepository(Group::class)->findAll(), function (Group $group) {
            return $group->getName() !== 'Crew';
        });

        usort($expected, function (Group $group1, Group $group2) {
            return $group1->getName() <=> $group2->getName();
        });

        self::assertEquals($expected, $this->object->getNotResponsibleGroups());

        $this->object->setType(State::TYPE_FINAL);
        self::assertEmpty($this->object->getNotResponsibleGroups());
    }

    public function testJsonSerialize()
    {
        $expected = [
            'id'           => $this->object->getId(),
            'name'         => $this->object->getName(),
            'abbreviation' => $this->object->getAbbreviation(),
            'type'         => $this->object->getType(),
            'responsible'  => $this->object->getResponsible(),
        ];

        self::assertEquals($expected, $this->object->jsonSerialize());
    }
}
