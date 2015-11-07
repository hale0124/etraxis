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

use eTraxis\Entity\Template;
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

        /** @var \eTraxis\Repository\IssuesRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:Issue');

        $this->object = new TemplateVoterStub($repository);
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
        $expected = [
            TemplateVoter::DELETE,
        ];

        $this->assertEquals($expected, $this->object->getSupportedAttributes());
    }

    public function testUnsupportedAttribute()
    {
        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);

        $hubert = $this->findUser('hubert');

        $this->assertFalse($this->object->isGranted('UNKNOWN', $template, $hubert));
    }

    public function testDelete()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $template = new Template();

        $template
            ->setName('Issue')
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
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);

        /** @var Template $empty */
        $empty = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Issue']);

        $this->assertInstanceOf('eTraxis\Entity\Template', $template);
        $this->assertInstanceOf('eTraxis\Entity\Template', $empty);

        $fry    = $this->findUser('fry');
        $hubert = $this->findUser('hubert');

        $this->assertFalse($this->object->isGranted(TemplateVoter::DELETE, $template));
        $this->assertFalse($this->object->isGranted(TemplateVoter::DELETE, $template, $hubert));
        $this->assertTrue($this->object->isGranted(TemplateVoter::DELETE, $empty, $hubert));
        $this->assertFalse($this->object->isGranted(TemplateVoter::DELETE, $empty, $fry));
    }
}
