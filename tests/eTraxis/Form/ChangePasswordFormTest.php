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

class ChangePasswordFormTest extends TransactionalTestCase
{
    public function testForm()
    {
        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(ChangePasswordForm::class);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayHasKey('current_password', $children);
        self::assertArrayHasKey('new_password', $children);
        self::assertArrayHasKey('confirmation', $children);
    }
}
