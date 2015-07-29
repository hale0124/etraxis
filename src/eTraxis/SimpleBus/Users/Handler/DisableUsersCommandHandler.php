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

use eTraxis\SimpleBus\Users\DisableUsersCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Disables specified accounts.
 */
class DisableUsersCommandHandler
{
    protected $doctrine;
    protected $token_storage;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface     $doctrine
     * @param   TokenStorageInterface $token_storage
     */
    public function __construct(RegistryInterface $doctrine, TokenStorageInterface $token_storage)
    {
        $this->doctrine      = $doctrine;
        $this->token_storage = $token_storage;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DisableUsersCommand $command)
    {
        /** @var \eTraxis\Entity\User $user */
        $user = $this->token_storage->getToken()->getUser();

        // Don't disable yourself.
        $ids = array_filter($command->ids, function ($id) use ($user) {
            return $id != $user->getId();
        });

        $em = $this->doctrine->getEntityManager();

        $query = $em->createQuery('
            UPDATE eTraxis:User u
            SET u.isDisabled = :state
            WHERE u.id IN (:ids)
        ');

        $query->execute([
            'ids'   => $ids,
            'state' => 1,
        ]);
    }
}
