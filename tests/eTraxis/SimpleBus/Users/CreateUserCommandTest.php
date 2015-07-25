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

namespace eTraxis\SimpleBus\Users;

use eTraxis\Tests\BaseTestCase;

class CreateUserCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $username    = 'anna';
        $fullname    = 'Anna Rodygina';
        $email       = 'anna@example.com';
        $description = 'Very lovely Daughter';
        $password    = 'secret';
        $encoded     = '5en6G6MezRroT3XKqkdPOmY/BfQ=';
        $admin       = true;
        $disabled    = false;

        $user = $this->findUser($username);

        $this->assertNull($user);

        $command = new CreateUserCommand([
            'username'    => $username,
            'fullname'    => $fullname,
            'email'       => $email,
            'description' => $description,
            'password'    => $password,
            'admin'       => $admin,
            'disabled'    => $disabled,
            'locale'      => static::$kernel->getContainer()->getParameter('locale'),
            'theme'       => static::$kernel->getContainer()->getParameter('theme'),
            'timezone'    => 377,
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser($username);

        $id = $user->getId();

        $this->assertInstanceOf('eTraxis\Entity\User', $user);
        $this->assertEquals($id, $command->result);
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($fullname, $user->getFullname());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($description, $user->getDescription());
        $this->assertEquals($encoded, $user->getPassword());
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isDisabled());
        $this->assertFalse($user->isLdap());
        $this->assertEquals(static::$kernel->getContainer()->getParameter('locale'), $user->getLocale());
        $this->assertEquals(static::$kernel->getContainer()->getParameter('theme'), $user->getTheme());
        $this->assertEquals(377, $user->getTimezone());
    }

    /**
     * @expectedException \eTraxis\SimpleBus\CommandException
     */
    public function testPasswordTooShort()
    {
        $command = new CreateUserCommand([
            'username' => 'anna',
            'fullname' => 'Anna Rodygina',
            'email'    => 'anna@example.com',
            'password' => 'short',
            'admin'    => true,
            'disabled' => false,
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
        $command = new CreateUserCommand([
            'username' => 'artem',
            'fullname' => 'Artem Rodygin',
            'email'    => 'artem@example.com',
            'password' => 'secret',
            'admin'    => true,
            'disabled' => false,
            'locale'   => static::$kernel->getContainer()->getParameter('locale'),
            'theme'    => static::$kernel->getContainer()->getParameter('theme'),
            'timezone' => 0,
        ]);

        $this->command_bus->handle($command);
    }
}
