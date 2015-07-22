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

        $artem    = $this->findUser('artem');
        $bender   = $this->findUser('bender');
        $francine = $this->findUser('francine');

        $this->assertInstanceOf('eTraxis\Entity\User', $artem);
        $this->assertInstanceOf('eTraxis\Entity\User', $bender);
        $this->assertInstanceOf('eTraxis\Entity\User', $francine);

        $this->loginAs('artem');

        $this->assertFalse($authChecker->isGranted(UserVoter::DISABLE, $artem));
        $this->assertTrue($authChecker->isGranted(UserVoter::DISABLE, $bender));
        $this->assertFalse($authChecker->isGranted(UserVoter::DISABLE, $francine));

        $this->loginAs('bender');

        $this->assertFalse($authChecker->isGranted(UserVoter::DISABLE, $artem));
    }

    public function testEnable()
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->client->getContainer()->get('security.authorization_checker');

        $bender   = $this->findUser('bender');
        $francine = $this->findUser('francine');

        $this->assertInstanceOf('eTraxis\Entity\User', $bender);
        $this->assertInstanceOf('eTraxis\Entity\User', $francine);

        $this->loginAs('artem');

        $this->assertFalse($authChecker->isGranted(UserVoter::ENABLE, $bender));
        $this->assertTrue($authChecker->isGranted(UserVoter::ENABLE, $francine));

        $this->loginAs('bender');

        $this->assertFalse($authChecker->isGranted(UserVoter::ENABLE, $francine));
    }

    public function testUnlock()
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->client->getContainer()->get('security.authorization_checker');

        $artem  = $this->findUser('artem');
        $bender = $this->findUser('bender');
        $fry    = $this->findUser('fry');

        $this->assertInstanceOf('eTraxis\Entity\User', $artem);
        $this->assertInstanceOf('eTraxis\Entity\User', $bender);
        $this->assertInstanceOf('eTraxis\Entity\User', $fry);

        $fry->setAuthAttempts(3);
        $fry->setLockedUntil(time() + 60);

        $this->loginAs('artem');

        $this->assertFalse($authChecker->isGranted(UserVoter::UNLOCK, $bender));
        $this->assertTrue($authChecker->isGranted(UserVoter::UNLOCK, $fry));

        $this->loginAs('bender');

        $this->assertFalse($authChecker->isGranted(UserVoter::UNLOCK, $artem));
        $this->assertFalse($authChecker->isGranted(UserVoter::UNLOCK, $fry));
    }
}
