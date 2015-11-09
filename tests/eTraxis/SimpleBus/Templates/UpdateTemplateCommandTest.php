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

use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class UpdateTemplateCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);

        $this->assertNotNull($template);
        $this->assertEquals('Delivery', $template->getName());
        $this->assertEquals('PE', $template->getPrefix());
        $this->assertEquals('Delivery task', $template->getDescription());
        $this->assertNull($template->getCriticalAge());
        $this->assertNull($template->getFrozenTime());
        $this->assertFalse($template->hasGuestAccess());

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

        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);

        $this->assertNull($template);

        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Maintenance']);

        $this->assertNotNull($template);
        $this->assertEquals('Maintenance', $template->getName());
        $this->assertEquals('M', $template->getPrefix());
        $this->assertEquals('Nimbus technical maintenance', $template->getDescription());
        $this->assertEquals(100, $template->getCriticalAge());
        $this->assertEquals(100, $template->getFrozenTime());
        $this->assertTrue($template->hasGuestAccess());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testUnknownProject()
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
     * @expectedException \eTraxis\SimpleBus\CommandException
     */
    public function testNameConflict()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $template = new Template();

        $template
            ->setName('Maintenance')
            ->setPrefix('M')
            ->setLocked(true)
            ->setGuestAccess(true)
            ->setRegisteredPermissions(0)
            ->setAuthorPermissions(0)
            ->setResponsiblePermissions(0)
            ->setProject($project)
        ;

        $this->doctrine->getManager()->persist($template);
        $this->doctrine->getManager()->flush();

        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);

        $this->assertNotNull($template);

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
     * @expectedException \eTraxis\SimpleBus\CommandException
     */
    public function testPrefixConflict()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $template = new Template();

        $template
            ->setName('Maintenance')
            ->setPrefix('M')
            ->setLocked(true)
            ->setGuestAccess(true)
            ->setRegisteredPermissions(0)
            ->setAuthorPermissions(0)
            ->setResponsiblePermissions(0)
            ->setProject($project)
        ;

        $this->doctrine->getManager()->persist($template);
        $this->doctrine->getManager()->flush();

        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);

        $this->assertNotNull($template);

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
