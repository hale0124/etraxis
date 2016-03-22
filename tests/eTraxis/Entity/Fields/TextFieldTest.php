<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Fields;

use eTraxis\Entity\Field;
use eTraxis\Entity\TextValue;
use eTraxis\Tests\BaseTestCase;

class TextFieldTest extends BaseTestCase
{
    /** @var Field */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Field();

        /** @noinspection PhpParamsInspection */
        $this->object
            ->setType(Field::TYPE_TEXT)
            ->setTextValuesRepository($this->doctrine->getRepository(TextValue::class))
        ;
    }

    public function testSupportedKeys()
    {
        $expected = ['maxLength', 'defaultValue'];

        $field = $this->object->asText();

        $reflection = new \ReflectionObject($field);
        $method     = $reflection->getMethod('getSupportedKeys');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($field, []);

        $this->assertCount(count($expected), $actual);

        foreach ($expected as $key) {
            $this->assertContains($key, $actual);
        }
    }

    public function testMaxLength()
    {
        $field = $this->object->asText();

        $field->setMaxLength(1000);
        $this->assertEquals(1000, $field->getMaxLength());
        $this->assertEquals(1000, $this->object->getParameter1());

        $field->setMaxLength(0);
        $this->assertEquals(TextField::MIN_LENGTH, $field->getMaxLength());
        $this->assertEquals(TextField::MIN_LENGTH, $this->object->getParameter1());

        $field->setMaxLength(PHP_INT_MAX);
        $this->assertEquals(TextField::MAX_LENGTH, $field->getMaxLength());
        $this->assertEquals(TextField::MAX_LENGTH, $this->object->getParameter1());
    }

    public function testDefaultValue()
    {
        $field = $this->object->asText();

        $tv = 'Pizza delivery boy Philip J. Fry accidentally stumbles into a cryogenic freezer on December 31, 1999, and '
            . 'awakens one thousand years in the future on New Year\'s Eve, 2999. He meets a one-eyed career counselor named '
            . 'Leela, who tries to assign him an eternal career as a delivery boy. Fry dislikes the idea and escapes into '
            . 'the city where he meets Bender, an alcoholic robot who has also abandoned his job, and the two become friends. '
            . 'Fry soon becomes depressed that he can never return to his old life and surrenders to Leela, but she realizes '
            . 'that she also hates her job and quits. Now fugitives, the three visit Fry\'s descendant, Professor Farnsworth, '
            . 'who helps them escape from the police on his intergalactic spaceship as the world celebrates the year 3000. '
            . 'Farnsworth hires the three to become his crew for his intergalactic delivery service, Planet Express, with Fry '
            . 'becoming a delivery boy.';

        /** @var \eTraxis\Repository\TextValuesRepository $repository */
        $repository = $this->doctrine->getRepository(TextValue::class);

        /** @var TextValue $value */
        $value = $repository->findOneBy(['value' => $tv]);

        $field->setDefaultValue($tv);
        $this->assertEquals($tv, $field->getDefaultValue());
        $this->assertEquals($value->getId(), $this->object->getDefaultValue());

        $huge = str_pad(null, 5000);
        $trim = str_pad(null, TextField::MAX_LENGTH);

        $field->setDefaultValue($huge);
        $this->assertEquals($trim, $field->getDefaultValue());

        $field->setDefaultValue(null);
        $this->assertNull($field->getDefaultValue());
        $this->assertNull($this->object->getDefaultValue());
    }
}
