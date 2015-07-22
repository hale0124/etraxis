<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Form;

use eTraxis\Tests\BaseTestCase;

class UserFormTest extends BaseTestCase
{
    public function testForm()
    {
        $user = $this->findUser('veins');

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(new UserForm(), $user);
        $view = $form->createView();

        $children = $view->children;

        $this->assertEquals($children['username']->vars['data'], $user->getUsername());
        $this->assertEquals($children['fullname']->vars['data'], $user->getFullname());
        $this->assertEquals($children['email']->vars['data'], $user->getEmail());
        $this->assertEquals($children['description']->vars['data'], $user->getDescription());
        $this->assertEmpty($children['password']->vars['data']);
        $this->assertEmpty($children['confirmation']->vars['data']);
        $this->assertEquals(boolval($children['admin']->vars['data']), $user->isAdmin());
        $this->assertEquals(boolval($children['disabled']->vars['data']), $user->isDisabled());
    }
}
