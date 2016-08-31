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

use eTraxis\Entity\Record;
use eTraxis\Tests\TransactionalTestCase;

class PostponeCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        $this->loginAs('hermes');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $events = count($record->getHistory());

        self::assertFalse($record->isPostponed());

        $command = new PostponeCommand([
            'record' => $record->getId(),
        ]);

        $this->commandbus->handle($command);

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        self::assertTrue($record->isPostponed());
        self::assertCount($events + 1, $record->getHistory());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown user.
     */
    public function testUnknownUser()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $command = new PostponeCommand([
            'record' => $record->getId(),
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown record.
     */
    public function testUnknownRecord()
    {
        $this->loginAs('hermes');

        $command = new PostponeCommand([
            'record' => self::UNKNOWN_ENTITY_ID,
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testForbidden()
    {
        $this->loginAs('zoidberg');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $command = new PostponeCommand([
            'record' => $record->getId(),
        ]);

        $this->commandbus->handle($command);
    }
}
