<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Voter;

use eTraxis\CommandBus\Projects\UpdateProjectCommand;
use eTraxis\CommandBus\Templates\LockTemplateCommand;
use eTraxis\Entity\Record;
use eTraxis\Tests\TransactionalTestCase;
use eTraxis\Traits\ReflectionTrait;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class RecordVoterTest extends TransactionalTestCase
{
    use ReflectionTrait;

    /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker */
    private $security;

    protected function setUp()
    {
        parent::setUp();

        $this->security = $this->client->getContainer()->get('security.authorization_checker');
    }

    public function testUnsupportedAttribute()
    {
        $this->loginAs('hubert');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        self::assertFalse($this->security->isGranted('UNKNOWN', $record));
    }

    public function testAnonymous()
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $voter = new RecordVoter($manager);
        $token = new AnonymousToken('', 'anon.');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Space Pilot 3000',
        ]);

        self::assertEquals(RecordVoter::ACCESS_DENIED, $voter->vote($token, $record, [RecordVoter::VIEW]));
        self::assertEquals(RecordVoter::ACCESS_DENIED, $voter->vote($token, $record, [RecordVoter::PUBLIC_COMMENT]));
        self::assertEquals(RecordVoter::ACCESS_DENIED, $voter->vote($token, $record, [RecordVoter::PRIVATE_COMMENT]));
    }

    public function testByAnyone()
    {
        $this->loginAs('mwop');

        /** @var Record $granted */
        $granted = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Space Pilot 3000',
        ]);

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $granted));
        self::assertTrue($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $granted));
        self::assertTrue($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $granted));

        /** @var Record $forbidden */
        $forbidden = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => '200 feet of hanging rope for the hanging of multiheaded monster.',
        ]);

        self::assertFalse($this->security->isGranted(RecordVoter::VIEW, $forbidden));
        self::assertFalse($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $forbidden));
        self::assertFalse($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $forbidden));
    }

    public function testByAuthor()
    {
        $this->loginAs('pmjones');

        /** @var Record $granted */
        $granted = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'A statue commemorating the loss of the first Planet Express crew',
        ]);

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $granted));
        self::assertTrue($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $granted));
        self::assertTrue($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $granted));

        /** @var Record $forbidden */
        $forbidden = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => '200 feet of hanging rope for the hanging of multiheaded monster.',
        ]);

        self::assertFalse($this->security->isGranted(RecordVoter::VIEW, $forbidden));
        self::assertFalse($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $forbidden));
        self::assertFalse($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $forbidden));
    }

    public function testByResponsible()
    {
        $this->loginAs('artem');

        /** @var Record $granted */
        $granted = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => '200 feet of hanging rope for the hanging of multiheaded monster.',
        ]);

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $granted));
        self::assertTrue($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $granted));
        self::assertTrue($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $granted));

        /** @var Record $forbidden */
        $forbidden = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'A statue commemorating the loss of the first Planet Express crew',
        ]);

        self::assertFalse($this->security->isGranted(RecordVoter::VIEW, $forbidden));
        self::assertFalse($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $forbidden));
        self::assertFalse($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $forbidden));
    }

    public function testByGroup()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $this->loginAs('hermes');

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $record));
        self::assertTrue($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $record));
        self::assertTrue($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $record));

        $this->loginAs('zoidberg');

        self::assertFalse($this->security->isGranted(RecordVoter::VIEW, $record));
        self::assertFalse($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $record));
        self::assertFalse($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $record));
    }

    public function testPostponedRecord()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'A soufflé laced with nitroglycerine',
        ]);

        $this->loginAs('hermes');

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $record));
        self::assertTrue($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $record));
        self::assertTrue($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $record));
    }

    public function testFrozenRecord()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Prizes for the claw crane',
        ]);

        $this->loginAs('hermes');

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $record));
        self::assertFalse($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $record));
        self::assertFalse($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $record));

        // fake it's not frozen
        $this->setProperty($record, 'closedAt', time() - 86400);

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $record));
        self::assertTrue($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $record));
        self::assertTrue($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $record));
    }

    public function testLockedTemplate()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $this->loginAs('hermes');

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $record));
        self::assertTrue($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $record));
        self::assertTrue($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $record));

        $command = new LockTemplateCommand([
            'id' => $record->getTemplate()->getId(),
        ]);

        $this->commandbus->handle($command);

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $record));
        self::assertFalse($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $record));
        self::assertFalse($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $record));
    }

    public function testSuspendedProject()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $this->loginAs('hermes');

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $record));
        self::assertTrue($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $record));
        self::assertTrue($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $record));

        $command = new UpdateProjectCommand([
            'id'        => $record->getProject()->getId(),
            'name'      => $record->getProject()->getName(),
            'suspended' => true,
        ]);

        $this->commandbus->handle($command);

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $record));
        self::assertFalse($this->security->isGranted(RecordVoter::PUBLIC_COMMENT, $record));
        self::assertFalse($this->security->isGranted(RecordVoter::PRIVATE_COMMENT, $record));
    }
}
