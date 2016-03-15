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

class StateTest extends \PHPUnit_Framework_TestCase
{
    /** @var State */
    private $object = null;

    protected function setUp()
    {
        $this->object = new State();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testTemplateId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setTemplateId($expected);
        $this->assertEquals($expected, $this->object->getTemplateId());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        $this->assertEquals($expected, $this->object->getName());
    }

    public function testAbbreviation()
    {
        $expected = 'Abbreviation';
        $this->object->setAbbreviation($expected);
        $this->assertEquals($expected, $this->object->getAbbreviation());
    }

    public function testType()
    {
        $expected = State::TYPE_INTERIM;
        $this->object->setType($expected);
        $this->assertEquals($expected, $this->object->getType());
    }

    public function testResponsible()
    {
        $expected = State::RESPONSIBLE_ASSIGN;
        $this->object->setResponsible($expected);
        $this->assertEquals($expected, $this->object->getResponsible());
    }

    public function testNextStateId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setNextStateId($expected);
        $this->assertEquals($expected, $this->object->getNextStateId());
    }

    public function testTemplate()
    {
        $this->object->setTemplate($template = new Template());
        $this->assertSame($template, $this->object->getTemplate());
    }

    public function testNextState()
    {
        $this->object->setNextState($state = new State());
        $this->assertSame($state, $this->object->getNextState());
    }

    public function testFields()
    {
        $this->assertCount(0, $this->object->getFields());

        $this->object->addField($field = new Field());
        $this->assertCount(1, $this->object->getFields());

        $this->object->removeField($field);
        $this->assertCount(0, $this->object->getFields());
    }
}
