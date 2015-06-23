<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\DataFixtures\Tests;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\Model\Field;
use eTraxis\Model\FieldGroupAccess;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadFieldsData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 6;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $fields = [
            'state:new' => [
                1 => [
                    'name'        => 'Crew',
                    'type'        => Field::TYPE_STRING,
                    'description' => 'Comma-separated list of assigned crew members',
                    'required'    => true,
                    'guest'       => true,
                    'registered'  => Field::ACCESS_DENIED,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_READ_ONLY,
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
                    'guest'       => true,
                    'registered'  => Field::ACCESS_DENIED,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_READ_ONLY,
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
                    'guest'       => true,
                    'registered'  => Field::ACCESS_DENIED,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_READ_ONLY,
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
                    'guest'       => true,
                    'registered'  => Field::ACCESS_DENIED,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_READ_ONLY,
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
                    'guest'       => false,
                    'registered'  => Field::ACCESS_DENIED,
                    'author'      => Field::ACCESS_READ_WRITE,
                    'responsible' => Field::ACCESS_READ_ONLY,
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

            foreach ($state_fields as $order => $info) {

                $state = $this->getReference($state_ref);

                $field = new Field();

                /** @noinspection PhpParamsInspection */
                $field
                    ->setName($info['name'])
                    ->setType($info['type'])
                    ->setDescription($info['description'])
                    ->setIndexNumber($order)
                    ->setRemovedAt(0)
                    ->setRequired($info['required'])
                    ->setGuestAccess($info['guest'])
                    ->setRegisteredAccess($info['registered'])
                    ->setAuthorAccess($info['author'])
                    ->setResponsibleAccess($info['responsible'])
                    ->setShowInEmails(false)
                    ->setParameter1($info['param1'])
                    ->setTemplate($state->getTemplate())
                    ->setState($state)
                ;

                $this->addReference($state_ref . ':' . $order, $field);

                $manager->persist($field);
            }
        }

        $manager->flush();

        foreach ($fields as $state_ref => $state_fields) {

            foreach ($state_fields as $order => $info) {

                foreach ($info['permissions'] as $group_ref => $permissions) {

                    $field = $this->getReference($state_ref . ':' . $order);
                    $group = $this->getReference($group_ref);

                    $access = new FieldGroupAccess();

                    /** @noinspection PhpParamsInspection */
                    $access
                        ->setFieldId($field->getId())
                        ->setGroupId($group->getId())
                        ->setAccess($permissions)
                        ->setField($field)
                        ->setGroup($group)
                    ;

                    $manager->persist($access);
                }
            }
        }

        $manager->flush();
    }
}
