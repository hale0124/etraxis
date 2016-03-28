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

use eTraxis\Entity\Event;
use eTraxis\Entity\User;
use eTraxis\Tests\BaseTestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class UserVoterTest extends BaseTestCase
{
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

        $hubert = $this->findUser('hubert');

        self::assertFalse($this->security->isGranted('UNKNOWN', $hubert));
    }

    public function testAnonymous()
    {
        $scruffy = $this->findUser('scruffy');

        /** @var \eTraxis\Repository\EventsRepository $repository */
        $repository = $this->doctrine->getRepository(Event::class);

        $voter = new UserVoter($repository);
        $token = new AnonymousToken('', 'anon.');

        self::assertEquals(UserVoter::ACCESS_DENIED, $voter->vote($token, $scruffy, [User::DELETE, User::DISABLE]));
    }

    public function testSetExpiredPassword()
    {
        $this->loginAs('hubert');

        /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage $storage */
        $storage = $this->client->getContainer()->get('security.token_storage');
        $token   = $storage->getToken();

        $hubert = $this->findUser('hubert');

        self::assertFalse($this->security->isGranted(User::SET_EXPIRED_PASSWORD, $hubert));

        $hubert->setPasswordSetAt(time() - 86400 * 2);

        /** @var \eTraxis\Repository\EventsRepository $repository */
        $repository = $this->doctrine->getRepository(Event::class);

        $voter = new UserVoter($repository, 3);
        self::assertEquals(UserVoter::ACCESS_DENIED, $voter->vote($token, $hubert, [User::SET_EXPIRED_PASSWORD]));

        $voter = new UserVoter($repository, 1);
        self::assertEquals(UserVoter::ACCESS_GRANTED, $voter->vote($token, $hubert, [User::SET_EXPIRED_PASSWORD]));
    }

    public function testDelete()
    {
        $this->loginAs('hubert');

        $hubert  = $this->findUser('hubert');
        $leela   = $this->findUser('leela');
        $scruffy = $this->findUser('scruffy');

        self::assertInstanceOf(User::class, $hubert);
        self::assertInstanceOf(User::class, $leela);
        self::assertInstanceOf(User::class, $scruffy);

        self::assertFalse($this->security->isGranted(User::DELETE, $hubert));
        self::assertFalse($this->security->isGranted(User::DELETE, $leela));
        self::assertTrue($this->security->isGranted(User::DELETE, $scruffy));
    }

    public function testDisable()
    {
        $this->loginAs('hubert');

        $hubert   = $this->findUser('hubert');
        $francine = $this->findUser('francine');
        $scruffy  = $this->findUser('scruffy');

        self::assertInstanceOf(User::class, $hubert);
        self::assertInstanceOf(User::class, $francine);
        self::assertInstanceOf(User::class, $scruffy);

        self::assertFalse($this->security->isGranted(User::DISABLE, $hubert));
        self::assertFalse($this->security->isGranted(User::DISABLE, $francine));
        self::assertTrue($this->security->isGranted(User::DISABLE, $scruffy));
    }

    public function testEnable()
    {
        $this->loginAs('hubert');

        $hubert   = $this->findUser('hubert');
        $francine = $this->findUser('francine');

        self::assertInstanceOf(User::class, $hubert);
        self::assertInstanceOf(User::class, $francine);

        self::assertFalse($this->security->isGranted(User::ENABLE, $hubert));
        self::assertTrue($this->security->isGranted(User::ENABLE, $francine));
    }

    public function testUnlock()
    {
        $this->loginAs('hubert');

        $hubert = $this->findUser('hubert');
        $bender = $this->findUser('bender');

        self::assertInstanceOf(User::class, $hubert);
        self::assertInstanceOf(User::class, $bender);

        $bender->setAuthAttempts(3);
        $bender->setLockedUntil(time() + 60);

        self::assertTrue($this->security->isGranted(User::UNLOCK, $bender));
        self::assertFalse($this->security->isGranted(User::UNLOCK, $hubert));
    }
}
