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

use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class TemplateVoterTest extends BaseTestCase
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

        /** @var Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->findOneBy(['name' => 'Delivery']);

        $this->assertFalse($this->security->isGranted('UNKNOWN', $template));
    }

    public function testDelete()
    {
        $this->loginAs('hubert');

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

        $this->assertFalse($this->security->isGranted(Template::DELETE, $template));
        $this->assertTrue($this->security->isGranted(Template::DELETE, $empty));
    }
}
