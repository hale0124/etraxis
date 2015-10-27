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
class ProjectVoterStub extends ProjectVoter
{
    use ClassAccessTrait;
}

class ProjectVoterTest extends BaseTestCase
{
    /** @var ProjectVoterStub */
    private $object = null;

    protected function setUp()
    {
        parent::setUp();

        /** @var \eTraxis\Repository\IssuesRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:Issue');

        $this->object = new ProjectVoterStub($repository);
    }

    public function testGetSupportedClasses()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $expected = [
            get_class($project),
        ];

        $this->assertEquals($expected, $this->object->getSupportedClasses());
    }

    public function testGetSupportedAttributes()
    {
        $expected = [
            ProjectVoter::DELETE,
        ];

        $this->assertEquals($expected, $this->object->getSupportedAttributes());
    }

    public function testUnsupportedAttribute()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $hubert = $this->findUser('hubert');

        $this->assertFalse($this->object->isGranted('UNKNOWN', $project, $hubert));
    }

    public function testDelete()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        /** @var \eTraxis\Entity\Project $project */
        $empty = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'eTraxis 1.0']);

        $this->assertInstanceOf('eTraxis\Entity\Project', $project);
        $this->assertInstanceOf('eTraxis\Entity\Project', $empty);

        $fry    = $this->findUser('fry');
        $hubert = $this->findUser('hubert');

        $this->assertFalse($this->object->isGranted(ProjectVoter::DELETE, $project));
        $this->assertFalse($this->object->isGranted(ProjectVoter::DELETE, $project, $hubert));
        $this->assertTrue($this->object->isGranted(ProjectVoter::DELETE, $empty, $hubert));
        $this->assertFalse($this->object->isGranted(ProjectVoter::DELETE, $empty, $fry));
    }
}
