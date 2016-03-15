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

use eTraxis\Entity\ListItem;
use eTraxis\Tests\BaseTestCase;

class ListItemVoterTest extends BaseTestCase
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

        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy(['value' => 'Season 1']);

        $this->assertFalse($this->security->isGranted('UNKNOWN', $item));
    }

    public function testDelete()
    {
        $this->loginAs('hubert');

        /** @var ListItem $used */
        $used = $this->doctrine->getRepository(ListItem::class)->findOneBy(['value' => 'Season 1']);

        $item = new ListItem();

        $item
            ->setFieldId($used->getFieldId())
            ->setKey(8)
            ->setValue('Season 8')
            ->setField($used->getField())
        ;

        $this->doctrine->getManager()->persist($item);
        $this->doctrine->getManager()->flush();

        $unused = $this->doctrine->getRepository(ListItem::class)->findOneBy(['value' => 'Season 8']);

        $this->assertInstanceOf(ListItem::class, $used);
        $this->assertInstanceOf(ListItem::class, $unused);

        $this->assertFalse($this->security->isGranted(ListItem::DELETE, $used));
        $this->assertTrue($this->security->isGranted(ListItem::DELETE, $unused));
    }
}
