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
     * Checks if the attributes are granted against the current authentication token and optionally supplied object.
     *
     * @param   mixed $attributes
     * @param   mixed $object
     *
     * @return  bool
     */
    protected function isGranted($attributes, $object = null)
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->container->get('security.authorization_checker');

        return $authChecker->isGranted($attributes, $object);
    }

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
