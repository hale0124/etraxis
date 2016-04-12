<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class AbstractUserTest extends \PHPUnit_Framework_TestCase
{
    /** @var AbstractUser */
    private $object;

    protected function setUp()
    {
        $this->object = new User();
    }

    public function testGetSalt()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        self::assertNull($this->object->getSalt());
    }

    public function testEraseCredentials()
    {
        $this->object->eraseCredentials();
    }

    public function testIsAccountNonExpired()
    {
        self::assertTrue($this->object->isAccountNonExpired());
    }

    public function testIsCredentialsNonExpired()
    {
        self::assertTrue($this->object->isCredentialsNonExpired());
    }
}
