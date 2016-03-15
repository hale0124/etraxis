<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Forces check of CSRF token for POST AJAX requests.
 */
class CsrfToken
{
    protected $logger;
    protected $tokens;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface           $logger
     * @param   CsrfTokenManagerInterface $tokens
     */
    public function __construct(LoggerInterface $logger, CsrfTokenManagerInterface $tokens)
    {
        $this->logger = $logger;
        $this->tokens = $tokens;
    }

    /**
     * Checks submitted POST data for valid CSRF token.
     *
     * @param   GetResponseEvent $event
     */
    public function checkCsrfToken(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->isXmlHttpRequest() && $request->getMethod() ===  Request::METHOD_POST) {

            $data = $request->request->all();

            $keys = array_keys($data);
            $name = reset($keys);

            // Check whether it is a submitted form.
            if (count($data) === 1 && is_array($data[$name])) {
                $data = $data[$name];
            }
            else {
                $name = '';
            }

            if (!array_key_exists('_token', $data)) {
                $this->logger->error('CSRF token is missing.');
                $event->setResponse(new Response('CSRF token is missing.', Response::HTTP_BAD_REQUEST));
            }
            else {
                $token = $this->tokens->getToken($name);

                if ($data['_token'] !== $token->getValue()) {
                    $this->logger->error('Invalid CSRF token.');
                    $event->setResponse(new Response('Invalid CSRF token.', Response::HTTP_BAD_REQUEST));
                }
            }
        }
    }
}
