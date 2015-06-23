<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Security;

class InternalPasswordEncoderTest extends \PHPUnit_Framework_TestCase
{
    /** @var InternalPasswordEncoder */
    private $object = null;

    protected function setUp()
    {
        $this->object = new InternalPasswordEncoder();
    }

    public function testMaxLength()
    {
        $raw = str_pad(null, InternalPasswordEncoder::MAX_PASSWORD_LENGTH, '*');

        try {
            $this->object->encodePassword($raw);
        }
        catch (\Exception $exception) {
            $this->fail();
        }
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testTooLong()
    {
        $raw = str_pad(null, InternalPasswordEncoder::MAX_PASSWORD_LENGTH + 1, '*');

        $this->object->encodePassword($raw);
    }

    public function testEncoder()
    {
        $encoded = 'mzMEbtOdGC462vqQRa1nh9S7wyE=';
        $valid   = 'legacy';

        $this->assertEquals($encoded, $this->object->encodePassword($valid));
    }

    public function testLegacyApache()
    {
        $encoded = 'mzMEbtOdGC462vqQRa1nh9S7wyE=';
        $valid   = 'legacy';
        $invalid = 'wrong';

        $this->assertTrue($this->object->isPasswordValid($encoded, $valid));
        $this->assertFalse($this->object->isPasswordValid($encoded, $invalid));
    }

    public function testLegacyMd5()
    {
        $encoded = '8dbdda48fb8748d6746f1965824e966a';
        $valid   = 'simple';
        $invalid = 'bad';

        $this->assertTrue($this->object->isPasswordValid($encoded, $valid));
        $this->assertFalse($this->object->isPasswordValid($encoded, $invalid));
    }

    public function testInvalid()
    {
        $this->assertFalse($this->object->isPasswordValid(null, null));
    }
}
