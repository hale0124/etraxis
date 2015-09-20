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

class AppearanceFormTest extends BaseTestCase
{
    public function testForm()
    {
        $user = $this->findUser('veins');

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(new AppearanceForm($this->translator), $user);
        $view = $form->createView();

        $children = $view->children;

        $this->assertEquals($children['locale']->vars['data'], $user->getLocale());
        $this->assertEquals($children['theme']->vars['data'], $user->getTheme());
        $this->assertEquals($children['timezone']->vars['data'], $user->getTimezone());
    }
}
