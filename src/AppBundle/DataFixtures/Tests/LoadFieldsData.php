<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\DataFixtures\Tests;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\Dictionary\FieldPermission;
use eTraxis\Dictionary\FieldType;
use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\DecimalValue;
use eTraxis\Entity\Field;
use eTraxis\Entity\ListItem;
use eTraxis\Traits\ReflectionTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadFieldsData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    use ReflectionTrait;

    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 6;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadCommonFields($manager);
        $this->loadDeliveryFields($manager);
        $this->loadFuturamaFields($manager);
        $this->loadPhpPsrFields($manager);
    }

    /**
     * Loads assorted fields not related to any project.
     *
     * @param   ObjectManager $manager
     */
    protected function loadCommonFields(ObjectManager $manager)
    {
        $pi = new DecimalValue('3.1415926535');

        $manager->persist($pi);
        $manager->flush();
    }

    /**
     * Loads fields of "Delivery" template.
     *
     * @param   ObjectManager $manager
     */
    protected function loadDeliveryFields(ObjectManager $manager)
    {
        $fields = [
            'state:new' => [
                1 => [
                    'name'        => 'Crew',
                    'type'        => FieldType::STRING,
                    'description' => 'Comma-separated list of assigned crew members',
                    'required'    => true,
                    'param1'      => 100,
                    'permissions' => [
                        'group:managers' => FieldPermission::READ_WRITE,
                        'group:staff'    => FieldPermission::READ_ONLY,
                    ],
                ],
                0 => [
                    'name'        => 'Package',
                    'type'        => FieldType::STRING,
                    'description' => 'A package description',
                    'required'    => true,
                    'removed'     => '1999-03-28',
                    'param1'      => 100,
                    'permissions' => [
                        'group:managers' => FieldPermission::READ_WRITE,
                        'group:staff'    => FieldPermission::READ_ONLY,
                    ],
                ],
                2 => [
                    'name'        => 'Delivery to',
                    'type'        => FieldType::STRING,
                    'description' => 'A person to deliver to',
                    'required'    => true,
                    'param1'      => 100,
                    'author'      => FieldPermission::NONE,
                    'permissions' => [
                        'group:managers' => FieldPermission::READ_WRITE,
                        'group:staff'    => FieldPermission::READ_ONLY,
                    ],
                ],
                3 => [
                    'name'        => 'Delivery at',
                    'type'        => FieldType::STRING,
                    'description' => 'A place to deliver at',
                    'required'    => true,
                    'param1'      => 100,
                    'responsible' => FieldPermission::NONE,
                    'permissions' => [
                        'group:managers' => FieldPermission::READ_WRITE,
                        'group:staff'    => FieldPermission::READ_ONLY,
                    ],
                ],
                4 => [
                    'name'        => 'Notes',
                    'type'        => FieldType::TEXT,
                    'description' => 'Optional notes to the crew',
                    'required'    => false,
                    'param1'      => 1000,
                    'permissions' => [
                        'group:managers' => FieldPermission::READ_WRITE,
                        'group:crew'     => FieldPermission::READ_ONLY,
                    ],
                ],
            ],
            'state:delivered' => [
                1 => [
                    'name'        => 'Notes',
                    'type'        => FieldType::TEXT,
                    'description' => 'Optional notes from the crew',
                    'required'    => false,
                    'param1'      => 1000,
                    'permissions' => [
                        'group:managers' => FieldPermission::READ_WRITE,
                        'group:crew'     => FieldPermission::READ_WRITE,
                    ],
                ],
            ],
        ];

        foreach ($fields as $state_ref => $state_fields) {

            /** @var \eTraxis\Entity\State $state */
            $state = $this->getReference($state_ref);

            foreach ($state_fields as $order => $info) {

                $field = new Field($state, $info['type']);

                $field
                    ->setName($info['name'])
                    ->setDescription($info['description'])
                    ->setOrder($order)
                    ->setRequired($info['required'])
                    ->setRolePermission(SystemRole::ANYONE, FieldPermission::NONE)
                    ->setRolePermission(SystemRole::AUTHOR, $info['author'] ?? FieldPermission::READ_WRITE)
                    ->setRolePermission(SystemRole::RESPONSIBLE, $info['responsible'] ?? FieldPermission::READ_ONLY)
                ;

                if ($info['removed'] ?? false) {
                    $this->setProperty($field, 'removedAt', strtotime($info['removed']));
                }

                $field->getParameters()
                    ->setParameter1($info['param1'])
                ;

                $this->addReference($state_ref . ':' . $order, $field);

                foreach ($info['permissions'] as $group_ref => $permission) {
                    /** @var \eTraxis\Entity\Group $group */
                    $group = $this->getReference($group_ref);
                    $field->setGroupPermission($group, $permission);
                }

                $manager->persist($field);
            }
        }

        $manager->flush();
    }

    /**
     * Loads fields of "Futurama" template.
     *
     * @param   ObjectManager $manager
     */
    protected function loadFuturamaFields(ObjectManager $manager)
    {
        $min_value = new DecimalValue('0.0');
        $max_value = new DecimalValue('10.0');

        $this->addReference('value:decimal:min', $min_value);
        $this->addReference('value:decimal:max', $max_value);

        $manager->persist($min_value);
        $manager->persist($max_value);

        $manager->flush();

        $fields = [
            'state:produced' => [
                1 => [
                    'name'        => 'Season',
                    'type'        => FieldType::LIST,
                    'required'    => true,
                    'param1'      => null,
                    'param2'      => null,
                ],
                2 => [
                    'name'        => 'Episode',
                    'type'        => FieldType::NUMBER,
                    'required'    => true,
                    'param1'      => 1,
                    'param2'      => 100,
                ],
                3 => [
                    'name'        => 'Production code',
                    'type'        => FieldType::STRING,
                    'required'    => true,
                    'param1'      => 7,
                    'param2'      => null,
                ],
                4 => [
                    'name'        => 'Running time',
                    'type'        => FieldType::DURATION,
                    'required'    => true,
                    'param1'      => 0,
                    'param2'      => 1440,
                ],
                5 => [
                    'name'        => 'Multipart',
                    'type'        => FieldType::CHECKBOX,
                    'required'    => true,
                    'param1'      => null,
                    'param2'      => null,
                ],
                6 => [
                    'name'        => 'Plot',
                    'type'        => FieldType::TEXT,
                    'required'    => true,
                    'param1'      => 2000,
                    'param2'      => null,
                ],
                7 => [
                    'name'        => 'Delivery',
                    'type'        => FieldType::RECORD,
                    'required'    => false,
                    'param1'      => null,
                    'param2'      => null,
                ],
            ],
            'state:released' => [
                1 => [
                    'name'        => 'Original air date',
                    'type'        => FieldType::DATE,
                    'required'    => true,
                    'param1'      => 0,
                    'param2'      => 7,
                ],
                2 => [
                    'name'        => 'U.S. viewers',
                    'type'        => FieldType::DECIMAL,
                    'required'    => false,
                    'param1'      => $min_value->getId(),
                    'param2'      => $max_value->getId(),
                ],
            ],
        ];

        foreach ($fields as $state_ref => $state_fields) {

            /** @var \eTraxis\Entity\State $state */
            $state = $this->getReference($state_ref);

            foreach ($state_fields as $order => $info) {

                $field = new Field($state, $info['type']);

                $field
                    ->setName($info['name'])
                    ->setDescription(null)
                    ->setOrder($order)
                    ->setRequired($info['required'])
                    ->setRolePermission(SystemRole::ANYONE, FieldPermission::READ_ONLY)
                    ->setRolePermission(SystemRole::AUTHOR, FieldPermission::READ_WRITE)
                    ->setRolePermission(SystemRole::RESPONSIBLE, FieldPermission::NONE)
                ;

                $field->getParameters()
                    ->setParameter1($info['param1'])
                    ->setParameter2($info['param2'])
                ;

                $this->addReference($state_ref . ':' . $order, $field);

                $manager->persist($field);
            }
        }

        $manager->flush();

        /** @var Field $field */
        $field = $this->getReference('state:produced:1');

        for ($i = 1; $i <= 7; $i++) {

            $value = new ListItem($field);

            $value
                ->setValue($i)
                ->setText('Season ' . $i)
            ;

            $this->addReference('value:list:' . $i, $value);

            $manager->persist($value);
        }

        $manager->flush();
    }

    /**
     * Loads fields of "PSR" template.
     *
     * @param   ObjectManager $manager
     */
    protected function loadPhpPsrFields(ObjectManager $manager)
    {
        $fields = [
            1 => [
                'name'     => 'PSR ID',
                'type'     => FieldType::STRING,
                'required' => true,
                'param1'   => 2,
                'param2'   => null,
            ],
            2 => [
                'name'     => 'Description',
                'type'     => FieldType::TEXT,
                'required' => false,
                'param1'   => 2000,
                'param2'   => null,
            ],
        ];

        /** @var \eTraxis\Entity\State $state */
        $state = $this->getReference('state:psr:draft');

        /** @var \eTraxis\Entity\Group $members */
        $members = $this->getReference('group:fig:members');

        foreach ($fields as $order => $info) {

            $field = new Field($state, $info['type']);

            $field
                ->setName($info['name'])
                ->setDescription(null)
                ->setOrder($order)
                ->setRequired($info['required'])
                ->setRolePermission(SystemRole::ANYONE, FieldPermission::NONE)
                ->setRolePermission(SystemRole::AUTHOR, FieldPermission::READ_WRITE)
                ->setRolePermission(SystemRole::RESPONSIBLE, FieldPermission::NONE)
                ->setGroupPermission($members, FieldPermission::READ_ONLY)
            ;

            $field->getParameters()
                ->setParameter1($info['param1'])
                ->setParameter2($info['param2'])
            ;

            $this->addReference('state:psr:draft:' . $order, $field);

            $manager->persist($field);
        }

        /** @var Field $field */
        $field = $this->getReference('state:psr:draft:1');

        $field->getPCRE()
            ->setCheck('(\\d+)')
            ->setSearch('(\\d+)')
            ->setReplace('PSR-$1')
        ;

        $manager->flush();
    }
}
