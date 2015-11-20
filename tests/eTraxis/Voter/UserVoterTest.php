<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Voter;

use eTraxis\Entity\User;
use eTraxis\Tests\BaseTestCase;
use eTraxis\Traits\ClassAccessTrait;

/**
 * @method getSupportedClasses()
 * @method getSupportedAttributes()
 * @method isGranted($attribute, $object, $user = null);
 */
class UserVoterStub extends UserVoter
{
    use ClassAccessTrait;
}

class UserVoterTest extends BaseTestCase
{
    /** @var UserVoterStub */
    private $object = null;

    protected function setUp()
    {
        parent::setUp();

        /** @var \eTraxis\Repository\EventsRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:Event');

        $this->object = new UserVoterStub($repository);
    }

    public function testGetSupportedClasses()
    {
        $user = $this->findUser('artem');

        $expected = [
            get_class($user),
        ];

        $this->assertEquals($expected, $this->object->getSupportedClasses());
    }

    public function testGetSupportedAttributes()
    {
        $expected = [
            User::SET_EXPIRED_PASSWORD,
            User::DELETE,
            User::DISABLE,
            User::ENABLE,
            User::UNLOCK,
        ];

        $this->assertEquals($expected, $this->object->getSupportedAttributes());
    }

    public function testUnsupportedAttribute()
    {
        $hubert = $this->findUser('hubert');

        $this->assertFalse($this->object->isGranted('UNKNOWN', $hubert));
    }

    public function testSetExpiredPassword()
    {
        $hubert = $this->findUser('hubert');

        $this->assertFalse($this->object->isGranted(User::SET_EXPIRED_PASSWORD, $hubert));

        $hubert->setPasswordSetAt(time() - 86400 * 2);

        /** @var \eTraxis\Repository\EventsRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:Event');

        $this->object = new UserVoterStub($repository, 3);
        $this->assertFalse($this->object->isGranted(User::SET_EXPIRED_PASSWORD, $hubert));

        $this->object = new UserVoterStub($repository, 1);
        $this->assertTrue($this->object->isGranted(User::SET_EXPIRED_PASSWORD, $hubert));
    }

    public function testDelete()
    {
        $hubert  = $this->findUser('hubert');
        $leela   = $this->findUser('leela');
        $scruffy = $this->findUser('scruffy');

        $this->assertInstanceOf('eTraxis\Entity\User', $hubert);
        $this->assertInstanceOf('eTraxis\Entity\User', $leela);
        $this->assertInstanceOf('eTraxis\Entity\User', $scruffy);

        $this->assertFalse($this->object->isGranted(User::DELETE, $leela, $hubert));
        $this->assertTrue($this->object->isGranted(User::DELETE, $scruffy, $hubert));
        $this->assertFalse($this->object->isGranted(User::DELETE, $scruffy, $scruffy));
        $this->assertFalse($this->object->isGranted(User::DELETE, $scruffy));
    }

    public function testDisable()
    {
        $hubert   = $this->findUser('hubert');
        $bender   = $this->findUser('bender');
        $francine = $this->findUser('francine');

        $this->assertInstanceOf('eTraxis\Entity\User', $hubert);
        $this->assertInstanceOf('eTraxis\Entity\User', $bender);
        $this->assertInstanceOf('eTraxis\Entity\User', $francine);

        $this->assertFalse($this->object->isGranted(User::DISABLE, $francine, $hubert));
        $this->assertTrue($this->object->isGranted(User::DISABLE, $bender, $hubert));
        $this->assertFalse($this->object->isGranted(User::DISABLE, $bender, $bender));
        $this->assertFalse($this->object->isGranted(User::DISABLE, $bender));
    }

    public function testEnable()
    {
        $hubert   = $this->findUser('hubert');
        $francine = $this->findUser('francine');

        $this->assertInstanceOf('eTraxis\Entity\User', $hubert);
        $this->assertInstanceOf('eTraxis\Entity\User', $francine);

        $this->assertFalse($this->object->isGranted(User::ENABLE, $hubert));
        $this->assertTrue($this->object->isGranted(User::ENABLE, $francine));
    }

    public function testUnlock()
    {
        $hubert = $this->findUser('hubert');
        $bender = $this->findUser('bender');

        $this->assertInstanceOf('eTraxis\Entity\User', $hubert);
        $this->assertInstanceOf('eTraxis\Entity\User', $bender);

        $bender->setAuthAttempts(3);
        $bender->setLockedUntil(time() + 60);

        $this->assertTrue($this->object->isGranted(User::UNLOCK, $bender));
        $this->assertFalse($this->object->isGranted(User::UNLOCK, $hubert));
    }
}
