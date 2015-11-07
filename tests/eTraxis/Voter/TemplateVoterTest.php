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

use eTraxis\Tests\BaseTestCase;
use eTraxis\Traits\ClassAccessTrait;

/**
 * @method getSupportedClasses()
 * @method getSupportedAttributes()
 * @method isGranted($attribute, $object, $user = null);
 */
class TemplateVoterStub extends TemplateVoter
{
    use ClassAccessTrait;
}

class TemplateVoterTest extends BaseTestCase
{
    /** @var TemplateVoterStub */
    private $object = null;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new TemplateVoterStub();
    }

    public function testGetSupportedClasses()
    {
        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);

        $expected = [
            get_class($template),
        ];

        $this->assertEquals($expected, $this->object->getSupportedClasses());
    }

    public function testGetSupportedAttributes()
    {
        $expected = [];

        $this->assertEquals($expected, $this->object->getSupportedAttributes());
    }

    public function testUnsupportedAttribute()
    {
        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);

        $hubert = $this->findUser('hubert');

        $this->assertFalse($this->object->isGranted('UNKNOWN', $template, $hubert));
    }
}
