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

use eTraxis\Tests\TransactionalTestCase;

class AppearanceFormTest extends TransactionalTestCase
{
    public function testForm()
    {
        $user = $this->findUser('veins');

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(AppearanceForm::class, $user);
        $view = $form->createView();

        $children = $view->children;

        self::assertEquals($children['locale']->vars['data'], $user->getLocale());
        self::assertEquals($children['theme']->vars['data'], $user->getTheme());
        self::assertEquals($children['timezone']->vars['data'], $user->getTimezone());
    }
}
