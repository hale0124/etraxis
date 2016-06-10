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
use eTraxis\Tests\TransactionalTestCase;

class ProjectVoterTest extends TransactionalTestCase
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

        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        self::assertFalse($this->security->isGranted('UNKNOWN', $project));
    }

    public function testDelete()
    {
        $this->loginAs('hubert');

        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        /** @var Project $project */
        $empty = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'eTraxis 1.0']);

        self::assertInstanceOf(Project::class, $project);
        self::assertInstanceOf(Project::class, $empty);

        self::assertFalse($this->security->isGranted(ProjectVoter::DELETE, $project));
        self::assertTrue($this->security->isGranted(ProjectVoter::DELETE, $empty));
    }
}
