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

use AltrEgo\AltrEgo;

class UserSettingsTest extends \PHPUnit_Framework_TestCase
{
    /** @var UserSettings|array */
    private $object;

    protected function setUp()
    {
        $this->object = new UserSettings();
    }

    public function testLocale()
    {
        $expected = 'ru';
        $this->object->setLocale($expected);
        self::assertEquals($expected, $this->object->getLocale());
    }

    public function testGetLocaleFallback()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected       = 'en_US';
        $object->locale = 0;
        self::assertEquals($expected, $this->object->getLocale());
    }

    public function testSetLocaleFallback()
    {
        $expected = 'en_US';
        $this->object->setLocale('xx-XX');
        self::assertEquals($expected, $this->object->getLocale());
    }

    public function testTheme()
    {
        $expected = 'emerald';
        $this->object->setTheme($expected);
        self::assertEquals($expected, $this->object->getTheme());
    }

    public function testGetThemeFallback()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected      = 'azure';
        $object->theme = 'unsupported';
        self::assertEquals($expected, $this->object->getTheme());
    }

    public function testSetThemeFallback()
    {
        $expected = 'azure';
        $this->object->setTheme('unsupported');
        self::assertEquals($expected, $this->object->getTheme());
    }

    public function testTimezone()
    {
        $expected = 'Pacific/Auckland';
        $this->object->setTimezone($expected);
        self::assertEquals($expected, $this->object->getTimezone());
    }

    public function testTimezoneUnsupported()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected         = 'UTC';
        $object->timezone = PHP_INT_MAX;

        self::assertEquals(PHP_INT_MAX, $object->timezone);
        self::assertEquals($expected, $this->object->getTimezone());
    }

    public function testArrayAccess()
    {
        /** @noinspection UnSafeIsSetOverArrayInspection */
        self::assertTrue(isset($this->object['locale']));
        /** @noinspection UnSafeIsSetOverArrayInspection */
        self::assertTrue(isset($this->object['theme']));
        /** @noinspection UnSafeIsSetOverArrayInspection */
        self::assertTrue(isset($this->object['timezone']));
        /** @noinspection UnSafeIsSetOverArrayInspection */
        self::assertFalse(isset($this->object['unknown']));

        $this->object['locale'] = 'ru';
        self::assertEquals('ru', $this->object['locale']);

        unset($this->object['locale']);
        self::assertEquals('en_US', $this->object['locale']);
    }
}
