<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle;

use Doctrine\ORM\Query;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        // PostgreSQL and Oracle treat NULLs as greatest values.
        if (in_array($this->container->getParameter('database_driver'), ['pdo_pgsql', 'oci8'])) {

            $em->getConfiguration()->setDefaultQueryHint(
                Query::HINT_CUSTOM_OUTPUT_WALKER,
                '\eTraxis\Doctrine\SortableNullsWalker'
            );
        }
    }
}
