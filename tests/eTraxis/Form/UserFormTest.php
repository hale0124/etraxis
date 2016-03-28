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

class UserFormTest extends BaseTestCase
{
    public function testForm()
    {
        $user = $this->findUser('veins');

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(UserForm::class, $user);
        $view = $form->createView();

        $children = $view->children;

        self::assertEquals($children['username']->vars['data'], $user->getUsername());
        self::assertEquals($children['fullname']->vars['data'], $user->getFullname());
        self::assertEquals($children['email']->vars['data'], $user->getEmail());
        self::assertEquals($children['description']->vars['data'], $user->getDescription());
        self::assertEmpty($children['password']->vars['data']);
        self::assertEmpty($children['confirmation']->vars['data']);
        self::assertEquals($children['locale']->vars['data'], $user->getLocale());
        self::assertEquals($children['theme']->vars['data'], $user->getTheme());
        self::assertEquals($children['timezone']->vars['data'], $user->getTimezone());
        self::assertEquals((bool) $children['admin']->vars['data'], $user->isAdmin());
        self::assertEquals((bool) $children['disabled']->vars['data'], $user->isDisabled());
    }
}
