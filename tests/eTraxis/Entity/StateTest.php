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

class StateTest extends \PHPUnit_Framework_TestCase
{
    /** @var State */
    private $object;

    protected function setUp()
    {
        $this->object = new State();
    }

    public function testId()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected   = mt_rand(1, PHP_INT_MAX);
        $object->id = $expected;
        self::assertEquals($expected, $this->object->getId());
    }

    public function testTemplate()
    {
        $this->object->setTemplate($template = new Template());
        self::assertSame($template, $this->object->getTemplate());
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
    }

    public function testResponsible()
    {
        $expected = State::RESPONSIBLE_ASSIGN;
        $this->object->setResponsible($expected);
        self::assertEquals($expected, $this->object->getResponsible());
    }

    public function testNextState()
    {
        $this->object->setNextState($state = new State());
        self::assertSame($state, $this->object->getNextState());
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
            'abbreviation',
            'type',
            'responsible',
        ];

        self::assertEquals($expected, array_keys($this->object->jsonSerialize()));
    }
}
