<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users;

use eTraxis\Entity\User;
use eTraxis\Tests\BaseTestCase;

class SaveAppearanceCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $user = $this->findUser('bender');

        self::assertEquals('en_US', $user->getSettings()->getLocale());
        self::assertEquals('azure', $user->getSettings()->getTheme());
        self::assertEquals(0, $user->getSettings()->getTimezone());

        $command = new SaveAppearanceCommand([
            'id'       => $user->getId(),
            'locale'   => 'es',
            'theme'    => 'humanity',
            'timezone' => 377,
        ]);

        $this->command_bus->handle($command);

        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->find($user->getId());

        self::assertEquals('es', $user->getSettings()->getLocale());
        self::assertEquals('humanity', $user->getSettings()->getTheme());
        self::assertEquals(377, $user->getSettings()->getTimezone());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown user.
     */
    public function testUnknownUser()
    {
        $user = $this->findUser('flexo');

        self::assertNull($user);

        $command = new SaveAppearanceCommand([
            'id'       => self::UNKNOWN_ENTITY_ID,
            'locale'   => static::$kernel->getContainer()->getParameter('locale'),
            'theme'    => static::$kernel->getContainer()->getParameter('theme'),
            'timezone' => 0,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \SimpleBus\ValidationException
     * @expectedExceptionMessage This value should not be blank.
     */
    public function testEmptyCommand()
    {
        $command = new SaveAppearanceCommand();

        $this->command_bus->handle($command);
    }
}
