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

/**
 * A trait to extend standard controller class.
 *
 * @property \Symfony\Component\DependencyInjection\ContainerInterface $container
 */
trait ContainerTrait
{
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
}
