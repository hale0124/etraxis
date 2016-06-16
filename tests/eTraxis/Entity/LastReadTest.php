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
use eTraxis\Tests\TransactionalTestCase;

class LastReadTest extends TransactionalTestCase
{
    public function testConstruct()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Prizes for the claw crane']);
        $user   = $this->findUser('bender');

        $lastRead = new LastRead($record, $user);

        /** @var \StdClass $object */
        $object = AltrEgo::create($lastRead);

        self::assertEquals($record, $object->record);
        self::assertEquals($user, $object->user);
        self::assertLessThanOrEqual(1, time() - $object->readAt);
    }
}
