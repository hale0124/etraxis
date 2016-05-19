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
use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\DecimalValue;
use eTraxis\Entity\Field;
use eTraxis\Entity\FieldGroupPermission;
use eTraxis\Entity\ListItem;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadFieldsData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
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
        $this->loadDeliveryFields($manager);
        $this->loadFuturamaFields($manager);
        $this->loadOtherFields($manager);
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
                    'type'        => Field::TYPE_STRING,
                    'description' => 'Comma-separated list of assigned crew members',
                    'required'    => true,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_READ_ONLY,
                    'registered'  => Field::ACCESS_DENIED,
                    'param1'      => 100,
                    'permissions' => [
                        'group:managers' => Field::ACCESS_READ_WRITE,
                        'group:staff'    => Field::ACCESS_READ_ONLY,
                    ],
                ],
                2 => [
                    'name'        => 'Delivery to',
                    'type'        => Field::TYPE_STRING,
                    'description' => 'A person to deliver to',
                    'required'    => true,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_READ_ONLY,
                    'registered'  => Field::ACCESS_DENIED,
                    'param1'      => 100,
                    'permissions' => [
                        'group:managers' => Field::ACCESS_READ_WRITE,
                        'group:staff'    => Field::ACCESS_READ_ONLY,
                    ],
                ],
                3 => [
                    'name'        => 'Delivery at',
                    'type'        => Field::TYPE_STRING,
                    'description' => 'A place to deliver at',
                    'required'    => true,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_READ_ONLY,
                    'registered'  => Field::ACCESS_DENIED,
                    'param1'      => 100,
                    'permissions' => [
                        'group:managers' => Field::ACCESS_READ_WRITE,
                        'group:staff'    => Field::ACCESS_READ_ONLY,
                    ],
                ],
                4 => [
                    'name'        => 'Notes',
                    'type'        => Field::TYPE_TEXT,
                    'description' => 'Optional notes to the crew',
                    'required'    => false,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_READ_ONLY,
                    'registered'  => Field::ACCESS_DENIED,
                    'param1'      => 1000,
                    'permissions' => [
                        'group:managers' => Field::ACCESS_READ_WRITE,
                        'group:staff'    => Field::ACCESS_READ_ONLY,
                    ],
                ],
            ],
            'state:delivered' => [
                1 => [
                    'name'        => 'Notes',
                    'type'        => Field::TYPE_TEXT,
                    'description' => 'Optional notes from the crew',
                    'required'    => false,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_READ_ONLY,
                    'registered'  => Field::ACCESS_DENIED,
                    'param1'      => 1000,
                    'permissions' => [
                        'group:managers' => Field::ACCESS_READ_WRITE,
                        'group:staff'    => Field::ACCESS_READ_ONLY,
                        'group:crew'     => Field::ACCESS_READ_WRITE,
                    ],
                ],
            ],
        ];

        foreach ($fields as $state_ref => $state_fields) {

            $state = $this->getReference($state_ref);

            foreach ($state_fields as $order => $info) {

                $field = new Field();

                /** @noinspection PhpParamsInspection */
                $field
                    ->setState($state)
                    ->setName($info['name'])
                    ->setType($info['type'])
                    ->setDescription($info['description'])
                    ->setIndexNumber($order)
                    ->setRequired($info['required'])
                    ->setRolePermission(SystemRole::AUTHOR, $info['author'])
                    ->setRolePermission(SystemRole::RESPONSIBLE, $info['responsible'])
                    ->setRolePermission(SystemRole::REGISTERED, $info['registered'])
                ;

                $field->getParameters()
                    ->setParameter1($info['param1'])
                ;

                $this->addReference($state_ref . ':' . $order, $field);

                $manager->persist($field);
            }
        }

        $manager->flush();

        foreach ($fields as $state_ref => $state_fields) {

            foreach ($state_fields as $order => $info) {

                $field = $this->getReference($state_ref . ':' . $order);

                foreach ($info['permissions'] as $group_ref => $permission) {

                    $group = $this->getReference($group_ref);

                    $access = new FieldGroupPermission();

                    /** @noinspection PhpParamsInspection */
                    $access
                        ->setField($field)
                        ->setGroup($group)
                        ->setPermission($permission)
                    ;

                    $manager->persist($access);
                }
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
        $min_value = new DecimalValue();
        $max_value = new DecimalValue();

        $min_value->setValue('0.0');
        $max_value->setValue('10.0');

        $this->addReference('value:decimal:min', $min_value);
        $this->addReference('value:decimal:max', $max_value);

        $manager->persist($min_value);
        $manager->persist($max_value);

        $manager->flush();

        $fields = [
            'state:produced' => [
                1 => [
                    'name'        => 'Season',
                    'type'        => Field::TYPE_LIST,
                    'required'    => true,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_DENIED,
                    'registered'  => Field::ACCESS_READ_ONLY,
                    'param1'      => null,
                    'param2'      => null,
                ],
                2 => [
                    'name'        => 'Episode',
                    'type'        => Field::TYPE_NUMBER,
                    'required'    => true,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_DENIED,
                    'registered'  => Field::ACCESS_READ_ONLY,
                    'param1'      => 1,
                    'param2'      => 100,
                ],
                3 => [
                    'name'        => 'Production code',
                    'type'        => Field::TYPE_STRING,
                    'required'    => true,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_DENIED,
                    'registered'  => Field::ACCESS_READ_ONLY,
                    'param1'      => 7,
                    'param2'      => null,
                ],
                4 => [
                    'name'        => 'Running time',
                    'type'        => Field::TYPE_DURATION,
                    'required'    => true,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_DENIED,
                    'registered'  => Field::ACCESS_READ_ONLY,
                    'param1'      => 0,
                    'param2'      => 1440,
                ],
                5 => [
                    'name'        => 'Multipart',
                    'type'        => Field::TYPE_CHECKBOX,
                    'required'    => true,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_DENIED,
                    'registered'  => Field::ACCESS_READ_ONLY,
                    'param1'      => null,
                    'param2'      => null,
                ],
                6 => [
                    'name'        => 'Plot',
                    'type'        => Field::TYPE_TEXT,
                    'required'    => true,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_DENIED,
                    'registered'  => Field::ACCESS_READ_ONLY,
                    'param1'      => 2000,
                    'param2'      => null,
                ],
                7 => [
                    'name'        => 'Delivery',
                    'type'        => Field::TYPE_RECORD,
                    'required'    => false,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_DENIED,
                    'registered'  => Field::ACCESS_READ_ONLY,
                    'param1'      => null,
                    'param2'      => null,
                ],
            ],
            'state:released' => [
                1 => [
                    'name'        => 'Original air date',
                    'type'        => Field::TYPE_DATE,
                    'required'    => true,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_DENIED,
                    'registered'  => Field::ACCESS_READ_ONLY,
                    'param1'      => 0,
                    'param2'      => 7,
                ],
                2 => [
                    'name'        => 'U.S. viewers',
                    'type'        => Field::TYPE_DECIMAL,
                    'required'    => false,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_DENIED,
                    'registered'  => Field::ACCESS_READ_ONLY,
                    'param1'      => $min_value->getId(),
                    'param2'      => $max_value->getId(),
                ],
            ],
        ];

        foreach ($fields as $state_ref => $state_fields) {

            $state = $this->getReference($state_ref);

            foreach ($state_fields as $order => $info) {

                $field = new Field();

                /** @noinspection PhpParamsInspection */
                $field
                    ->setState($state)
                    ->setName($info['name'])
                    ->setType($info['type'])
                    ->setDescription(null)
                    ->setIndexNumber($order)
                    ->setRequired($info['required'])
                    ->setRolePermission(SystemRole::AUTHOR, $info['author'])
                    ->setRolePermission(SystemRole::RESPONSIBLE, $info['responsible'])
                    ->setRolePermission(SystemRole::REGISTERED, $info['registered'])
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

            $value = new ListItem();

            $value
                ->setField($field)
                ->setKey($i)
                ->setValue('Season ' . $i)
            ;

            $this->addReference('value:list:' . $i, $value);

            $manager->persist($value);
        }

        $manager->flush();
    }

    /**
     * Loads assorted fields not related to any project.
     *
     * @param   ObjectManager $manager
     */
    protected function loadOtherFields(ObjectManager $manager)
    {
        $pi = new DecimalValue();
        $pi->setValue('3.1415926535');

        $manager->persist($pi);
        $manager->flush();
    }
}
