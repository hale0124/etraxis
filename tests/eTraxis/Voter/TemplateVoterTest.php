<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Voter;

use eTraxis\Entity\Project;
use eTraxis\Entity\Template;
use eTraxis\SimpleBus\Templates\LockTemplateCommand;
use eTraxis\Tests\BaseTestCase;

class TemplateVoterTest extends BaseTestCase
{
    /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker */
    private $security = null;

    protected function setUp()
    {
        parent::setUp();

        $this->security = $this->client->getContainer()->get('security.authorization_checker');
    }

    public function testUnsupportedAttribute()
    {
        $this->loginAs('hubert');

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        $this->assertFalse($this->security->isGranted('UNKNOWN', $template));
    }

    public function testDelete()
    {
        $this->loginAs('hubert');

        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

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

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        /** @var Template $empty */
        $empty = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Bug report']);

        $this->assertInstanceOf('eTraxis\Entity\Template', $template);
        $this->assertInstanceOf('eTraxis\Entity\Template', $empty);

        $this->assertFalse($this->security->isGranted(Template::DELETE, $template));
        $this->assertTrue($this->security->isGranted(Template::DELETE, $empty));
    }

    public function testLockUnlock()
    {
        $this->loginAs('hubert');

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        $command = new LockTemplateCommand(['id' => $template->getId()]);
        $this->command_bus->handle($command);

        /** @var Template $delivery */
        $delivery = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        $this->assertTrue($delivery->isLocked());
        $this->assertFalse($this->security->isGranted(Template::LOCK, $delivery));
        $this->assertTrue($this->security->isGranted(Template::UNLOCK, $delivery));

        /** @var Template $futurama */
        $futurama = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Futurama']);

        $this->assertFalse($futurama->isLocked());
        $this->assertTrue($this->security->isGranted(Template::LOCK, $futurama));
        $this->assertFalse($this->security->isGranted(Template::UNLOCK, $futurama));
    }

    public function testUnlockNoInitialState()
    {
        $this->loginAs('hubert');

        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

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

        /** @var Template $empty */
        $empty = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Bug report']);

        $this->assertTrue($empty->isLocked());
        $this->assertFalse($this->security->isGranted(Template::UNLOCK, $empty));
    }
}
