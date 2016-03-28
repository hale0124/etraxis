<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\EventListener;

use eTraxis\Tests\BaseTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UsersLockoutTest extends BaseTestCase
{
    public function testSuccess()
    {
        $user = $this->findUser('artem');

        self::assertNotNull($user);

        $token = new UsernamePasswordToken($user, 'secret', 'etraxis_provider');

        $success = new AuthenticationEvent($token);

        $object = new UsersLockout($this->logger, $this->command_bus);

        $object->onSuccess($success);
        self::assertTrue($this->findUser('artem')->isAccountNonLocked());
    }

    public function testFailure()
    {
        $token = new UsernamePasswordToken('artem', 'secret', 'etraxis_provider');

        $failure = new AuthenticationFailureEvent($token, new AuthenticationException());

        $object = new UsersLockout($this->logger, $this->command_bus);

        $object->onFailure($failure);
        self::assertTrue($this->findUser('artem')->isAccountNonLocked());
    }
}
