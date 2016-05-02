<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Tests;

use eTraxis\Entity\User;

class BaseTestCaseTest extends BaseTestCase
{
    public function testFindUser()
    {
        self::assertNull($this->findUser('unknown'));

        $user = $this->findUser('artem');

        self::assertInstanceOf(User::class, $user);
        self::assertEquals('artem', $user->getUsername());
    }

    public function testLoginAs()
    {
        self::assertFalse($this->loginAs('unknown'));
        self::assertTrue($this->loginAs('artem'));
        self::assertTrue($this->loginAs('einstein', true));
    }
}
