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

use eTraxis\Entity\User;
use eTraxis\Tests\TransactionalTestCase;
use eTraxis\Traits\ReflectionTrait;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class UserVoterTest extends TransactionalTestCase
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

        $hubert = $this->findUser('hubert');

        self::assertFalse($this->security->isGranted('UNKNOWN', $hubert));
    }

    public function testAnonymous()
    {
        $scruffy = $this->findUser('scruffy');

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $voter = new UserVoter($manager);
        $token = new AnonymousToken('', 'anon.');

        self::assertEquals(UserVoter::ACCESS_DENIED, $voter->vote($token, $scruffy, [UserVoter::DELETE, UserVoter::DISABLE]));
    }

    public function testSetExpiredPassword()
    {
        $this->loginAs('hubert');

        /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage $storage */
        $storage = $this->client->getContainer()->get('security.token_storage');
        $token   = $storage->getToken();

        $hubert = $this->findUser('hubert');

        self::assertFalse($this->security->isGranted(UserVoter::SET_EXPIRED_PASSWORD, $hubert));

        $this->setProperty($hubert, 'passwordExpiresAt', time() - 60);

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $voter = new UserVoter($manager, 1);
        self::assertEquals(UserVoter::ACCESS_GRANTED, $voter->vote($token, $hubert, [UserVoter::SET_EXPIRED_PASSWORD]));

        $voter = new UserVoter($manager);
        self::assertEquals(UserVoter::ACCESS_DENIED, $voter->vote($token, $hubert, [UserVoter::SET_EXPIRED_PASSWORD]));
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

        self::assertFalse($this->security->isGranted(UserVoter::DELETE, $hubert));
        self::assertFalse($this->security->isGranted(UserVoter::DELETE, $leela));
        self::assertTrue($this->security->isGranted(UserVoter::DELETE, $scruffy));
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

        self::assertFalse($this->security->isGranted(UserVoter::DISABLE, $hubert));
        self::assertFalse($this->security->isGranted(UserVoter::DISABLE, $francine));
        self::assertTrue($this->security->isGranted(UserVoter::DISABLE, $scruffy));
    }

    public function testEnable()
    {
        $this->loginAs('hubert');

        $hubert   = $this->findUser('hubert');
        $francine = $this->findUser('francine');

        self::assertInstanceOf(User::class, $hubert);
        self::assertInstanceOf(User::class, $francine);

        self::assertFalse($this->security->isGranted(UserVoter::ENABLE, $hubert));
        self::assertTrue($this->security->isGranted(UserVoter::ENABLE, $francine));
    }

    public function testUnlock()
    {
        $this->loginAs('hubert');

        $hubert = $this->findUser('hubert');
        $bender = $this->findUser('bender');

        self::assertInstanceOf(User::class, $hubert);
        self::assertInstanceOf(User::class, $bender);

        $auth_attempts = $this->client->getContainer()->getParameter('security_auth_attempts');
        $lock_time     = $this->client->getContainer()->getParameter('security_lock_time');

        do {} while(!$bender->lock($auth_attempts, $lock_time));

        self::assertTrue($this->security->isGranted(UserVoter::UNLOCK, $bender));
        self::assertFalse($this->security->isGranted(UserVoter::UNLOCK, $hubert));
    }
}
