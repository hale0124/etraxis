<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\EventListener;

use eTraxis\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListenerTest extends BaseTestCase
{
    public function testAuthenticationSuccess()
    {
        $user = $this->findUser('artem');
        $user->setLocale('ru');

        $request = new Request();
        $token   = new UsernamePasswordToken($user, null, 'etraxis_provider');

        $event = new InteractiveLoginEvent($request, $token);

        $object = new SecurityListener($this->session);
        $object->onInteractiveLogin($event);

        $this->assertEquals('ru', $this->session->get('_locale'));
    }
}
