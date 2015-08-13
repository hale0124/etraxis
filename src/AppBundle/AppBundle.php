<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle;

use AppBundle\DependencyInjection\CommandBusCompilerPass;
use Doctrine\ORM\Query;
use eTraxis\Collection\DatabasePlatform;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $platforms = [
            DatabasePlatform::POSTGRESQL,
            DatabasePlatform::ORACLE,
        ];

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        // PostgreSQL and Oracle treat NULLs as greatest values.
        if (in_array($em->getConnection()->getDatabasePlatform()->getName(), $platforms)) {

            $em->getConfiguration()->setDefaultQueryHint(
                Query::HINT_CUSTOM_OUTPUT_WALKER,
                '\eTraxis\Doctrine\SortableNullsWalker'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CommandBusCompilerPass());
    }
}
