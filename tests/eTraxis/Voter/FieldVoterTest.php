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

use eTraxis\Entity\Field;
use eTraxis\Entity\Template;
use eTraxis\SimpleBus\Templates\LockTemplateCommand;
use eTraxis\Tests\BaseTestCase;

class FieldVoterTest extends BaseTestCase
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

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        self::assertFalse($this->security->isGranted('UNKNOWN', $field));
    }

    public function testDeleteLocked()
    {
        $this->loginAs('hubert');

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        $command = new LockTemplateCommand(['id' => $template->getId()]);
        $this->command_bus->handle($command);

        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        self::assertTrue($template->isLocked());
        self::assertTrue($this->security->isGranted(Field::DELETE, $field));
    }

    public function testDeleteUnlocked()
    {
        $this->loginAs('hubert');

        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Crew']);

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        self::assertFalse($template->isLocked());
        self::assertFalse($this->security->isGranted(Field::DELETE, $field));
    }
}
