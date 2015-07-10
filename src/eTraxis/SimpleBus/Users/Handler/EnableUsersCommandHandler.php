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

namespace eTraxis\SimpleBus\Users\Handler;

use eTraxis\SimpleBus\Users\EnableUsersCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Enables specified accounts.
 */
class EnableUsersCommandHandler
{
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(EnableUsersCommand $command)
    {
        $em = $this->doctrine->getEntityManager();

        $query = $em->createQuery('
            UPDATE eTraxis:User u
            SET u.isDisabled = :state
            WHERE u.id IN (:ids)
        ');

        $query->execute([
            'ids'   => $command->ids,
            'state' => false,
        ]);
    }
}
