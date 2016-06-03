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

use eTraxis\Entity\Project;
use eTraxis\Entity\Template;
use eTraxis\Tests\TransactionalTestCase;

class DeleteTemplateCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        $template = new Template();

        $template
            ->setProject($project)
            ->setName('Bug report')
            ->setPrefix('bug')
            ->setLocked(true)
        ;

        $this->doctrine->getManager()->persist($template);
        $this->doctrine->getManager()->flush();

        $this->loginAs('hubert');

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Bug report']);
        self::assertNotNull($template);

        $command = new DeleteTemplateCommand(['id' => $template->getId()]);
        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Bug report']);
        self::assertNull($template);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        $this->loginAs('hubert');

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

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

        $command = new DeleteTemplateCommand(['id' => self::UNKNOWN_ENTITY_ID]);
        $this->command_bus->handle($command);
    }
}
