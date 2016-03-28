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
