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

namespace eTraxis\Service;

use eTraxis\Tests\BaseTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class LocalizerServiceTest extends BaseTestCase
{
    private function getTimestamp()
    {
        $timestamp = 1089291600;
        $offset    = timezone_offset_get(timezone_open('UTC'), date_create()) - intval(date('Z'));

        return $timestamp + $offset;
    }

    public function testDate()
    {
        $expected = '7/8/2004'; // en_US

        $token_storage = new TokenStorage();

        $service = new LocalizerService($token_storage, 'xx_XX');

        $this->assertEquals($expected, $service->formatDate($this->getTimestamp()));
    }

    public function testTime()
    {
        $expected = '1:00 PM'; // en_US

        $token_storage = new TokenStorage();

        $service = new LocalizerService($token_storage, 'xx_XX');

        $this->assertEquals($expected, $service->formatTime($this->getTimestamp()));
    }

    public function testEmptyToken()
    {
        $expected = '08/07/2004'; // en_GB

        $token_storage = new TokenStorage();

        $service = new LocalizerService($token_storage, 'en_GB');

        $this->assertEquals($expected, $service->formatDate($this->getTimestamp()));
    }

    public function testGuest()
    {
        $expected = '08/07/2004'; // en_GB

        $token = new UsernamePasswordToken('anon.', null, 'etraxis.provider');

        $token_storage = new TokenStorage();
        $token_storage->setToken($token);

        $service = new LocalizerService($token_storage, 'en_GB');

        $this->assertEquals($expected, $service->formatDate($this->getTimestamp()));
    }

    public function testAuthenticated()
    {
        $expected = '7/8/2004'; // en_US

        $token = new UsernamePasswordToken($this->findUser('artem'), null, 'etraxis.provider');

        $token_storage = new TokenStorage();
        $token_storage->setToken($token);

        $service = new LocalizerService($token_storage, 'en_GB');

        $this->assertEquals($expected, $service->formatDate($this->getTimestamp()));
    }
}
