<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @param   array   $extra   Optional extra items to be added to the resulted array.
     *                           In case of keys conflicts this array overrides submitted data.
     *
     * @return  array Submitted data.
     *
     * @throws  BadRequestHttpException
     */
    protected function getFormData(Request $request, $name = 'form', $extra = [])
    {
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $this->container->get('logger');

        $data = $request->getMethod() == Request::METHOD_GET
            ? $request->query->all()
            : $request->request->all();

        if (!array_key_exists($name, $data)) {
            $logger->error('No data submitted.', [$name]);
            throw new BadRequestHttpException('No data submitted.');
        }

        if ($request->getMethod() == Request::METHOD_POST) {
            if (!array_key_exists('_token', $data[$name])) {
                $logger->error('CSRF token is missing.');
                throw new BadRequestHttpException('CSRF token is missing.');
            }

            /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrf */
            $csrf  = $this->container->get('security.csrf.token_manager');
            $token = $csrf->getToken($name);

            if ($data[$name]['_token'] !== $token->getValue()) {
                $logger->error('Invalid CSRF token.');
                throw new BadRequestHttpException('Invalid CSRF token.');
            }
        }

        return $this->empty2null($extra + $data[$name]);
    }

    /**
     * Sets information message to be displayed on the next page.
     *
     * @param   string $notice Message.
     */
    protected function setNotice($notice)
    {
        /** @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface $flashBag */
        $flashBag = $this->container->get('session')->getFlashBag();
        $flashBag->add('notice', $notice);
    }

    /**
     * Sets error message to be displayed on the next page.
     *
     * @param   string $error Message.
     */
    protected function setError($error)
    {
        /** @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface $flashBag */
        $flashBag = $this->container->get('session')->getFlashBag();
        $flashBag->add('error', $error);
    }

    /**
     * Shortcut to get the Translator service.
     *
     * @return  \Symfony\Component\Translation\TranslatorInterface
     */
    protected function getTranslator()
    {
        return $this->container->get('translator');
    }

    /**
     * Shortcut to get the Command Bus service.
     *
     * @return  \SimpleBus\Message\Bus\MessageBus
     */
    protected function getCommandBus()
    {
        return $this->container->get('command_bus');
    }

    /**
     * Shortcut to get the Event Bus service.
     *
     * @return  \SimpleBus\Message\Bus\MessageBus
     */
    protected function getEventBus()
    {
        return $this->container->get('event_bus');
    }

    /**
     * Shortcut to get the DataTables service.
     *
     * @return  \DataTables\DataTablesInterface
     */
    protected function getDataTables()
    {
        return $this->container->get('datatables');
    }

    /**
     * Replaces empty strings with nulls.
     *
     * @param   mixed $value A value to be updated. Can be an array.
     *
     * @return  mixed Updated value.
     */
    private function empty2null($value)
    {
        if (is_array($value)) {
            foreach ($value as &$v) {
                $v = $this->empty2null($v);
            }

            return $value;
        }

        return strlen($value) == 0 ? null : $value;
    }
}
