<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Voter;

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

        $this->object = new UserVoterStub($this->doctrine);
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
            UserVoter::SET_EXPIRED_PASSWORD,
            UserVoter::DELETE,
            UserVoter::DISABLE,
            UserVoter::ENABLE,
            UserVoter::UNLOCK,
        ];

        $this->assertEquals($expected, $this->object->getSupportedAttributes());
    }

    public function testUnsupportedAttribute()
    {
        $hubert  = $this->findUser('hubert');
        $scruffy = $this->findUser('scruffy');

        $this->assertFalse($this->object->isGranted('UNKNOWN', $scruffy, $hubert));
    }

    public function testSetExpiredPassword()
    {
        $hubert = $this->findUser('hubert');

        $this->assertFalse($this->object->isGranted(UserVoter::SET_EXPIRED_PASSWORD, $hubert, $hubert));

        $hubert->setPasswordSetAt(time() - 86400 * 2);

        $this->object = new UserVoterStub($this->doctrine, 3);
        $this->assertFalse($this->object->isGranted(UserVoter::SET_EXPIRED_PASSWORD, $hubert, $hubert));

        $this->object = new UserVoterStub($this->doctrine, 1);
        $this->assertTrue($this->object->isGranted(UserVoter::SET_EXPIRED_PASSWORD, $hubert, $hubert));
    }

    public function testDelete()
    {
        $fry     = $this->findUser('fry');
        $hubert  = $this->findUser('hubert');
        $leela   = $this->findUser('leela');
        $scruffy = $this->findUser('scruffy');

        $this->assertInstanceOf('eTraxis\Entity\User', $fry);
        $this->assertInstanceOf('eTraxis\Entity\User', $hubert);
        $this->assertInstanceOf('eTraxis\Entity\User', $leela);
        $this->assertInstanceOf('eTraxis\Entity\User', $scruffy);

        $this->assertFalse($this->object->isGranted(UserVoter::DELETE, $hubert, $hubert));
        $this->assertFalse($this->object->isGranted(UserVoter::DELETE, $leela, $hubert));
        $this->assertTrue($this->object->isGranted(UserVoter::DELETE, $scruffy, $hubert));

        $this->assertFalse($this->object->isGranted(UserVoter::DELETE, $scruffy, $fry));

        $this->assertFalse($this->object->isGranted(UserVoter::DELETE, $scruffy));
    }

    public function testDisable()
    {
        $fry      = $this->findUser('fry');
        $hubert   = $this->findUser('hubert');
        $bender   = $this->findUser('bender');
        $francine = $this->findUser('francine');

        $this->assertInstanceOf('eTraxis\Entity\User', $fry);
        $this->assertInstanceOf('eTraxis\Entity\User', $hubert);
        $this->assertInstanceOf('eTraxis\Entity\User', $bender);
        $this->assertInstanceOf('eTraxis\Entity\User', $francine);

        $this->assertFalse($this->object->isGranted(UserVoter::DISABLE, $hubert, $hubert));
        $this->assertTrue($this->object->isGranted(UserVoter::DISABLE, $bender, $hubert));
        $this->assertFalse($this->object->isGranted(UserVoter::DISABLE, $francine, $hubert));

        $this->assertFalse($this->object->isGranted(UserVoter::DISABLE, $bender, $fry));

        $this->assertFalse($this->object->isGranted(UserVoter::DISABLE, $bender));
    }

    public function testEnable()
    {
        $fry      = $this->findUser('fry');
        $hubert   = $this->findUser('hubert');
        $bender   = $this->findUser('bender');
        $francine = $this->findUser('francine');

        $this->assertInstanceOf('eTraxis\Entity\User', $fry);
        $this->assertInstanceOf('eTraxis\Entity\User', $hubert);
        $this->assertInstanceOf('eTraxis\Entity\User', $bender);
        $this->assertInstanceOf('eTraxis\Entity\User', $francine);

        $this->assertFalse($this->object->isGranted(UserVoter::ENABLE, $bender, $hubert));
        $this->assertTrue($this->object->isGranted(UserVoter::ENABLE, $francine, $hubert));

        $this->assertFalse($this->object->isGranted(UserVoter::ENABLE, $francine, $fry));

        $this->assertFalse($this->object->isGranted(UserVoter::ENABLE, $francine));
    }

    public function testUnlock()
    {
        $fry    = $this->findUser('fry');
        $hubert = $this->findUser('hubert');
        $bender = $this->findUser('bender');

        $this->assertInstanceOf('eTraxis\Entity\User', $fry);
        $this->assertInstanceOf('eTraxis\Entity\User', $hubert);
        $this->assertInstanceOf('eTraxis\Entity\User', $bender);

        $bender->setAuthAttempts(3);
        $bender->setLockedUntil(time() + 60);

        $this->assertFalse($this->object->isGranted(UserVoter::UNLOCK, $fry, $hubert));
        $this->assertTrue($this->object->isGranted(UserVoter::UNLOCK, $bender, $hubert));

        $this->assertFalse($this->object->isGranted(UserVoter::UNLOCK, $bender, $fry));

        $this->assertFalse($this->object->isGranted(UserVoter::UNLOCK, $bender));
    }
}
