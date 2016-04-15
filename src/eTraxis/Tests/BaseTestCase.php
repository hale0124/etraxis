<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Tests;

use eTraxis\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Basic test case with database transactions, users authentication, and access to kernel.
 */
class BaseTestCase extends WebTestCase
{
    /** @var \Symfony\Bundle\FrameworkBundle\Client */
    protected $client;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Symfony\Bundle\FrameworkBundle\Routing\Router */
    protected $router;

    /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface */
    protected $session;

    /** @var \Symfony\Component\Validator\Validator\ValidatorInterface */
    protected $validator;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    protected $translator;

    /** @var \Symfony\Bridge\Doctrine\RegistryInterface */
    protected $doctrine;

    /** @var \SimpleBus\Message\Bus\MessageBus */
    protected $command_bus;

    /** @var \SimpleBus\Message\Bus\MessageBus */
    protected $event_bus;

    /** @var \DataTables\DataTablesInterface */
    protected $datatables;

    /**
     * Begins new transaction.
     */
    protected function setUp()
    {
        $this->client = static::createClient();

        $this->logger      = $this->client->getContainer()->get('logger');
        $this->router      = $this->client->getContainer()->get('router');
        $this->session     = $this->client->getContainer()->get('session');
        $this->validator   = $this->client->getContainer()->get('validator');
        $this->translator  = $this->client->getContainer()->get('translator');
        $this->doctrine    = $this->client->getContainer()->get('doctrine');
        $this->command_bus = $this->client->getContainer()->get('command_bus');
        $this->event_bus   = $this->client->getContainer()->get('event_bus');
        $this->datatables  = $this->client->getContainer()->get('datatables');

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();
        $manager->beginTransaction();
    }

    /**
     * Rolls back current transaction.
     */
    protected function tearDown()
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();
        $manager->rollback();

        parent::tearDown();
    }

    /**
     * Returns maximum value of signed 32-bits integer which can be used as Id of non-existing entity.
     *
     * @return  int
     */
    protected function getMaxId()
    {
        return 0x7FFFFFFF;
    }

    /**
     * Finds specified user.
     *
     * @param   string $username Login.
     * @param   bool   $ldap     Whether it's a LDAP user.
     *
     * @return  User|null Found user.
     */
    protected function findUser($username, $ldap = false)
    {
        return $this->doctrine->getRepository(User::class)->findOneBy([
            'username' => $ldap ? $username : $username . '@eTraxis',
            'isLdap'   => $ldap ? 1 : 0,
        ]);
    }

    /**
     * Emulates authentication of specified user.
     *
     * @param   string $username Login.
     * @param   bool   $ldap     Whether it's a LDAP user.
     *
     * @return  bool Whether user was authenticated.
     */
    protected function loginAs($username, $ldap = false)
    {
        if ($user = $this->findUser($username, $ldap)) {

            $token = new UsernamePasswordToken($user, null, 'etraxis_provider', $user->getRoles());
            $this->client->getContainer()->get('security.token_storage')->setToken($token);

            $this->session->set('_security_default', serialize($token));
            $this->session->save();

            $cookie = new Cookie($this->session->getName(), $this->session->getId());
            $this->client->getCookieJar()->set($cookie);

            return true;
        }

        return false;
    }
}
