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

namespace eTraxis\Traits;

use eTraxis\Exception\CommandException;
use Symfony\Component\HttpFoundation\Request;

/**
 * A trait to extend standard controller class.
 *
 * @property \Symfony\Component\DependencyInjection\ContainerInterface $container
 */
trait ContainerTrait
{
    /**
     * Returns submitted data of specified CSRF-protected form.
     * A form can be submitted via GET or POST.
     * CSRF token is verified automatically; in case of failure an exception is raised.
     *
     * @param   Request $request Current request.
     * @param   string  $name    Form name.
     *
     * @return  array Submitted data.
     *
     * @throws  CommandException
     */
    public function getFormData(Request $request, $name = 'form')
    {
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $this->container->get('logger');

        $data = $request->getMethod() == Request::METHOD_GET
            ? $request->query->all()
            : $request->request->all();

        if (!array_key_exists($name, $data)) {
            $logger->error('No data submitted.', [$name]);
            throw new CommandException('No data submitted.');
        }

        if (!array_key_exists('_token', $data[$name])) {
            $logger->error('CSRF token is missing.');
            throw new CommandException('CSRF token is missing.');
        }

        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrf */
        $csrf  = $this->container->get('security.csrf.token_manager');
        $token = $csrf->getToken($name);

        if ($data[$name]['_token'] !== $token->getValue()) {
            $logger->error('Invalid CSRF token.');
            throw new CommandException('Invalid CSRF token.');
        }

        return $data[$name];
    }

    /**
     * Shortcut to get the Command Bus service.
     *
     * @return  \SimpleBus\Message\Bus\MessageBus
     */
    public function getCommandBus()
    {
        return $this->container->get('command_bus');
    }
}
