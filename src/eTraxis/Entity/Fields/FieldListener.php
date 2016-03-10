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
use eTraxis\Entity\DecimalValue;
use eTraxis\Entity\Field;
use eTraxis\Entity\ListItem;
use eTraxis\Entity\StringValue;
use eTraxis\Entity\TextValue;

/**
 * Entity listener.
 */
class FieldListener
{
    public function postLoad(Field $field, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();

        /** @noinspection PhpParamsInspection */
        $field
            ->setDecimalValuesRepository($em->getRepository(DecimalValue::class))
            ->setStringValuesRepository($em->getRepository(StringValue::class))
            ->setTextValuesRepository($em->getRepository(TextValue::class))
            ->setListItemsRepository($em->getRepository(ListItem::class))
        ;
    }
}
