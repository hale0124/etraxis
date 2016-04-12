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

class UpdateTemplateCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        self::assertNotNull($template);
        self::assertEquals('Delivery', $template->getName());
        self::assertEquals('PE', $template->getPrefix());
        self::assertEquals('Delivery task', $template->getDescription());
        self::assertNull($template->getCriticalAge());
        self::assertNull($template->getFrozenTime());
        self::assertFalse($template->hasGuestAccess());

        $command = new UpdateTemplateCommand([
            'id'          => $template->getId(),
            'name'        => 'Maintenance',
            'prefix'      => 'M',
            'description' => 'Nimbus technical maintenance',
            'criticalAge' => 100,
            'frozenTime'  => 100,
            'guestAccess' => true,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        self::assertNull($template);

        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Maintenance']);

        self::assertNotNull($template);
        self::assertEquals('Maintenance', $template->getName());
        self::assertEquals('M', $template->getPrefix());
        self::assertEquals('Nimbus technical maintenance', $template->getDescription());
        self::assertEquals(100, $template->getCriticalAge());
        self::assertEquals(100, $template->getFrozenTime());
        self::assertTrue($template->hasGuestAccess());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown template.
     */
    public function testUnknownTemplate()
    {
        $command = new UpdateTemplateCommand([
            'id'          => $this->getMaxId(),
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
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        $template = new Template();

        $template
            ->setProject($project)
            ->setName('Maintenance')
            ->setPrefix('M')
            ->setLocked(true)
            ->setGuestAccess(true)
            ->setRegisteredPermissions(0)
            ->setAuthorPermissions(0)
            ->setResponsiblePermissions(0)
        ;

        $this->doctrine->getManager()->persist($template);
        $this->doctrine->getManager()->flush();

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        self::assertNotNull($template);

        $command = new UpdateTemplateCommand([
            'id'          => $template->getId(),
            'name'        => 'Maintenance',
            'prefix'      => $template->getPrefix(),
            'description' => $template->getDescription(),
            'criticalAge' => $template->getCriticalAge(),
            'frozenTime'  => $template->getFrozenTime(),
            'guestAccess' => $template->hasGuestAccess(),
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Template with entered prefix already exists.
     */
    public function testPrefixConflict()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        $template = new Template();

        $template
            ->setProject($project)
            ->setName('Maintenance')
            ->setPrefix('M')
            ->setLocked(true)
            ->setGuestAccess(true)
            ->setRegisteredPermissions(0)
            ->setAuthorPermissions(0)
            ->setResponsiblePermissions(0)
        ;

        $this->doctrine->getManager()->persist($template);
        $this->doctrine->getManager()->flush();

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        self::assertNotNull($template);

        $command = new UpdateTemplateCommand([
            'id'          => $template->getId(),
            'name'        => $template->getName(),
            'prefix'      => 'M',
            'description' => $template->getDescription(),
            'criticalAge' => $template->getCriticalAge(),
            'frozenTime'  => $template->getFrozenTime(),
            'guestAccess' => $template->hasGuestAccess(),
        ]);

        $this->command_bus->handle($command);
    }
}
