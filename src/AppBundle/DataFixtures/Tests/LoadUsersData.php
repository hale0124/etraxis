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
use eTraxis\Entity\User;
use eTraxis\Security\InternalPasswordEncoder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUsersData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @noinspection PhpParamsInspection */
        $encoder = new InternalPasswordEncoder(
            $this->container->get('translator'),
            $this->container->getParameter('password_min_length')
        );

        $data = [

            'artem' => [
                'fullname'    => 'Artem Rodygin',
                'email'       => 'artem@example.com',
                'password'    => 'secret',
                'is_admin'    => true,
                'is_disabled' => false,
                'is_ldap'     => false,
            ],

            'einstein' => [
                'fullname'    => 'Albert Einstein',
                'email'       => 'einstein@ldap.forumsys.com',
                'is_admin'    => false,
                'is_disabled' => false,
                'is_ldap'     => true,
            ],

            'hubert' => [
                'fullname'    => 'Hubert J. Farnsworth',
                'email'       => 'hubert@planetexpress.com',
                'password'    => 'secret',
                'description' => 'Founder / Owner',
                'is_admin'    => true,
                'is_disabled' => false,
                'is_ldap'     => false,
            ],

            'fry' => [
                'fullname'    => 'Philip J. Fry',
                'email'       => 'fry@planetexpress.com',
                'password'    => 'secret',
                'description' => 'Delivery Crew',
                'is_admin'    => false,
                'is_disabled' => false,
                'is_ldap'     => false,
            ],

            'bender' => [
                'fullname'    => 'Bender Bending Rodriguez',
                'email'       => 'bender@planetexpress.com',
                'password'    => 'secret',
                'description' => 'Delivery Crew',
                'is_admin'    => false,
                'is_disabled' => false,
                'is_ldap'     => false,
            ],

            'leela' => [
                'fullname'    => 'Turanga Leela',
                'email'       => 'leela@planetexpress.com',
                'password'    => 'secret',
                'description' => 'Delivery Crew',
                'is_admin'    => false,
                'is_disabled' => false,
                'is_ldap'     => false,
            ],

            'amy' => [
                'fullname'    => 'Dr. Amy Wong',
                'email'       => 'amy@planetexpress.com',
                'password'    => 'secret',
                'description' => 'Delivery Crew',
                'is_admin'    => false,
                'is_disabled' => false,
                'is_ldap'     => false,
            ],

            'zoidberg' => [
                'fullname'    => 'Dr. John A. Zoidberg',
                'email'       => 'zoidberg@planetexpress.com',
                'password'    => 'secret',
                'description' => 'Staff Doctor',
                'is_admin'    => false,
                'is_disabled' => false,
                'is_ldap'     => false,
            ],

            'hermes' => [
                'fullname'    => 'Hermes Conrad',
                'email'       => 'hermes@planetexpress.com',
                'password'    => 'secret',
                'description' => 'Grade 36 Bureaucrat',
                'is_admin'    => false,
                'is_disabled' => false,
                'is_ldap'     => false,
            ],

            'scruffy' => [
                'fullname'    => 'Scruffy',
                'email'       => 'scruffy@planetexpress.com',
                'password'    => 'secret',
                'description' => 'Janitor',
                'is_admin'    => false,
                'is_disabled' => false,
                'is_ldap'     => false,
            ],

            'zapp' => [
                'fullname'    => 'Zapp Brannigan',
                'email'       => 'captain@nimbus.com',
                'password'    => 'secret',
                'description' => 'Captain',
                'is_admin'    => false,
                'is_disabled' => false,
                'is_ldap'     => false,
            ],

            'kif' => [
                'fullname'    => 'Kif Kroker',
                'email'       => 'kif@nimbus.com',
                'password'    => 'secret',
                'description' => 'Fourth Lieutenant ',
                'is_admin'    => false,
                'is_disabled' => false,
                'is_ldap'     => false,
            ],

            'veins' => [
                'fullname'    => 'Dr. Veins McGee',
                'email'       => 'veins@nimbus.com',
                'password'    => 'secret',
                'description' => 'Doctor',
                'is_admin'    => false,
                'is_disabled' => true,
                'is_ldap'     => false,
            ],

            'francine' => [
                'fullname'    => 'Francine',
                'email'       => 'francine@nimbus.com',
                'password'    => 'secret',
                'description' => 'Officer',
                'is_admin'    => false,
                'is_disabled' => true,
                'is_ldap'     => false,
            ],
        ];

        foreach ($data as $username => $row) {

            $user = new User();

            $user
                ->setUsername($username)
                ->setFullname($row['fullname'])
                ->setEmail($row['email'])
                ->setPassword($row['is_ldap'] ? null : $encoder->encodePassword($row['password']))
                ->setAdmin($row['is_admin'])
                ->setDisabled($row['is_disabled'])
                ->setLdap($row['is_ldap'])
                ->setLocale($this->container->getParameter('locale'))
                ->setTheme($this->container->getParameter('theme'))
            ;

            if (array_key_exists('description', $row)) {
                $user->setDescription($row['description']);
            }

            // Make Zapp locked out.
            if ($username == 'zapp') {
                $forever = (1 << 31) - 1;
                $user->setLockedUntil($forever);
            }

            $this->addReference('user:' . $username, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
