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
use eTraxis\Tests\TransactionalTestCase;

class StateVoterTest extends TransactionalTestCase
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

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        self::assertFalse($this->security->isGranted('UNKNOWN', $state));
    }

    public function testDelete()
    {
        $this->loginAs('hubert');

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        $state = new State();

        $state
            ->setTemplate($template)
            ->setName('Cancelled')
            ->setAbbreviation('C')
            ->setType(State::TYPE_FINAL)
            ->setResponsible(State::RESPONSIBLE_REMOVE)
        ;

        $this->doctrine->getManager()->persist($state);
        $this->doctrine->getManager()->flush();

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        /** @var State $empty */
        $empty = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Cancelled']);

        self::assertInstanceOf(State::class, $state);
        self::assertInstanceOf(State::class, $empty);

        self::assertFalse($this->security->isGranted(State::DELETE, $state));
        self::assertTrue($this->security->isGranted(State::DELETE, $empty));
    }

    public function testInitial()
    {
        $this->loginAs('hubert');

        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        $state = new State();

        $state
            ->setTemplate($template)
            ->setName('On the way')
            ->setAbbreviation('O')
            ->setType(State::TYPE_INTERIM)
            ->setResponsible(State::RESPONSIBLE_KEEP)
        ;

        $this->doctrine->getManager()->persist($state);
        $this->doctrine->getManager()->flush();

        /** @var State $initial */
        $initial = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var State $interim */
        $interim = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'On the way']);

        /** @var State $final */
        $final = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        self::assertInstanceOf(State::class, $initial);
        self::assertInstanceOf(State::class, $interim);
        self::assertInstanceOf(State::class, $final);

        self::assertFalse($this->security->isGranted(State::INITIAL, $initial));
        self::assertTrue($this->security->isGranted(State::INITIAL, $interim));
        self::assertFalse($this->security->isGranted(State::INITIAL, $final));
    }
}
