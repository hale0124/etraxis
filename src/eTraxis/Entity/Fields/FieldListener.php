<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Fields;

use Doctrine\ORM\Event\LifecycleEventArgs;
use eTraxis\Entity\Field;

/**
 * Entity listener.
 */
class FieldListener
{
    public function postLoad(Field $field, LifecycleEventArgs $event)
    {
        $field->injectDependencies($event->getEntityManager());
    }
}
