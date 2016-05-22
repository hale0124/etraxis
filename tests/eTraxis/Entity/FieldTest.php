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
use eTraxis\Dictionary\SystemRole;
use eTraxis\Tests\TransactionalTestCase;

class FieldTest extends TransactionalTestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Field();

        /** @noinspection PhpParamsInspection */
        $this->object->setEntityManager($this->doctrine->getManager());
    }

    public function testId()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected   = random_int(1, PHP_INT_MAX);
        $object->id = $expected;
        self::assertEquals($expected, $this->object->getId());
    }

    public function testState()
    {
        $this->object->setState($state = new State());
        self::assertEquals($state, $this->object->getState());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        self::assertEquals($expected, $this->object->getName());
    }

    public function testType()
    {
        self::assertNull($this->object->getType());

        $this->object->setType(Field::TYPE_NUMBER);
        self::assertEquals('number', $this->object->getType());

        $this->object->setType(Field::TYPE_DECIMAL);
        self::assertEquals('decimal', $this->object->getType());

        $this->object->setType(Field::TYPE_STRING);
        self::assertEquals('string', $this->object->getType());

        $this->object->setType(Field::TYPE_TEXT);
        self::assertEquals('text', $this->object->getType());

        $this->object->setType(Field::TYPE_CHECKBOX);
        self::assertEquals('checkbox', $this->object->getType());

        $this->object->setType(Field::TYPE_LIST);
        self::assertEquals('list', $this->object->getType());

        $this->object->setType(Field::TYPE_RECORD);
        self::assertEquals('record', $this->object->getType());

        $this->object->setType(Field::TYPE_DATE);
        self::assertEquals('date', $this->object->getType());

        $this->object->setType(Field::TYPE_DURATION);
        self::assertEquals('duration', $this->object->getType());

        $this->object->setType('unknown');
        self::assertEquals('duration', $this->object->getType());
    }

    public function testDescription()
    {
        $expected = 'Description';
        $this->object->setDescription($expected);
        self::assertEquals($expected, $this->object->getDescription());
    }

    public function testIndexNumber()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->object->setIndexNumber($expected);
        self::assertEquals($expected, $this->object->getIndexNumber());
    }

    public function testRemove()
    {
        self::assertFalse($this->object->isRemoved());

        $this->object->remove();
        self::assertTrue($this->object->isRemoved());
    }

    public function testIsRequired()
    {
        $this->object->setRequired(false);
        self::assertFalse($this->object->isRequired());

        $this->object->setRequired(true);
        self::assertTrue($this->object->isRequired());
    }

    public function testAuthorPermission()
    {
        $expected = Field::ACCESS_READ_ONLY;

        $this->object->setRolePermission(SystemRole::AUTHOR, $expected);
        self::assertEquals($expected, $this->object->getRolePermission(SystemRole::AUTHOR));
    }

    public function testResponsiblePermission()
    {
        $expected = Field::ACCESS_READ_ONLY;

        $this->object->setRolePermission(SystemRole::RESPONSIBLE, $expected);
        self::assertEquals($expected, $this->object->getRolePermission(SystemRole::RESPONSIBLE));
    }

    public function testRegisteredPermission()
    {
        $expected = Field::ACCESS_READ_ONLY;

        $this->object->setRolePermission(SystemRole::REGISTERED, $expected);
        self::assertEquals($expected, $this->object->getRolePermission(SystemRole::REGISTERED));
    }

    public function testUnknownRolePermission()
    {
        self::assertEquals(Field::ACCESS_DENIED, $this->object->getRolePermission(PHP_INT_MIN));
    }

    public function testGetGroupPermission()
    {
        $expected = [
            'Planet Express, Inc.' => Field::ACCESS_DENIED,
            'Nimbus'               => Field::ACCESS_DENIED,
            'Managers'             => Field::ACCESS_READ_WRITE,
            'Staff'                => Field::ACCESS_READ_ONLY,
            'Crew'                 => Field::ACCESS_DENIED,
        ];

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        /** @var Group[] $groups */
        $groups = $this->doctrine->getRepository(Group::class)->findAll();

        foreach ($groups as $group) {
            self::assertEquals($expected[$group->getName()], $field->getGroupPermission($group));
        }
    }

    public function testRegex()
    {
        self::assertNotNull($this->object->getRegex());
        self::assertInstanceOf(FieldRegex::class, $this->object->getRegex());
    }

    public function testParameters()
    {
        self::assertNotNull($this->object->getParameters());
        self::assertInstanceOf(FieldParameters::class, $this->object->getParameters());
    }

    public function testFacades()
    {
        self::assertInstanceOf(Fields\NumberField::class,   $this->object->asNumber());
        self::assertInstanceOf(Fields\DecimalField::class,  $this->object->asDecimal());
        self::assertInstanceOf(Fields\StringField::class,   $this->object->asString());
        self::assertInstanceOf(Fields\TextField::class,     $this->object->asText());
        self::assertInstanceOf(Fields\CheckboxField::class, $this->object->asCheckbox());
        self::assertInstanceOf(Fields\ListField::class,     $this->object->asList());
        self::assertInstanceOf(Fields\RecordField::class,   $this->object->asRecord());
        self::assertInstanceOf(Fields\DateField::class,     $this->object->asDate());
        self::assertInstanceOf(Fields\DurationField::class, $this->object->asDuration());
    }

    public function testJsonSerialize()
    {
        $expected = [
            'id',
            'name',
            'type',
            'description',
            'isRequired',
        ];

        self::assertEquals($expected, array_keys($this->object->jsonSerialize()));
    }
}
