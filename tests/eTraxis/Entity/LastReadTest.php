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

use eTraxis\Tests\TransactionalTestCase;
use eTraxis\Traits\ReflectionTrait;

class LastReadTest extends TransactionalTestCase
{
    use ReflectionTrait;

    public function testConstruct()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Prizes for the claw crane']);
        $user   = $this->findUser('bender');

        $lastRead = new LastRead($record, $user);

        self::assertEquals($record, $this->getProperty($lastRead, 'record'));
        self::assertEquals($user, $this->getProperty($lastRead, 'user'));
        self::assertLessThanOrEqual(1, time() - $this->getProperty($lastRead, 'readAt'));
    }
}
