<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\EventListener;

use eTraxis\Tests\BaseTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationListenerTest extends BaseTestCase
{
    /**
     * @return  \eTraxis\Model\User
     */
    protected function findUser()
    {
        $repository = $this->doctrine->getManager()->getRepository('eTraxis:User');

        return $repository->findOneBy(['username' => 'artem@eTraxis']);
    }

    public function testAuthenticationSuccess()
    {
        $token = new UsernamePasswordToken('artem', 'secret', 'etraxis_provider');

        $success = new AuthenticationEvent($token);

        $object = new AuthenticationListener($this->command_bus);

        $object->onAuthenticationSuccess($success);
        $this->assertTrue($this->findUser()->isAccountNonLocked());
    }

    public function testAuthenticationFailure()
    {
        $token = new UsernamePasswordToken('artem', 'secret', 'etraxis_provider');

        $success = new AuthenticationEvent($token);
        $failure = new AuthenticationFailureEvent($token, new AuthenticationException());

        $object = new AuthenticationListener($this->command_bus);

        $object->onAuthenticationFailure($failure);
        $this->assertTrue($this->findUser()->isAccountNonLocked());
    }
}
