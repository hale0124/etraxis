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

class FieldTest extends BaseTestCase
{
    /** @var Field */
    private $object = null;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Field();

        /** @noinspection PhpParamsInspection */
        $this->object
            ->setDecimalValuesRepository($this->doctrine->getRepository(DecimalValue::class))
            ->setStringValuesRepository($this->doctrine->getRepository(StringValue::class))
            ->setTextValuesRepository($this->doctrine->getRepository(TextValue::class))
            ->setListItemsRepository($this->doctrine->getRepository(ListItem::class))
        ;
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

    public function testTypeEx()
    {
        $this->object->setType(Field::TYPE_NUMBER);
        $this->assertEquals('number', $this->object->getTypeEx());

        $this->object->setType(Field::TYPE_DECIMAL);
        $this->assertEquals('decimal', $this->object->getTypeEx());

        $this->object->setType(Field::TYPE_STRING);
        $this->assertEquals('string', $this->object->getTypeEx());

        $this->object->setType(Field::TYPE_TEXT);
        $this->assertEquals('text', $this->object->getTypeEx());

        $this->object->setType(Field::TYPE_CHECKBOX);
        $this->assertEquals('checkbox', $this->object->getTypeEx());

        $this->object->setType(Field::TYPE_LIST);
        $this->assertEquals('list', $this->object->getTypeEx());

        $this->object->setType(Field::TYPE_RECORD);
        $this->assertEquals('record', $this->object->getTypeEx());

        $this->object->setType(Field::TYPE_DATE);
        $this->assertEquals('date', $this->object->getTypeEx());

        $this->object->setType(Field::TYPE_DURATION);
        $this->assertEquals('duration', $this->object->getTypeEx());

        $this->object->setType(0);
        $this->assertNull($this->object->getTypeEx());
    }

    public function testFacades()
    {
        $this->assertInstanceOf(Fields\NumberField::class,  $this->object->asNumber());
        $this->assertInstanceOf(Fields\DecimalField::class, $this->object->asDecimal());
        $this->assertInstanceOf(Fields\StringField::class,  $this->object->asString());
        $this->assertInstanceOf(Fields\TextField::class,    $this->object->asText());
    }
}
