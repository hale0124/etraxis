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
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Prizes for the claw crane']);

        self::assertFalse($this->security->isGranted('UNKNOWN', $record));
    }

    public function testAnonymous()
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $voter = new RecordVoter($manager);
        $token = new AnonymousToken('', 'anon.');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Prizes for the claw crane']);

        self::assertEquals(RecordVoter::ACCESS_DENIED, $voter->vote($token, $record, [RecordVoter::VIEW]));
    }

    public function testViewByAuthor()
    {
        $this->loginAs('hubert');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'e-Waste']);

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $record));
    }

    public function testViewByResponsible()
    {
        $this->loginAs('leela');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'e-Waste']);

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $record));
    }

    public function testViewByAnyone()
    {
        $this->loginAs('mwop');

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Space Pilot 3000']);

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $record));
    }

    public function testViewByGroup()
    {
        $this->loginAs('hubert');

        /** @var Record $granted */
        $granted = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Prizes for the claw crane']);

        /** @var Record $forbidden */
        $forbidden = $this->doctrine->getRepository(Record::class)->findOneBy(['subject' => 'Basic Coding Standard']);

        self::assertTrue($this->security->isGranted(RecordVoter::VIEW, $granted));
        self::assertFalse($this->security->isGranted(RecordVoter::VIEW, $forbidden));
    }
}
