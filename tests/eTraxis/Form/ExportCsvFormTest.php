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

class ExportCsvFormTest extends BaseTestCase
{
    public function testForm()
    {
        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        /** @var \Symfony\Component\Translation\TranslatorInterface $translator */
        $translator = $this->client->getContainer()->get('translator');

        $form = $factory->create(new ExportCsvForm($translator));
        $view = $form->createView();

        $children = $view->children;

        $this->assertArrayHasKey('filename', $children);
        $this->assertArrayHasKey('delimiter', $children);
        $this->assertArrayHasKey('encoding', $children);
        $this->assertArrayHasKey('tail', $children);
    }
}
