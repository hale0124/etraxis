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

    public function testConstruct()
    {
        $template = $this->object->getTemplate();

        $state = new State($template, StateType::INTERIM);
        self::assertEquals($template, $state->getTemplate());
        self::assertNull($state->getResponsible());

        $state = new State($template, StateType::FINAL);
        self::assertEquals($template, $state->getTemplate());
        self::assertEquals(StateResponsible::REMOVE, $state->getResponsible());
    }

    public function testId()
    {
        $state = new State($this->object->getTemplate(), StateType::INTERIM);
        self::assertNull($state->getId());
        self::assertNotNull($this->object->getId());
    }

    public function testTemplate()
    {
        $expected = 'Delivery';
        self::assertEquals($expected, $this->object->getTemplate()->getName());
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
        $expected = StateType::INITIAL;
        self::assertEquals($expected, $this->object->getType());
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

        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $object->type = StateType::FINAL;
        $this->object->setResponsible($expected);
        self::assertNotEquals($expected, $this->object->getResponsible());
    }

    public function testNextState()
    {
        $project = $this->object->getTemplate()->getProject();

        $state = new State(new Template($project), StateType::INTERIM);
        $this->object->setNextState($state);
        self::assertNotEquals($state, $this->object->getNextState());

        $state = new State($this->object->getTemplate(), StateType::INTERIM);
        $this->object->setNextState($state);
        self::assertEquals($state, $this->object->getNextState());

        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $object->type = StateType::FINAL;
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

        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $object->type = StateType::FINAL;
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

        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $object->type = StateType::FINAL;
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

        $this->object->setResponsible(StateResponsible::REMOVE);
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

        $this->object->setResponsible(StateResponsible::REMOVE);
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
            'template'     => $this->object->getTemplate()->getId(),
            'name'         => $this->object->getName(),
            'abbreviation' => $this->object->getAbbreviation(),
            'type'         => $this->object->getType(),
            'responsible'  => $this->object->getResponsible(),
        ];

        self::assertEquals($expected, $this->object->jsonSerialize());
    }
}
