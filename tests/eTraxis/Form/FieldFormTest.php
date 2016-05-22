<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Form;

use eTraxis\Entity\Field;
use eTraxis\Tests\TransactionalTestCase;

class FieldFormTest extends TransactionalTestCase
{
    public function testNewForm()
    {
        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(FieldForm::class);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayHasKey('name', $children);
        self::assertArrayHasKey('type', $children);
        self::assertArrayHasKey('asNumber', $children);
        self::assertArrayHasKey('asDecimal', $children);
        self::assertArrayHasKey('asString', $children);
        self::assertArrayHasKey('asText', $children);
        self::assertArrayHasKey('asCheckbox', $children);
        self::assertArrayHasKey('asDate', $children);
        self::assertArrayHasKey('asDuration', $children);
        self::assertArrayHasKey('description', $children);
        self::assertArrayHasKey('required', $children);
    }

    public function testNumberForm()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Episode']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(FieldForm::class, $field);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayHasKey('name', $children);
        self::assertArrayNotHasKey('type', $children);
        self::assertArrayHasKey('asNumber', $children);
        self::assertArrayNotHasKey('asDecimal', $children);
        self::assertArrayNotHasKey('asString', $children);
        self::assertArrayNotHasKey('asText', $children);
        self::assertArrayNotHasKey('asCheckbox', $children);
        self::assertArrayNotHasKey('asDate', $children);
        self::assertArrayNotHasKey('asDuration', $children);
        self::assertArrayHasKey('description', $children);
        self::assertArrayHasKey('required', $children);
    }

    public function testDecimalForm()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'U.S. viewers']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(FieldForm::class, $field);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayHasKey('name', $children);
        self::assertArrayNotHasKey('type', $children);
        self::assertArrayNotHasKey('asNumber', $children);
        self::assertArrayHasKey('asDecimal', $children);
        self::assertArrayNotHasKey('asString', $children);
        self::assertArrayNotHasKey('asText', $children);
        self::assertArrayNotHasKey('asCheckbox', $children);
        self::assertArrayNotHasKey('asDate', $children);
        self::assertArrayNotHasKey('asDuration', $children);
        self::assertArrayHasKey('description', $children);
        self::assertArrayHasKey('required', $children);
    }

    public function testStringForm()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Production code']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(FieldForm::class, $field);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayHasKey('name', $children);
        self::assertArrayNotHasKey('type', $children);
        self::assertArrayNotHasKey('asNumber', $children);
        self::assertArrayNotHasKey('asDecimal', $children);
        self::assertArrayHasKey('asString', $children);
        self::assertArrayNotHasKey('asText', $children);
        self::assertArrayNotHasKey('asCheckbox', $children);
        self::assertArrayNotHasKey('asDate', $children);
        self::assertArrayNotHasKey('asDuration', $children);
        self::assertArrayHasKey('description', $children);
        self::assertArrayHasKey('required', $children);
    }

    public function testTextForm()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Plot']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(FieldForm::class, $field);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayHasKey('name', $children);
        self::assertArrayNotHasKey('type', $children);
        self::assertArrayNotHasKey('asNumber', $children);
        self::assertArrayNotHasKey('asDecimal', $children);
        self::assertArrayNotHasKey('asString', $children);
        self::assertArrayHasKey('asText', $children);
        self::assertArrayNotHasKey('asCheckbox', $children);
        self::assertArrayNotHasKey('asDate', $children);
        self::assertArrayNotHasKey('asDuration', $children);
        self::assertArrayHasKey('description', $children);
        self::assertArrayHasKey('required', $children);
    }

    public function testCheckboxForm()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Multipart']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(FieldForm::class, $field);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayHasKey('name', $children);
        self::assertArrayNotHasKey('type', $children);
        self::assertArrayNotHasKey('asNumber', $children);
        self::assertArrayNotHasKey('asDecimal', $children);
        self::assertArrayNotHasKey('asString', $children);
        self::assertArrayNotHasKey('asText', $children);
        self::assertArrayHasKey('asCheckbox', $children);
        self::assertArrayNotHasKey('asDate', $children);
        self::assertArrayNotHasKey('asDuration', $children);
        self::assertArrayHasKey('description', $children);
        self::assertArrayNotHasKey('required', $children);
    }

    public function testDateForm()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Original air date']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(FieldForm::class, $field);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayHasKey('name', $children);
        self::assertArrayNotHasKey('type', $children);
        self::assertArrayNotHasKey('asNumber', $children);
        self::assertArrayNotHasKey('asDecimal', $children);
        self::assertArrayNotHasKey('asString', $children);
        self::assertArrayNotHasKey('asText', $children);
        self::assertArrayNotHasKey('asCheckbox', $children);
        self::assertArrayHasKey('asDate', $children);
        self::assertArrayNotHasKey('asDuration', $children);
        self::assertArrayHasKey('description', $children);
        self::assertArrayHasKey('required', $children);
    }

    public function testDurationForm()
    {
        /** @var Field $field */
        $field = $this->doctrine->getRepository(Field::class)->findOneBy(['name' => 'Running time']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(FieldForm::class, $field);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayHasKey('name', $children);
        self::assertArrayNotHasKey('type', $children);
        self::assertArrayNotHasKey('asNumber', $children);
        self::assertArrayNotHasKey('asDecimal', $children);
        self::assertArrayNotHasKey('asString', $children);
        self::assertArrayNotHasKey('asText', $children);
        self::assertArrayNotHasKey('asCheckbox', $children);
        self::assertArrayNotHasKey('asDate', $children);
        self::assertArrayHasKey('asDuration', $children);
        self::assertArrayHasKey('description', $children);
        self::assertArrayHasKey('required', $children);
    }
}
