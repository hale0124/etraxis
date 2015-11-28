<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Templates;

use eTraxis\Tests\BaseTestCase;

class LockUnlockTemplateCommandTest extends BaseTestCase
{
    /**
     * @return  \eTraxis\Entity\Template
     */
    private function getTemplate()
    {
        return $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);
    }

    public function testSuccess()
    {
        $this->loginAs('hubert');

        $template = $this->getTemplate();
        $this->assertNotNull($template);
        $id = $template->getId();

        $this->assertFalse($this->getTemplate()->isLocked());

        $command = new LockTemplateCommand(['id' => $id]);
        $this->command_bus->handle($command);

        $this->assertTrue($this->getTemplate()->isLocked());

        $command = new UnlockTemplateCommand(['id' => $id]);
        $this->command_bus->handle($command);

        $this->assertFalse($this->getTemplate()->isLocked());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testNotFoundLock()
    {
        $this->loginAs('hubert');

        $command = new LockTemplateCommand(['id' => $this->getMaxId()]);
        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testNotFoundUnlock()
    {
        $this->loginAs('hubert');

        $command = new UnlockTemplateCommand(['id' => $this->getMaxId()]);
        $this->command_bus->handle($command);
    }
}
