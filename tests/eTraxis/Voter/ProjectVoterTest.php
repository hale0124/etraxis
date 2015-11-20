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

use eTraxis\Entity\Project;
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
        /** @var Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $expected = [
            get_class($project),
        ];

        $this->assertEquals($expected, $this->object->getSupportedClasses());
    }

    public function testGetSupportedAttributes()
    {
        $expected = [
            Project::DELETE,
        ];

        $this->assertEquals($expected, $this->object->getSupportedAttributes());
    }

    public function testUnsupportedAttribute()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $this->assertFalse($this->object->isGranted('UNKNOWN', $project));
    }

    public function testDelete()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        /** @var Project $project */
        $empty = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'eTraxis 1.0']);

        $this->assertInstanceOf('eTraxis\Entity\Project', $project);
        $this->assertInstanceOf('eTraxis\Entity\Project', $empty);

        $this->assertFalse($this->object->isGranted(Project::DELETE, $project));
        $this->assertTrue($this->object->isGranted(Project::DELETE, $empty));
    }
}
