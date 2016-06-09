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
use eTraxis\Tests\TransactionalTestCase;

class TemplateVoterTest extends TransactionalTestCase
{
    /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker */
    private $security;

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

        self::assertFalse($this->security->isGranted('UNKNOWN', $template));
    }

    public function testDelete()
    {
        $this->loginAs('hubert');

        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        $template = new Template($project);

        $template
            ->setName('Bug report')
            ->setPrefix('bug')
            ->setLocked(true)
        ;

        $this->doctrine->getManager()->persist($template);
        $this->doctrine->getManager()->flush();

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        /** @var Template $empty */
        $empty = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Bug report']);

        self::assertInstanceOf(Template::class, $template);
        self::assertInstanceOf(Template::class, $empty);

        self::assertFalse($this->security->isGranted(Template::DELETE, $template));
        self::assertTrue($this->security->isGranted(Template::DELETE, $empty));
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

        self::assertTrue($delivery->isLocked());
        self::assertFalse($this->security->isGranted(Template::LOCK, $delivery));
        self::assertTrue($this->security->isGranted(Template::UNLOCK, $delivery));

        /** @var Template $futurama */
        $futurama = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Futurama']);

        self::assertFalse($futurama->isLocked());
        self::assertTrue($this->security->isGranted(Template::LOCK, $futurama));
        self::assertFalse($this->security->isGranted(Template::UNLOCK, $futurama));
    }

    public function testUnlockNoInitialState()
    {
        $this->loginAs('hubert');

        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        $template = new Template($project);

        $template
            ->setName('Bug report')
            ->setPrefix('bug')
            ->setLocked(true)
        ;

        $this->doctrine->getManager()->persist($template);
        $this->doctrine->getManager()->flush();

        /** @var Template $empty */
        $empty = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Bug report']);

        self::assertTrue($empty->isLocked());
        self::assertFalse($this->security->isGranted(Template::UNLOCK, $empty));
    }
}
