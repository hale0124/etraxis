<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Security;

use eTraxis\Tests\BaseTestCase;

class InternalPasswordEncoderTest extends BaseTestCase
{
    /** @var InternalPasswordEncoder */
    private $object = null;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new InternalPasswordEncoder($this->translator, 6);
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

    public function testMinLength()
    {
        $raw = str_pad(null, 6, '*');

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

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testTooShort()
    {
        $raw = str_pad(null, 5, '*');

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
        $invalid = 'invalid';

        $this->assertTrue($this->object->isPasswordValid($encoded, $valid));
        $this->assertFalse($this->object->isPasswordValid($encoded, $invalid));
    }

    public function testLegacyMd5()
    {
        $encoded = '8dbdda48fb8748d6746f1965824e966a';
        $valid   = 'simple';
        $invalid = 'invalid';

        $this->assertTrue($this->object->isPasswordValid($encoded, $valid));
        $this->assertFalse($this->object->isPasswordValid($encoded, $invalid));
    }

    public function testInvalid()
    {
        $this->assertFalse($this->object->isPasswordValid(null, null));
    }
}
