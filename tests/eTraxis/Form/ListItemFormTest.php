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

use eTraxis\Entity\ListItem;
use eTraxis\Tests\TransactionalTestCase;

class ListItemFormTest extends TransactionalTestCase
{
    public function testNewForm()
    {
        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(ListItemForm::class);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayHasKey('key', $children);
        self::assertArrayHasKey('value', $children);
    }

    public function testEditForm()
    {
        /** @var ListItem $item */
        $item = $this->doctrine->getRepository(ListItem::class)->findOneBy(['value' => 'Season 1']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(ListItemForm::class, $item);
        $view = $form->createView();

        $children = $view->children;

        self::assertArrayNotHasKey('key', $children);
        self::assertArrayHasKey('value', $children);
        self::assertEquals($children['value']->vars['data'], $item->getValue());
    }
}
