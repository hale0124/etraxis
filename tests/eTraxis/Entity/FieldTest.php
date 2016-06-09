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
use eTraxis\Dictionary\FieldPermission;
use eTraxis\Dictionary\FieldType;
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

        $this->object->setType(FieldType::NUMBER);
        self::assertEquals('number', $this->object->getType());

        $this->object->setType(FieldType::DECIMAL);
        self::assertEquals('decimal', $this->object->getType());

        $this->object->setType(FieldType::STRING);
        self::assertEquals('string', $this->object->getType());

        $this->object->setType(FieldType::TEXT);
        self::assertEquals('text', $this->object->getType());

        $this->object->setType(FieldType::CHECKBOX);
        self::assertEquals('checkbox', $this->object->getType());

        $this->object->setType(FieldType::LIST);
        self::assertEquals('list', $this->object->getType());

        $this->object->setType(FieldType::RECORD);
        self::assertEquals('record', $this->object->getType());

        $this->object->setType(FieldType::DATE);
        self::assertEquals('date', $this->object->getType());

        $this->object->setType(FieldType::DURATION);
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

    public function testOrder()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->object->setOrder($expected);
        self::assertEquals($expected, $this->object->getOrder());
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

    public function testPCRE()
    {
        self::assertNotNull($this->object->getPCRE());
        self::assertInstanceOf(FieldPCRE::class, $this->object->getPCRE());
    }

    public function testParameters()
    {
        self::assertNotNull($this->object->getParameters());
        self::assertInstanceOf(FieldParameters::class, $this->object->getParameters());
    }

    public function testAnyoneRolePermission()
    {
        $expected = FieldPermission::READ_ONLY;

        $this->object->setRolePermission(SystemRole::ANYONE, $expected);
        self::assertEquals($expected, $this->object->getRolePermission(SystemRole::ANYONE));
    }

    public function testAuthorRolePermission()
    {
        $expected = FieldPermission::READ_ONLY;

        $this->object->setRolePermission(SystemRole::AUTHOR, $expected);
        self::assertEquals($expected, $this->object->getRolePermission(SystemRole::AUTHOR));
    }

    public function testResponsibleRolePermission()
    {
        $expected = FieldPermission::READ_ONLY;

        $this->object->setRolePermission(SystemRole::RESPONSIBLE, $expected);
        self::assertEquals($expected, $this->object->getRolePermission(SystemRole::RESPONSIBLE));
    }

    public function testUnknownRolePermission()
    {
        self::assertEquals(FieldPermission::NONE, $this->object->getRolePermission('wtf'));
    }

    public function testSetGroupPermission()
    {
        $expected = FieldPermission::READ_ONLY;

        $this->object->setGroupPermission($group = new Group(), $expected);
        self::assertEquals($expected, $this->object->getGroupPermission($group));
    }

    public function testGetGroupPermission()
    {
        $expected = [
            'Planet Express, Inc.' => FieldPermission::NONE,
            'Nimbus'               => FieldPermission::NONE,
            'Members'              => FieldPermission::NONE,
            'Managers'             => FieldPermission::READ_WRITE,
            'Staff'                => FieldPermission::READ_ONLY,
            'Crew'                 => FieldPermission::NONE,
        ];

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        /** @var Group[] $groups */
        $groups = $this->doctrine->getRepository(Group::class)->findAll();

        foreach ($groups as $group) {
            self::assertEquals($expected[$group->getName()], $field->getGroupPermission($group));
        }
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

    public function testToString()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertRegExp('/^field\#(\d+)$/', (string) $field);
    }

    public function testJsonSerialize()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        $expected = [
            'id'          => $field->getId(),
            'state'       => $field->getState()->getId(),
            'name'        => $field->getName(),
            'type'        => $field->getType(),
            'description' => $field->getDescription(),
            'order'       => $field->getOrder(),
            'isRequired'  => $field->isRequired(),
        ];

        self::assertEquals($expected, $field->jsonSerialize());
    }
}
