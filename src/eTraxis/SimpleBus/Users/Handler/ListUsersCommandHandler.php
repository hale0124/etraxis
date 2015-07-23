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

use eTraxis\SimpleBus\Users\ListUsersCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Enumerates all accounts existing in eTraxis database.
 * Returns array of "User" entities in the "users" property.
 */
class ListUsersCommandHandler
{
    protected $translator;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   TranslatorInterface $translator Translation service.
     * @param   RegistryInterface   $doctrine   Doctrine entity managers registry.
     */
    public function __construct(TranslatorInterface $translator, RegistryInterface $doctrine)
    {
        $this->translator = $translator;
        $this->doctrine   = $doctrine;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ListUsersCommand $command)
    {
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:User');

        $query = $repository->createQueryBuilder('u')->select('COUNT(u.id)');

        $command->result['total'] = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('u');

        // Search.
        if ($command->search) {

            $query
                ->where('u.username LIKE :search')
                ->orWhere('u.fullname LIKE :search')
                ->orWhere('u.email LIKE :search')
                ->orWhere('u.description LIKE :search')
                ->setParameter('search', "%{$command->search}%")
            ;
        }

        // Order.
        foreach ($command->order as $order) {

            $map = [
                0 => 'u.id',
                1 => 'u.username',
                2 => 'u.fullname',
                3 => 'u.email',
                4 => 'u.isAdmin',
                5 => 'u.description',
            ];

            $query->addOrderBy($map[$order['column']], $order['dir']);
        }

        /** @var \eTraxis\Entity\User[] $entities */
        $entities = $query->getQuery()->getResult();

        $command->result['filtered'] = count($entities);
        $command->result['users']    = [];

        for ($i = 0; $i < $command->length || $command->length == -1; $i++) {

            $index = $i + $command->start;

            if ($index >= $command->result['filtered']) {
                break;
            }

            $entity = $entities[$index];

            if (!$entity->isAccountNonLocked()) {
                $color = 'red';
            }
            elseif ($entity->isDisabled()) {
                $color = 'gray';
            }
            else {
                $color = null;
            }

            $command->result['users'][] = [
                $entity->getId(),
                $entity->getUsername(),
                $entity->getFullname(),
                $entity->getEmail(),
                $this->translator->trans($entity->isAdmin() ? 'role.administrator' : 'role.user'),
                $entity->getAuthenticationSource(),
                $entity->getDescription(),
                'DT_RowAttr'  => ['data-id' => $entity->getId()],
                'DT_RowClass' => $color,
            ];
        }
    }
}
