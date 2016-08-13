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

use eTraxis\CommandBus\Middleware\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Handles any unhandled exception.
 */
class UnhandledException
{
    protected $logger;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Logs the exception and (in case of AJAX) converts it into JSON response with HTTP error.
     *
     * @param   GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        $request   = $event->getRequest();
        $exception = $event->getException();

        if ($request->isXmlHttpRequest()) {

            if ($exception instanceof ValidationException) {
                $this->logger->error('Validation exception', $exception->toArray());
                $response = new JsonResponse($exception->toArray(), $exception->getStatusCode());
            }
            elseif ($exception instanceof HttpException) {
                $message = $exception->getMessage() ?: Response::$statusTexts[$exception->getStatusCode()];
                $this->logger->error('HTTP exception', [$message]);
                $response = new JsonResponse($message, $exception->getStatusCode());
            }
            else {
                $this->logger->error('Exception', [$exception->getMessage()]);
                $response = new JsonResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
            }

            $event->setResponse($response);
        }
    }
}
