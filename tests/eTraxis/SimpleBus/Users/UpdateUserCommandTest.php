<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users;

use eTraxis\Tests\BaseTestCase;

class UpdateUserCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $this->loginAs('hubert');

        $user = $this->findUser('bender');

        $this->assertNotNull($user);
        $this->assertNotEmpty($user->getDescription());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isDisabled());
        $this->assertEquals('en_US', $user->getLocale());
        $this->assertEquals('azure', $user->getTheme());
        $this->assertEquals(0, $user->getTimezone());

        $command = new UpdateUserCommand([
            'id'       => $user->getId(),
            'username' => 'flexo',
            'fullname' => 'Flexo',
            'email'    => 'flexo@example.com',
            'admin'    => true,
            'disabled' => true,
            'locale'   => 'es',
            'theme'    => 'humanity',
            'timezone' => 377,
        ]);

        $this->command_bus->handle($command);

        $user = $this->doctrine->getRepository('eTraxis:User')->find($user->getId());

        $this->assertEquals('flexo', $user->getUsername());
        $this->assertEquals('Flexo', $user->getFullname());
        $this->assertEquals('flexo@example.com', $user->getEmail());
        $this->assertEmpty($user->getDescription());
        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->isDisabled());
        $this->assertEquals('es', $user->getLocale());
        $this->assertEquals('humanity', $user->getTheme());
        $this->assertEquals(377, $user->getTimezone());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testUnknownUser()
    {
        $this->loginAs('hubert');

        $user = $this->findUser('flexo');

        $this->assertNull($user);

        $command = new UpdateUserCommand([
            'id'       => $this->getMaxId(),
            'username' => 'flexo',
            'fullname' => 'Flexo',
            'email'    => 'flexo@example.com',
            'admin'    => true,
            'disabled' => true,
            'locale'   => static::$kernel->getContainer()->getParameter('locale'),
            'theme'    => static::$kernel->getContainer()->getParameter('theme'),
            'timezone' => 0,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\SimpleBus\CommandException
     */
    public function testUsernameConflict()
    {
        $this->loginAs('hubert');

        $user = $this->findUser('fry');

        $this->assertNotNull($user);

        $command = new UpdateUserCommand([
            'id'       => $user->getId(),
            'username' => 'bender',
            'fullname' => $user->getFullname(),
            'email'    => $user->getEmail(),
            'admin'    => $user->isAdmin(),
            'disabled' => $user->isDisabled(),
            'locale'   => static::$kernel->getContainer()->getParameter('locale'),
            'theme'    => static::$kernel->getContainer()->getParameter('theme'),
            'timezone' => 0,
        ]);

        $this->command_bus->handle($command);
    }
}
