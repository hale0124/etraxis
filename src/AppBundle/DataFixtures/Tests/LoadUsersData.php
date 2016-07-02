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
use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Entity\User;
use eTraxis\Security\InternalPasswordEncoder;
use eTraxis\Traits\ReflectionTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUsersData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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

            // Assorted users.

            'artem' => [
                'fullname'   => 'Artem Rodygin',
                'email'      => 'artem@example.com',
                'isAdmin'    => true,
                'isDisabled' => false,
            ],

            'einstein' => [
                'provider'   => AuthenticationProvider::LDAP,
                'fullname'   => 'Albert Einstein',
                'email'      => 'einstein@ldap.forumsys.com',
                'isAdmin'    => false,
                'isDisabled' => false,
            ],

            // Futurama users.

            'hubert' => [
                'fullname'    => 'Hubert J. Farnsworth',
                'email'       => 'hubert@planetexpress.com',
                'description' => 'Founder / Owner',
                'isAdmin'     => true,
                'isDisabled'  => false,
            ],

            'fry' => [
                'fullname'    => 'Philip J. Fry',
                'email'       => 'fry@planetexpress.com',
                'description' => 'Delivery Crew',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'bender' => [
                'fullname'    => 'Bender Bending Rodriguez',
                'email'       => 'bender@planetexpress.com',
                'description' => 'Delivery Crew',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'leela' => [
                'fullname'    => 'Turanga Leela',
                'email'       => 'leela@planetexpress.com',
                'description' => 'Delivery Crew',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'amy' => [
                'fullname'    => 'Dr. Amy Wong',
                'email'       => 'amy@planetexpress.com',
                'description' => 'Delivery Crew',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'zoidberg' => [
                'fullname'    => 'Dr. John A. Zoidberg',
                'email'       => 'zoidberg@planetexpress.com',
                'description' => 'Staff Doctor',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'hermes' => [
                'fullname'    => 'Hermes Conrad',
                'email'       => 'hermes@planetexpress.com',
                'description' => 'Grade 36 Bureaucrat',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'scruffy' => [
                'fullname'    => 'Scruffy',
                'email'       => 'scruffy@planetexpress.com',
                'description' => 'Janitor',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'zapp' => [
                'fullname'    => 'Zapp Brannigan',
                'email'       => 'captain@nimbus.com',
                'description' => 'Captain',
                'isAdmin'     => false,
                'isDisabled'  => false,
                'lockedUntil' => 0x7FFFFFFF,
            ],

            'kif' => [
                'fullname'    => 'Kif Kroker',
                'email'       => 'kif@nimbus.com',
                'description' => 'Fourth Lieutenant ',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'veins' => [
                'fullname'    => 'Dr. Veins McGee',
                'email'       => 'veins@nimbus.com',
                'description' => 'Doctor',
                'isAdmin'     => false,
                'isDisabled'  => true,
            ],

            'francine' => [
                'fullname'    => 'Francine',
                'email'       => 'francine@nimbus.com',
                'description' => 'Officer',
                'isAdmin'     => false,
                'isDisabled'  => true,
            ],

            // PHP-FIG users.

            'mwop' => [
                'fullname'    => 'Matthew Weier O\'Phinney',
                'email'       => 'mwop@example.com',
                'description' => 'Zend Framework 2',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'pmjones' => [
                'fullname'    => 'Paul M. Jones',
                'email'       => 'pmjones@example.com',
                'description' => 'Aura Project and Solar Framework',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'seldaek' => [
                'fullname'    => 'Jordi Boggiano',
                'email'       => 'seldaek@example.com',
                'description' => 'Composer',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'mvriel' => [
                'fullname'    => 'Mike van Riel',
                'email'       => 'mvriel@example.com',
                'description' => 'phpDocumentor',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'Crell' => [
                'fullname'    => 'Larry Garfield',
                'email'       => 'Crell@example.com',
                'description' => 'Drupal',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'lsmith' => [
                'fullname'    => 'Lukas Kahwe Smith',
                'email'       => 'lsmith@example.com',
                'description' => 'Jackalope',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'moufmouf' => [
                'fullname'   => 'David Négrier',
                'email'      => 'moufmouf@example.com',
                'isAdmin'    => false,
                'isDisabled' => false,
            ],

            'korvinszanto' => [
                'fullname'    => 'Korvin Szanto',
                'email'       => 'korvinszanto@example.com',
                'description' => 'concrete5',
                'isAdmin'     => false,
                'isDisabled'  => false,
            ],

            'manchuck' => [
                'fullname'   => 'Chuck Reeves',
                'email'      => 'manchuck@example.com',
                'isAdmin'    => false,
                'isDisabled' => false,
            ],
        ];

        foreach ($data as $username => $row) {

            $user = new User(AuthenticationProvider::ETRAXIS);

            $this->setProperty($user, 'username', $username);
            $this->setProperty($user, 'password', $encoder->encodePassword('secret'));

            foreach ($row as $property => $value) {
                $this->setProperty($user, $property, $value);
            }

            $this->addReference('user:' . $username, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
