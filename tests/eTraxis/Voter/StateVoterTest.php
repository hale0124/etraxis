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

use eTraxis\Entity\State;
use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class StateVoterTest extends BaseTestCase
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

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        $this->assertFalse($this->security->isGranted('UNKNOWN', $state));
    }

    public function testDelete()
    {
        $this->loginAs('hubert');

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        $state = new State();

        $state
            ->setName('Cancelled')
            ->setAbbreviation('C')
            ->setType(State::TYPE_FINAL)
            ->setResponsible(State::RESPONSIBLE_REMOVE)
            ->setTemplate($template)
        ;

        $this->doctrine->getManager()->persist($state);
        $this->doctrine->getManager()->flush();

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        /** @var State $empty */
        $empty = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Cancelled']);

        $this->assertInstanceOf(State::class, $state);
        $this->assertInstanceOf(State::class, $empty);

        $this->assertFalse($this->security->isGranted(State::DELETE, $state));
        $this->assertTrue($this->security->isGranted(State::DELETE, $empty));
    }

    public function testInitial()
    {
        $this->loginAs('hubert');

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        $state = new State();

        $state
            ->setName('On the way')
            ->setAbbreviation('O')
            ->setType(State::TYPE_INTERIM)
            ->setResponsible(State::RESPONSIBLE_KEEP)
            ->setTemplate($template)
        ;

        $this->doctrine->getManager()->persist($state);
        $this->doctrine->getManager()->flush();

        /** @var State $initial */
        $initial = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var State $interim */
        $interim = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'On the way']);

        /** @var State $final */
        $final = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        $this->assertInstanceOf(State::class, $initial);
        $this->assertInstanceOf(State::class, $interim);
        $this->assertInstanceOf(State::class, $final);

        $this->assertFalse($this->security->isGranted(State::INITIAL, $initial));
        $this->assertTrue($this->security->isGranted(State::INITIAL, $interim));
        $this->assertFalse($this->security->isGranted(State::INITIAL, $final));
    }
}
