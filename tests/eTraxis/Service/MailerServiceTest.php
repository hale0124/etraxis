<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MailerServiceTest extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    public function testSend()
    {
        $logger = static::$kernel->getContainer()->get('logger');
        $twig   = static::$kernel->getContainer()->get('twig');
        $mailer = static::$kernel->getContainer()->get('mailer');

        /** @noinspection PhpParamsInspection */
        $service = new Mailer\MailerService($logger, $twig, $mailer, 'noreply@example.com', 'eTraxis mailer');

        $result = $service->send(
            'test@example.com',
            'Recipient',
            'Test',
            'email.html.twig'
        );

        self::assertTrue($result);
    }
}
