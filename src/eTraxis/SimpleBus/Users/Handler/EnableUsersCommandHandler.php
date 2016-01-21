<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users\Handler;

use eTraxis\SimpleBus\Users\EnableUsersCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
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
     * Enables specified accounts.
     *
     * @param   EnableUsersCommand $command
     */
    public function handle(EnableUsersCommand $command)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery('
            UPDATE eTraxis:User u
            SET u.isDisabled = :state
            WHERE u.id IN (:ids)
        ');

        $query->execute([
            'ids'   => $command->ids,
            'state' => 0,
        ]);
    }
}
