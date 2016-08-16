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

class AddCommentCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'e-Waste']);

        $this->loginAs('hermes');

        $total = count($record->getComments());

        $command = new AddCommentCommand([
            'record'  => $record->getId(),
            'text'    => 'Test comment.',
            'private' => false,
        ]);

        $this->commandbus->handle($command);

        self::assertCount($total + 1, $record->getComments());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown user.
     */
    public function testUnknownUser()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'e-Waste']);

        $command = new AddCommentCommand([
            'record'  => $record->getId(),
            'text'    => 'Test comment.',
            'private' => false,
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

        $command = new AddCommentCommand([
            'record'  => self::UNKNOWN_ENTITY_ID,
            'text'    => 'Test comment.',
            'private' => false,
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testForbiddenByPermissions()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'e-Waste']);

        $this->loginAs('zoidberg');

        $command = new AddCommentCommand([
            'record'  => $record->getId(),
            'text'    => 'Test comment.',
            'private' => false,
        ]);

        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testForbiddenByFrozen()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Prizes for the claw crane']);

        $this->loginAs('hermes');

        $command = new AddCommentCommand([
            'record'  => $record->getId(),
            'text'    => 'Test comment.',
            'private' => false,
        ]);

        $this->commandbus->handle($command);
    }
}
