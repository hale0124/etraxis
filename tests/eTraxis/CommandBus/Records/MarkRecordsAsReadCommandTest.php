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

class MarkRecordsAsReadCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        $this->loginAs('bender');

        $user = $this->findUser('bender');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Prizes for the claw crane']);

        $result = $this->doctrine->getRepository(LastRead::class)->findBy([
            'record' => $record,
            'user'   => $user,
        ]);

        self::assertCount(0, $result);

        $command = new MarkRecordsAsReadCommand([
            'records' => [$record->getId()],
        ]);

        $this->commandbus->handle($command);

        $result = $this->doctrine->getRepository(LastRead::class)->findBy([
            'record' => $record,
            'user'   => $user,
        ]);

        self::assertCount(1, $result);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown user.
     */
    public function testUnknownUser()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Prizes for the claw crane']);

        $command = new MarkRecordsAsReadCommand([
            'records' => [$record->getId()],
        ]);

        $this->commandbus->handle($command);
    }
}
