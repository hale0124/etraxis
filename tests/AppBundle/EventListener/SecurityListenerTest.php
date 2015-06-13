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


namespace AppBundle\EventListener;

use eTraxis\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListenerTest extends BaseTestCase
{
    /**
     * @return  \eTraxis\Model\User
     */
    protected function findUser()
    {
        $repository = $this->doctrine->getManager()->getRepository('eTraxis:User');
        $user       = $repository->findOneBy(['username' => 'artem@eTraxis']);

        $user->setLocale('ru');

        return $user;
    }

    public function testAuthenticationSuccess()
    {
        $request = new Request();
        $token   = new UsernamePasswordToken($this->findUser(), 'secret', 'etraxis_provider');

        $event = new InteractiveLoginEvent($request, $token);

        $object = new SecurityListener($this->session);
        $object->onInteractiveLogin($event);

        $this->assertEquals('ru', $this->session->get('_locale'));
    }
}
