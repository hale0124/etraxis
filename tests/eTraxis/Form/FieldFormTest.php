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

use eTraxis\Tests\BaseTestCase;

class FieldFormTest extends BaseTestCase
{
    public function testForm()
    {
        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(FieldForm::class);
        $view = $form->createView();

        $children = $view->children;

        $this->assertArrayHasKey('name', $children);
        $this->assertArrayHasKey('type', $children);
        $this->assertArrayHasKey('number', $children);
        $this->assertArrayHasKey('decimal', $children);
        $this->assertArrayHasKey('string', $children);
        $this->assertArrayHasKey('text', $children);
        $this->assertArrayHasKey('checkbox', $children);
        $this->assertArrayHasKey('date', $children);
        $this->assertArrayHasKey('duration', $children);
        $this->assertArrayHasKey('description', $children);
        $this->assertArrayHasKey('required', $children);
        $this->assertArrayHasKey('guestAccess', $children);
        $this->assertArrayHasKey('showInEmails', $children);
    }
}
