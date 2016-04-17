<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Entity listener.
 */
class EntityListener
{
    public function postLoad(Entity $entity, LifecycleEventArgs $event)
    {
        $entity->setEntityManager($event->getEntityManager());
    }
}
