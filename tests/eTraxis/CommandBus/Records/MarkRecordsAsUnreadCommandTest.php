<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Records;

use eTraxis\Entity\LastRead;
use eTraxis\Entity\Record;
use eTraxis\Tests\TransactionalTestCase;

class MarkRecordsAsUnreadCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        $user = $this->findUser('bender');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Lug nuts']);

        $result = $this->doctrine->getRepository(LastRead::class)->findBy([
            'record' => $record,
            'user'   => $user,
        ]);

        self::assertCount(1, $result);

        $command = new MarkRecordsAsUnreadCommand([
            'user'    => $user->getId(),
            'records' => [$record->getId()],
        ]);

        $this->commandbus->handle($command);

        $result = $this->doctrine->getRepository(LastRead::class)->findBy([
            'record' => $record,
            'user'   => $user,
        ]);

        self::assertCount(0, $result);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown user.
     */
    public function testUnknownUser()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Lug nuts']);

        $command = new MarkRecordsAsUnreadCommand([
            'user'    => self::UNKNOWN_ENTITY_ID,
            'records' => [$record->getId()],
        ]);

        $this->commandbus->handle($command);
    }
}
