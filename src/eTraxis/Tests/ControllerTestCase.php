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

use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Entity\User;
use eTraxis\Security\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Base test case with container shortcuts and security helpers.
 */
class ControllerTestCase extends WebTestCase
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

    /** @var \League\Tactician\CommandBus */
    protected $commandbus;

    /**
     * Prepares shortcuts for most popular container services.
     */
    protected function setUp()
    {
        $this->client = static::createClient();

        $this->logger     = $this->client->getContainer()->get('logger');
        $this->router     = $this->client->getContainer()->get('router');
        $this->session    = $this->client->getContainer()->get('session');
        $this->validator  = $this->client->getContainer()->get('validator');
        $this->translator = $this->client->getContainer()->get('translator');
        $this->commandbus = $this->client->getContainer()->get('tactician.commandbus');
    }

    /**
     * Makes request to specified URI.
     *
     * @param   string $method
     * @param   string $uri
     * @param   bool   $isXmlHttpRequest
     */
    protected function makeRequest(string $method, string $uri, bool $isXmlHttpRequest = false)
    {
        $headers = $isXmlHttpRequest ? ['HTTP_X-Requested-With' => 'XMLHttpRequest'] : [];

        $this->client->request($method, $uri, [], [], $headers);
    }

    /**
     * Asserts that HTTP status code of the last request equals to the specified one.
     *
     * @param   int $statusCode
     */
    protected function assertStatusCode(int $statusCode)
    {
        self::assertEquals($statusCode, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Asserts that "Location" HTTP header of the last request equals to the specified one.
     *
     * @param   string $location
     */
    protected function assertLocationHeader(string $location)
    {
        self::assertEquals($location, $this->client->getResponse()->headers->get('Location'));
    }

    /**
     * Asserts that user was redirected to the login page after the last request.
     */
    protected function assertLoginPage()
    {
        $urls = [
            $this->router->generate('login', [], UrlGeneratorInterface::ABSOLUTE_PATH),
            $this->router->generate('login', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $response = $this->client->getResponse();

        self::assertTrue($response->isRedirection() && in_array($response->headers->get('Location'), $urls));
    }

    /**
     * Finds specified user.
     *
     * @param   string $username Login.
     * @param   string $provider Authentication provider.
     *
     * @return  User|null Found user.
     */
    protected function findUser(string $username, string $provider = AuthenticationProvider::ETRAXIS)
    {
        /** @var \Symfony\Bridge\Doctrine\RegistryInterface $doctrine */
        $doctrine = $this->client->getContainer()->get('doctrine');

        return $doctrine->getRepository(User::class)->findOneBy([
            'username' => $username,
            'provider' => $provider,
        ]);
    }

    /**
     * Emulates authentication of specified user.
     *
     * @param   string $username Login.
     * @param   string $provider Authentication provider.
     *
     * @return  bool Whether user was authenticated.
     */
    protected function loginAs(string $username, string $provider = AuthenticationProvider::ETRAXIS)
    {
        if ($user = $this->findUser($username, $provider)) {

            $current = new CurrentUser($user);

            $token = new UsernamePasswordToken($current, null, 'etraxis_provider', $current->getRoles());
            $this->client->getContainer()->get('security.token_storage')->setToken($token);

            $this->session->set('_security_main', serialize($token));
            $this->session->save();

            $cookie = new Cookie($this->session->getName(), $this->session->getId());
            $this->client->getCookieJar()->set($cookie);

            return true;
        }

        return false;
    }
}
