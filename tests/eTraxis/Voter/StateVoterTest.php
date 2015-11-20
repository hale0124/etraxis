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
use eTraxis\Traits\ClassAccessTrait;

/**
 * @method getSupportedClasses()
 * @method getSupportedAttributes()
 * @method isGranted($attribute, $object, $user = null);
 */
class StateVoterStub extends StateVoter
{
    use ClassAccessTrait;
}

class StateVoterTest extends BaseTestCase
{
    /** @var StateVoterStub */
    private $object = null;

    protected function setUp()
    {
        parent::setUp();

        /** @var \eTraxis\Repository\EventsRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:Event');

        $this->object = new StateVoterStub($repository);
    }

    public function testGetSupportedClasses()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

        $expected = [
            get_class($state),
        ];

        $this->assertEquals($expected, $this->object->getSupportedClasses());
    }

    public function testGetSupportedAttributes()
    {
        $expected = [
            State::DELETE,
        ];

        $this->assertEquals($expected, $this->object->getSupportedAttributes());
    }

    public function testUnsupportedAttribute()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

        $this->assertFalse($this->object->isGranted('UNKNOWN', $state));
    }

    public function testDelete()
    {
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

        $this->assertFalse($this->object->isGranted(State::DELETE, $state));
        $this->assertTrue($this->object->isGranted(State::DELETE, $empty));
    }
}
