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

class AssignCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        $this->loginAs('hermes');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $events = count($record->getHistory());

        self::assertEquals('leela', $record->getResponsible()->getUsername());

        $command = new AssignCommand([
            'record'      => $record->getId(),
            'responsible' => $this->findUser('fry')->getId(),
        ]);

        $this->commandbus->handle($command);

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        self::assertEquals('fry', $record->getResponsible()->getUsername());
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

        $command = new AssignCommand([
            'record'      => $record->getId(),
            'responsible' => $this->findUser('fry')->getId(),
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

        $command = new AssignCommand([
            'record'      => self::UNKNOWN_ENTITY_ID,
            'responsible' => $this->findUser('fry')->getId(),
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown responsible.
     */
    public function testUnknownResponsible()
    {
        $this->loginAs('hermes');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $command = new AssignCommand([
            'record'      => $record->getId(),
            'responsible' => self::UNKNOWN_ENTITY_ID,
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

        $command = new AssignCommand([
            'record'      => $record->getId(),
            'responsible' => $this->findUser('fry')->getId(),
        ]);

        $this->commandbus->handle($command);
    }
}
