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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use eTraxis\Tests\TransactionalTestCase;
use eTraxis\Traits\ReflectionTrait;

class EntityListenerTest extends TransactionalTestCase
{
    use ReflectionTrait;

    public function testPostLoad()
    {
        $listener = new EntityListener();

        $entity = new Entity();

        $event = new LifecycleEventArgs($entity, $this->doctrine->getManager());

        self::assertNull($this->getProperty($entity, 'manager'));
        $listener->postLoad($entity, $event);
        self::assertNotNull($this->getProperty($entity, 'manager'));
        self::assertInstanceOf(EntityManagerInterface::class, $this->getProperty($entity, 'manager'));
    }
}
