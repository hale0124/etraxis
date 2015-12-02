<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Voter;

use eTraxis\Entity\State;
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
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

        $this->assertFalse($this->security->isGranted('UNKNOWN', $state));
    }

    public function testDelete()
    {
        $this->loginAs('hubert');

        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);

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
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

        /** @var State $empty */
        $empty = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Cancelled']);

        $this->assertInstanceOf('eTraxis\Entity\State', $state);
        $this->assertInstanceOf('eTraxis\Entity\State', $empty);

        $this->assertFalse($this->security->isGranted(State::DELETE, $state));
        $this->assertTrue($this->security->isGranted(State::DELETE, $empty));
    }
}
