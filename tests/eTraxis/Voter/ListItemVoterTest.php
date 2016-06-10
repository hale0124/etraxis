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
use eTraxis\Tests\TransactionalTestCase;

class ListItemVoterTest extends TransactionalTestCase
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
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy(['text' => 'Season 1']);

        self::assertFalse($this->security->isGranted('UNKNOWN', $item));
    }

    public function testDelete()
    {
        $this->loginAs('hubert');

        /** @var ListItem $used */
        $used = $this->doctrine->getRepository(ListItem::class)->findOneBy(['text' => 'Season 1']);

        $item = new ListItem($used->getField());

        $item
            ->setValue(8)
            ->setText('Season 8')
        ;

        $this->doctrine->getManager()->persist($item);
        $this->doctrine->getManager()->flush();

        $unused = $this->doctrine->getRepository(ListItem::class)->findOneBy(['text' => 'Season 8']);

        self::assertInstanceOf(ListItem::class, $used);
        self::assertInstanceOf(ListItem::class, $unused);

        self::assertFalse($this->security->isGranted(ListItemVoter::DELETE, $used));
        self::assertTrue($this->security->isGranted(ListItemVoter::DELETE, $unused));
    }
}
