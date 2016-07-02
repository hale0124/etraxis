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

use eTraxis\Dictionary\FieldPermission;
use eTraxis\Dictionary\FieldType;
use eTraxis\Dictionary\SystemRole;
use eTraxis\Tests\TransactionalTestCase;
use eTraxis\Traits\ReflectionTrait;

class FieldTest extends TransactionalTestCase
{
    use ReflectionTrait;

    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(Field::class)->findOneBy([
            'name' => 'Crew',
        ]);
    }

    public function testConstruct()
    {
        $state = $this->object->getState();

        $field = new Field($state, FieldType::STRING);
        self::assertEquals($state, $field->getState());
    }

    public function testId()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->setProperty($this->object, 'id', $expected);
        self::assertEquals($expected, $this->object->getId());
    }

    public function testState()
    {
        $expected = 'New';
        self::assertEquals($expected, $this->object->getState()->getName());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        self::assertEquals($expected, $this->object->getName());
    }

    public function testType()
    {
        self::assertEquals('string', $this->object->getType());
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
        self::assertEquals(FieldPermission::NONE, $this->object->getRolePermission(SystemRole::ANYONE));

        $expected = FieldPermission::READ_ONLY;
        $this->object->setRolePermission(SystemRole::ANYONE, $expected);
        self::assertEquals($expected, $this->object->getRolePermission(SystemRole::ANYONE));
    }

    public function testAuthorRolePermission()
    {
        self::assertEquals(FieldPermission::READ_WRITE, $this->object->getRolePermission(SystemRole::AUTHOR));

        $expected = FieldPermission::READ_ONLY;
        $this->object->setRolePermission(SystemRole::AUTHOR, $expected);
        self::assertEquals($expected, $this->object->getRolePermission(SystemRole::AUTHOR));
    }

    public function testResponsibleRolePermission()
    {
        self::assertEquals(FieldPermission::READ_ONLY, $this->object->getRolePermission(SystemRole::RESPONSIBLE));

        $expected = FieldPermission::READ_WRITE;
        $this->object->setRolePermission(SystemRole::RESPONSIBLE, $expected);
        self::assertEquals($expected, $this->object->getRolePermission(SystemRole::RESPONSIBLE));
    }

    public function testUnknownRolePermission()
    {
        self::assertEquals(FieldPermission::NONE, $this->object->getRolePermission('wtf'));
    }

    public function testSetGroupReadPermission()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);
        self::assertEquals(FieldPermission::READ_WRITE, $this->object->getGroupPermission($group));

        $expected = FieldPermission::READ_ONLY;
        $this->object->setGroupPermission($group, $expected);
        self::assertEquals($expected, $this->object->getGroupPermission($group));
    }

    public function testSetGroupWritePermission()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Staff']);
        self::assertEquals(FieldPermission::READ_ONLY, $this->object->getGroupPermission($group));

        $expected = FieldPermission::READ_WRITE;
        $this->object->setGroupPermission($group, $expected);
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

        /** @var Group[] $groups */
        $groups = $this->doctrine->getRepository(Group::class)->findAll();

        foreach ($groups as $group) {
            self::assertEquals($expected[$group->getName()], $this->object->getGroupPermission($group));
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
        self::assertRegExp('/^field\#(\d+)$/', (string) $this->object);
    }

    public function testJsonSerialize()
    {
        $expected = [
            'id'          => $this->object->getId(),
            'state'       => $this->object->getState()->getId(),
            'name'        => $this->object->getName(),
            'type'        => $this->object->getType(),
            'description' => $this->object->getDescription(),
            'order'       => $this->object->getOrder(),
            'isRequired'  => $this->object->isRequired(),
        ];

        self::assertEquals($expected, $this->object->jsonSerialize());
    }
}
