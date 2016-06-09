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

use eTraxis\Dictionary\StateResponsible;
use eTraxis\Dictionary\StateType;
use eTraxis\Dictionary\SystemRole;
use eTraxis\Tests\TransactionalTestCase;

class StateTest extends TransactionalTestCase
{
    /** @var State */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(State::class)->findOneBy([
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
        $expected = StateType::INTERIM;
        $this->object->setType($expected);
        self::assertEquals($expected, $this->object->getType());
        self::assertEquals(StateResponsible::ASSIGN, $this->object->getResponsible());

        $expected = StateType::FINAL;
        $this->object->setType($expected);
        self::assertEquals($expected, $this->object->getType());
        self::assertEquals(StateResponsible::REMOVE, $this->object->getResponsible());
    }

    public function testResponsible()
    {
        $expected = StateResponsible::KEEP;
        self::assertNotEquals($expected, $this->object->getResponsible());
        $this->object->setResponsible($expected);
        self::assertEquals($expected, $this->object->getResponsible());
    }

    public function testResponsibleOnFinal()
    {
        $expected = StateResponsible::KEEP;
        self::assertNotEquals($expected, $this->object->getResponsible());

        $this->object->setType(StateType::FINAL);
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

        $this->object->setType(StateType::FINAL);
        self::assertNull($this->object->getNextState());
        $this->object->setNextState($state);
        self::assertNull($this->object->getNextState());
    }

    public function testFields()
    {
        self::assertCount(4, $this->object->getFields());
    }

    public function testRoleTransitions()
    {
        /** @var State $delivered */
        $delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        $expected = [
            $delivered,
        ];

        self::assertArraysByValues($expected, $this->object->getRoleTransitions(SystemRole::RESPONSIBLE));

        $this->object->setRoleTransitions(SystemRole::RESPONSIBLE, [$this->object]);
        self::assertArraysByValues([$this->object], $this->object->getRoleTransitions(SystemRole::RESPONSIBLE));

        $this->object->setType(StateType::FINAL);
        self::assertEmpty($this->object->getRoleTransitions(SystemRole::RESPONSIBLE));
    }

    public function testGroupTransitions()
    {
        /** @var Group $managers */
        $managers = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        /** @var State $delivered */
        $delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        $expected = [
            $delivered,
        ];

        self::assertArraysByValues($expected, $this->object->getGroupTransitions($managers));

        $this->object->setGroupTransitions($managers, [$this->object]);
        self::assertArraysByValues([$this->object], $this->object->getGroupTransitions($managers));

        $this->object->setType(StateType::FINAL);
        self::assertEmpty($this->object->getGroupTransitions($managers));
    }

    public function testAddResponsibleGroups()
    {
        /** @var Group $crew */
        $crew = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);

        /** @var Group $managers */
        $managers = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        self::assertArraysByValues([$crew], $this->object->getResponsibleGroups());

        $this->object->addResponsibleGroups([$crew, $managers]);

        self::assertArraysByValues([$crew, $managers], $this->object->getResponsibleGroups());

        $this->object->setType(StateType::FINAL);
        self::assertEmpty($this->object->getResponsibleGroups());
    }

    public function testRemoveResponsibleGroups()
    {
        /** @var Group $crew */
        $crew = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);

        /** @var Group $managers */
        $managers = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        self::assertArraysByValues([$crew], $this->object->getResponsibleGroups());

        $this->object->removeResponsibleGroups([$crew, $managers]);
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

        self::assertArraysByValues($expected, $this->object->getNotResponsibleGroups());

        $this->object->setType(StateType::FINAL);
        self::assertEmpty($this->object->getNotResponsibleGroups());
    }

    public function testToString()
    {
        self::assertRegExp('/^state\#(\d+)$/', (string) $this->object);
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
