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

class CreateTemplateCommandTest extends BaseTestCase
{
    /**
     * @return  \eTraxis\Entity\Project
     */
    private function getProject()
    {
        return $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);
    }

    public function testSuccess()
    {
        $name        = 'Maintenance';
        $prefix      = 'M';
        $description = 'Nimbus technical maintenance';
        $guestAccess = true;

        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => $name]);

        $this->assertNull($template);

        $command = new CreateTemplateCommand([
            'project'     => $this->getProject()->getId(),
            'name'        => $name,
            'prefix'      => $prefix,
            'description' => $description,
            'guestAccess' => $guestAccess,
        ]);

        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => $name]);

        $this->assertInstanceOf('eTraxis\Entity\Template', $template);
        $this->assertEquals($name, $template->getName());
        $this->assertEquals($prefix, $template->getPrefix());
        $this->assertEquals($description, $template->getDescription());
        $this->assertEquals($guestAccess, $template->hasGuestAccess());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
     * @expectedException \eTraxis\SimpleBus\CommandException
     */
    public function testTemplateNameConflict()
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
     * @expectedException \eTraxis\SimpleBus\CommandException
     */
    public function testTemplatePrefixConflict()
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
