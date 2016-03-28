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
use eTraxis\Tests\BaseTestCase;

class CreateTemplateCommandTest extends BaseTestCase
{
    /**
     * @return  \eTraxis\Entity\Project
     */
    private function getProject()
    {
        return $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);
    }

    public function testSuccess()
    {
        $project     = $this->getProject();
        $name        = 'Maintenance';
        $prefix      = 'M';
        $description = 'Nimbus technical maintenance';
        $guestAccess = true;

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => $name]);

        self::assertNull($template);

        $command = new CreateTemplateCommand([
            'project'     => $project->getId(),
            'name'        => $name,
            'prefix'      => $prefix,
            'description' => $description,
            'guestAccess' => $guestAccess,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => $name]);

        self::assertInstanceOf(Template::class, $template);
        self::assertEquals($project->getId(), $template->getProject()->getId());
        self::assertEquals($name, $template->getName());
        self::assertEquals($prefix, $template->getPrefix());
        self::assertEquals($description, $template->getDescription());
        self::assertEquals($guestAccess, $template->hasGuestAccess());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown project.
     */
    public function testUnknownProject()
    {
        $command = new CreateTemplateCommand([
            'project'     => $this->getMaxId(),
            'name'        => 'Maintenance',
            'prefix'      => 'M',
            'description' => 'Nimbus technical maintenance',
            'guestAccess' => true,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Template with entered name already exists.
     */
    public function testNameConflict()
    {
        $command = new CreateTemplateCommand([
            'project'     => $this->getProject()->getId(),
            'name'        => 'Delivery',
            'prefix'      => 'M',
            'description' => 'Nimbus technical maintenance',
            'guestAccess' => true,
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Template with entered prefix already exists.
     */
    public function testPrefixConflict()
    {
        $command = new CreateTemplateCommand([
            'project'     => $this->getProject()->getId(),
            'name'        => 'Maintenance',
            'prefix'      => 'PE',
            'description' => 'Nimbus technical maintenance',
            'guestAccess' => true,
        ]);

        $this->command_bus->handle($command);
    }
}
