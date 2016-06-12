<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Repository;

use eTraxis\Entity\Template;
use eTraxis\Tests\TransactionalTestCase;

class TemplatesRepositoryTest extends TransactionalTestCase
{
    public function testGetTemplates()
    {
        /** @var TemplatesRepository $repository */
        $repository = $this->doctrine->getRepository(Template::class);

        $user = $this->findUser('mwop');

        $result = $repository->getTemplates($user->getId());

        $templates = array_map(function (Template $template) {
            return $template->getName();
        }, $result);

        $expected = [
            'Futurama',
            'PSR',
        ];

        self::assertEquals($expected, $templates);
    }
}
