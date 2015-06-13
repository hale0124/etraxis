<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------


namespace eTraxis\Model;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    /** @var Field */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Field();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testTemplateId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setTemplateId($expected);
        $this->assertEquals($expected, $this->object->getTemplateId());
    }

    public function testStateId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setStateId($expected);
        $this->assertEquals($expected, $this->object->getStateId());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        $this->assertEquals($expected, $this->object->getName());
    }

    public function testType()
    {
        $expected = Field::TYPE_STRING;
        $this->object->setType($expected);
        $this->assertEquals($expected, $this->object->getType());
    }

    public function testDescription()
    {
        $expected = 'Description';
        $this->object->setDescription($expected);
        $this->assertEquals($expected, $this->object->getDescription());
    }

    public function testIndexNumber()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setIndexNumber($expected);
        $this->assertEquals($expected, $this->object->getIndexNumber());
    }

    public function testRemovedAt()
    {
        $expected = time();
        $this->object->setRemovedAt($expected);
        $this->assertEquals($expected, $this->object->getRemovedAt());
    }

    public function testIsRequired()
    {
        $this->object->setRequired(false);
        $this->assertFalse($this->object->isRequired());

        $this->object->setRequired(true);
        $this->assertTrue($this->object->isRequired());
    }

    public function testHasGuestAccess()
    {
        $this->object->setGuestAccess(false);
        $this->assertFalse($this->object->hasGuestAccess());

        $this->object->setGuestAccess(true);
        $this->assertTrue($this->object->hasGuestAccess());
    }

    public function testRegisteredAccess()
    {
        $expected = Field::ACCESS_READ_ONLY;

        $this->object->setRegisteredAccess($expected);
        $this->assertEquals($expected, $this->object->getRegisteredAccess());
    }

    public function testAuthorAccess()
    {
        $expected = Field::ACCESS_READ_ONLY;

        $this->object->setAuthorAccess($expected);
        $this->assertEquals($expected, $this->object->getAuthorAccess());
    }

    public function testResponsibleAccess()
    {
        $expected = Field::ACCESS_READ_ONLY;

        $this->object->setResponsibleAccess($expected);
        $this->assertEquals($expected, $this->object->getResponsibleAccess());
    }

    public function testShowInEmails()
    {
        $this->object->setShowInEmails(false);
        $this->assertFalse($this->object->getShowInEmails());

        $this->object->setShowInEmails(true);
        $this->assertTrue($this->object->getShowInEmails());
    }

    public function testRegexCheck()
    {
        $expected = 'PCRE';
        $this->object->setRegexCheck($expected);
        $this->assertEquals($expected, $this->object->getRegexCheck());
    }

    public function testRegexSearch()
    {
        $expected = 'PCRE';
        $this->object->setRegexSearch($expected);
        $this->assertEquals($expected, $this->object->getRegexSearch());
    }

    public function testRegexReplace()
    {
        $expected = 'PCRE';
        $this->object->setRegexReplace($expected);
        $this->assertEquals($expected, $this->object->getRegexReplace());
    }

    public function testParameter1()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setParameter1($expected);
        $this->assertEquals($expected, $this->object->getParameter1());
    }

    public function testParameter2()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setParameter2($expected);
        $this->assertEquals($expected, $this->object->getParameter2());
    }

    public function testDefaultValue()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setDefaultValue($expected);
        $this->assertEquals($expected, $this->object->getDefaultValue());
    }

    public function testTemplate()
    {
        $this->object->setTemplate($template = new Template());
        $this->assertSame($template, $this->object->getTemplate());
    }

    public function testState()
    {
        $this->object->setState($state = new State());
        $this->assertSame($state, $this->object->getState());
    }
}