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

use eTraxis\Entity\Event;
use eTraxis\Entity\FieldValue;
use eTraxis\Entity\Record;
use eTraxis\Tests\TransactionalTestCase;

class DeleteCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        $this->loginAs('hermes');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        self::assertNotNull($record);

        $events = count($this->doctrine->getRepository(Event::class)->findAll());
        $values = count($this->doctrine->getRepository(FieldValue::class)->findAll());

        $command = new DeleteCommand([
            'record' => $record->getId(),
        ]);

        $this->commandbus->handle($command);

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        self::assertNull($record);

        self::assertCount($events - 2, $this->doctrine->getRepository(Event::class)->findAll());
        self::assertCount($values - 4, $this->doctrine->getRepository(FieldValue::class)->findAll());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown record.
     */
    public function testUnknownRecord()
    {
        $this->loginAs('hermes');

        $command = new DeleteCommand([
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

        $command = new DeleteCommand([
            'record' => $record->getId(),
        ]);

        $this->commandbus->handle($command);
    }
}
