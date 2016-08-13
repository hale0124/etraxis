<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Templates;

use eTraxis\Entity\Template;
use eTraxis\Tests\TransactionalTestCase;

class LockUnlockTemplateCommandTest extends TransactionalTestCase
{
    /**
     * @return  \eTraxis\Entity\Template
     */
    private function getTemplate()
    {
        return $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);
    }

    public function testSuccess()
    {
        $this->loginAs('hubert');

        $template = $this->getTemplate();

        self::assertFalse($this->getTemplate()->isLocked());

        $command = new LockTemplateCommand(['id' => $template->getId()]);
        $this->command_bus->handle($command);

        self::assertTrue($this->getTemplate()->isLocked());

        $command = new UnlockTemplateCommand(['id' => $template->getId()]);
        $this->command_bus->handle($command);

        self::assertFalse($this->getTemplate()->isLocked());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testNotFoundLock()
    {
        $this->loginAs('hubert');

        $command = new LockTemplateCommand(['id' => self::UNKNOWN_ENTITY_ID]);
        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testNotFoundUnlock()
    {
        $this->loginAs('hubert');

        $command = new UnlockTemplateCommand(['id' => self::UNKNOWN_ENTITY_ID]);
        $this->command_bus->handle($command);
    }
}
