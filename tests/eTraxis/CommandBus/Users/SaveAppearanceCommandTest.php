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

namespace eTraxis\CommandBus\Users;

use eTraxis\Tests\BaseTestCase;

class SaveAppearanceCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $user = $this->findUser('bender');

        $this->assertNotNull($user);
        $this->assertEquals('en_US', $user->getLocale());
        $this->assertEquals('azure', $user->getTheme());
        $this->assertEquals(0, $user->getTimezone());

        $command = new SaveAppearanceCommand([
            'id'       => $user->getId(),
            'locale'   => 'es',
            'theme'    => 'humanity',
            'timezone' => 377,
        ]);

        $this->command_bus->handle($command);

        $user = $this->doctrine->getRepository('eTraxis:User')->find($user->getId());

        $this->assertEquals('es', $user->getLocale());
        $this->assertEquals('humanity', $user->getTheme());
        $this->assertEquals(377, $user->getTimezone());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testUnknownUser()
    {
        $user = $this->findUser('flexo');

        $this->assertNull($user);

        $command = new SaveAppearanceCommand([
            'id'       => $this->getMaxId(),
            'locale'   => static::$kernel->getContainer()->getParameter('locale'),
            'theme'    => static::$kernel->getContainer()->getParameter('theme'),
            'timezone' => 0,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\ValidationException
     */
    public function testEmptyCommand()
    {
        $command = new SaveAppearanceCommand();

        $this->command_bus->handle($command);
    }
}
