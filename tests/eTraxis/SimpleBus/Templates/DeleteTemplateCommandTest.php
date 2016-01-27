<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Templates;

use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class DeleteTemplateCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $template = new Template();

        $template
            ->setName('Bug report')
            ->setPrefix('bug')
            ->setLocked(true)
            ->setGuestAccess(false)
            ->setRegisteredPermissions(0)
            ->setAuthorPermissions(0)
            ->setResponsiblePermissions(0)
            ->setProject($project)
        ;

        $this->doctrine->getManager()->persist($template);
        $this->doctrine->getManager()->flush();

        $this->loginAs('hubert');

        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Bug report']);
        $this->assertNotNull($template);

        $command = new DeleteTemplateCommand(['id' => $template->getId()]);
        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Bug report']);
        $this->assertNull($template);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        $this->loginAs('hubert');

        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);
        $this->assertNotNull($template);

        $command = new DeleteTemplateCommand(['id' => $template->getId()]);
        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testNotFound()
    {
        $this->loginAs('hubert');

        $command = new DeleteTemplateCommand(['id' => $this->getMaxId()]);
        $this->command_bus->handle($command);
    }
}
