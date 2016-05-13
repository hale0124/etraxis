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

use SimpleBus\Message\Bus\MessageBus;

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
     * @return  MessageBus
     */
    protected function getCommandBus(): MessageBus
    {
        return $this->container->get('command_bus');
    }

    /**
     * Shortcut to get the Event Bus service.
     *
     * @return  MessageBus
     */
    protected function getEventBus(): MessageBus
    {
        return $this->container->get('event_bus');
    }
}
