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
use eTraxis\Model\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUsersData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
        return 1;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
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
        ];

        foreach ($data as $username => $row) {

            $user = new User();

            $user
                ->setUsername($username)
                ->setFullname($row['fullname'])
                ->setEmail($row['email'])
                ->setPassword($row['is_ldap'] ? null : '5en6G6MezRroT3XKqkdPOmY/BfQ=')
                ->setAdmin($row['is_admin'])
                ->setDisabled($row['is_disabled'])
                ->setLdap($row['is_ldap'])
                ->setLocale($this->container->getParameter('locale'))
                ->setTheme($this->container->getParameter('theme'))
            ;

            if (array_key_exists('description', $row)) {
                $user->setDescription($row['description']);
            }

            $this->addReference('user:' . $username, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
