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

class ResetPasswordFormTest extends BaseTestCase
{
    public function testForm()
    {
        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(new ResetPasswordForm());
        $view = $form->createView();

        $children = $view->children;

        $this->assertArrayHasKey('password', $children);
        $this->assertArrayHasKey('confirmation', $children);
    }
}
