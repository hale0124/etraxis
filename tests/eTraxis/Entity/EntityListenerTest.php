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

use AltrEgo\AltrEgo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use eTraxis\Tests\TransactionalTestCase;

class EntityListenerTest extends TransactionalTestCase
{
    public function testPostLoad()
    {
        $listener = new EntityListener();

        $entity = new Entity();

        /** @var \StdClass $object */
        $object = AltrEgo::create($entity);

        $event = new LifecycleEventArgs($entity, $this->doctrine->getManager());

        self::assertNull($object->manager);
        $listener->postLoad($entity, $event);
        self::assertNotNull($object->manager);
        self::assertInstanceOf(EntityManagerInterface::class, $object->manager);
    }
}
