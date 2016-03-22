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
use eTraxis\Tests\BaseTestCase;

class FieldFormTest extends BaseTestCase
{
    public function testNewForm()
    {
        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(FieldForm::class);
        $view = $form->createView();

        $children = $view->children;

        $this->assertArrayHasKey('name', $children);
        $this->assertArrayHasKey('type', $children);
        $this->assertArrayHasKey('asNumber', $children);
        $this->assertArrayHasKey('asDecimal', $children);
        $this->assertArrayHasKey('asString', $children);
        $this->assertArrayHasKey('asText', $children);
        $this->assertArrayHasKey('asCheckbox', $children);
        $this->assertArrayHasKey('asDate', $children);
        $this->assertArrayHasKey('asDuration', $children);
        $this->assertArrayHasKey('description', $children);
        $this->assertArrayHasKey('required', $children);
        $this->assertArrayHasKey('guestAccess', $children);
        $this->assertArrayHasKey('showInEmails', $children);
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

        $this->assertArrayHasKey('name', $children);
        $this->assertArrayNotHasKey('type', $children);
        $this->assertArrayHasKey('asNumber', $children);
        $this->assertArrayNotHasKey('asDecimal', $children);
        $this->assertArrayNotHasKey('asString', $children);
        $this->assertArrayNotHasKey('asText', $children);
        $this->assertArrayNotHasKey('asCheckbox', $children);
        $this->assertArrayNotHasKey('asDate', $children);
        $this->assertArrayNotHasKey('asDuration', $children);
        $this->assertArrayHasKey('description', $children);
        $this->assertArrayHasKey('required', $children);
        $this->assertArrayHasKey('guestAccess', $children);
        $this->assertArrayHasKey('showInEmails', $children);
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

        $this->assertArrayHasKey('name', $children);
        $this->assertArrayNotHasKey('type', $children);
        $this->assertArrayNotHasKey('asNumber', $children);
        $this->assertArrayHasKey('asDecimal', $children);
        $this->assertArrayNotHasKey('asString', $children);
        $this->assertArrayNotHasKey('asText', $children);
        $this->assertArrayNotHasKey('asCheckbox', $children);
        $this->assertArrayNotHasKey('asDate', $children);
        $this->assertArrayNotHasKey('asDuration', $children);
        $this->assertArrayHasKey('description', $children);
        $this->assertArrayHasKey('required', $children);
        $this->assertArrayHasKey('guestAccess', $children);
        $this->assertArrayHasKey('showInEmails', $children);
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

        $this->assertArrayHasKey('name', $children);
        $this->assertArrayNotHasKey('type', $children);
        $this->assertArrayNotHasKey('asNumber', $children);
        $this->assertArrayNotHasKey('asDecimal', $children);
        $this->assertArrayHasKey('asString', $children);
        $this->assertArrayNotHasKey('asText', $children);
        $this->assertArrayNotHasKey('asCheckbox', $children);
        $this->assertArrayNotHasKey('asDate', $children);
        $this->assertArrayNotHasKey('asDuration', $children);
        $this->assertArrayHasKey('description', $children);
        $this->assertArrayHasKey('required', $children);
        $this->assertArrayHasKey('guestAccess', $children);
        $this->assertArrayHasKey('showInEmails', $children);
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

        $this->assertArrayHasKey('name', $children);
        $this->assertArrayNotHasKey('type', $children);
        $this->assertArrayNotHasKey('asNumber', $children);
        $this->assertArrayNotHasKey('asDecimal', $children);
        $this->assertArrayNotHasKey('asString', $children);
        $this->assertArrayHasKey('asText', $children);
        $this->assertArrayNotHasKey('asCheckbox', $children);
        $this->assertArrayNotHasKey('asDate', $children);
        $this->assertArrayNotHasKey('asDuration', $children);
        $this->assertArrayHasKey('description', $children);
        $this->assertArrayHasKey('required', $children);
        $this->assertArrayHasKey('guestAccess', $children);
        $this->assertArrayHasKey('showInEmails', $children);
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

        $this->assertArrayHasKey('name', $children);
        $this->assertArrayNotHasKey('type', $children);
        $this->assertArrayNotHasKey('asNumber', $children);
        $this->assertArrayNotHasKey('asDecimal', $children);
        $this->assertArrayNotHasKey('asString', $children);
        $this->assertArrayNotHasKey('asText', $children);
        $this->assertArrayHasKey('asCheckbox', $children);
        $this->assertArrayNotHasKey('asDate', $children);
        $this->assertArrayNotHasKey('asDuration', $children);
        $this->assertArrayHasKey('description', $children);
        $this->assertArrayNotHasKey('required', $children);
        $this->assertArrayHasKey('guestAccess', $children);
        $this->assertArrayHasKey('showInEmails', $children);
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

        $this->assertArrayHasKey('name', $children);
        $this->assertArrayNotHasKey('type', $children);
        $this->assertArrayNotHasKey('asNumber', $children);
        $this->assertArrayNotHasKey('asDecimal', $children);
        $this->assertArrayNotHasKey('asString', $children);
        $this->assertArrayNotHasKey('asText', $children);
        $this->assertArrayNotHasKey('asCheckbox', $children);
        $this->assertArrayHasKey('asDate', $children);
        $this->assertArrayNotHasKey('asDuration', $children);
        $this->assertArrayHasKey('description', $children);
        $this->assertArrayHasKey('required', $children);
        $this->assertArrayHasKey('guestAccess', $children);
        $this->assertArrayHasKey('showInEmails', $children);
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

        $this->assertArrayHasKey('name', $children);
        $this->assertArrayNotHasKey('type', $children);
        $this->assertArrayNotHasKey('asNumber', $children);
        $this->assertArrayNotHasKey('asDecimal', $children);
        $this->assertArrayNotHasKey('asString', $children);
        $this->assertArrayNotHasKey('asText', $children);
        $this->assertArrayNotHasKey('asCheckbox', $children);
        $this->assertArrayNotHasKey('asDate', $children);
        $this->assertArrayHasKey('asDuration', $children);
        $this->assertArrayHasKey('description', $children);
        $this->assertArrayHasKey('required', $children);
        $this->assertArrayHasKey('guestAccess', $children);
        $this->assertArrayHasKey('showInEmails', $children);
    }
}
