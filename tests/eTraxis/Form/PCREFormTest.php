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

class PCREFormTest extends TransactionalTestCase
{
    public function testForm()
    {
        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(PCREForm::class);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayHasKey('check', $children);
        self::assertArrayHasKey('search', $children);
        self::assertArrayHasKey('replace', $children);
    }
}
