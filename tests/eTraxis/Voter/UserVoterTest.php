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

class UserVoterTest extends BaseTestCase
{
    public function testDisable()
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->client->getContainer()->get('security.authorization_checker');

        $hubert   = $this->findUser('hubert');
        $bender   = $this->findUser('bender');
        $francine = $this->findUser('francine');

        $this->assertInstanceOf('eTraxis\Entity\User', $hubert);
        $this->assertInstanceOf('eTraxis\Entity\User', $bender);
        $this->assertInstanceOf('eTraxis\Entity\User', $francine);

        $this->loginAs('hubert');

        $this->assertFalse($authChecker->isGranted(UserVoter::DISABLE, $hubert));
        $this->assertTrue($authChecker->isGranted(UserVoter::DISABLE, $bender));
        $this->assertFalse($authChecker->isGranted(UserVoter::DISABLE, $francine));

        $this->loginAs('fry');

        $this->assertFalse($authChecker->isGranted(UserVoter::DISABLE, $bender));
    }

    public function testEnable()
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->client->getContainer()->get('security.authorization_checker');

        $bender   = $this->findUser('bender');
        $francine = $this->findUser('francine');

        $this->assertInstanceOf('eTraxis\Entity\User', $bender);
        $this->assertInstanceOf('eTraxis\Entity\User', $francine);

        $this->loginAs('hubert');

        $this->assertFalse($authChecker->isGranted(UserVoter::ENABLE, $bender));
        $this->assertTrue($authChecker->isGranted(UserVoter::ENABLE, $francine));

        $this->loginAs('fry');

        $this->assertFalse($authChecker->isGranted(UserVoter::ENABLE, $francine));
    }

    public function testUnlock()
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->client->getContainer()->get('security.authorization_checker');

        $hubert = $this->findUser('hubert');
        $bender = $this->findUser('bender');
        $fry    = $this->findUser('fry');

        $this->assertInstanceOf('eTraxis\Entity\User', $hubert);
        $this->assertInstanceOf('eTraxis\Entity\User', $fry);
        $this->assertInstanceOf('eTraxis\Entity\User', $bender);

        $bender->setAuthAttempts(3);
        $bender->setLockedUntil(time() + 60);

        $this->loginAs('hubert');

        $this->assertFalse($authChecker->isGranted(UserVoter::UNLOCK, $fry));
        $this->assertTrue($authChecker->isGranted(UserVoter::UNLOCK, $bender));

        $this->loginAs('fry');

        $this->assertFalse($authChecker->isGranted(UserVoter::UNLOCK, $bender));
    }
}
