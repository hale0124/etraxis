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


namespace eTraxis\SimpleBus\CommandHandler\User;

use eTraxis\Exception\ResponseException;
use eTraxis\SimpleBus\Command\User\ListUsersCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Enumerates all accounts existing in eTraxis database.
 * Returns array of "User" entities in the "users" property.
 */
class ListUsersCommandHandler
{
    protected $security;
    protected $translator;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   AuthorizationCheckerInterface $security   Authorization checker.
     * @param   TranslatorInterface           $translator Translation service.
     * @param   RegistryInterface             $doctrine   Doctrine entity managers registry.
     */
    public function __construct(
        AuthorizationCheckerInterface $security,
        TranslatorInterface           $translator,
        RegistryInterface             $doctrine)
    {
        $this->security   = $security;
        $this->translator = $translator;
        $this->doctrine   = $doctrine;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ListUsersCommand $command)
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new ResponseException('You don\'t have sufficient permissions to access this resource.', Response::HTTP_FORBIDDEN);
        }

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:User');

        $query = $repository->createQueryBuilder('u');

        // Search.
        if (array_key_exists('value', $command->search)) {

            $query
                ->where('u.username LIKE :search')
                ->orWhere('u.fullname LIKE :search')
                ->orWhere('u.email LIKE :search')
                ->orWhere('u.description LIKE :search')
                ->setParameter('search', "%{$command->search['value']}%")
            ;
        }

        // Order.
        foreach ($command->order as $order) {

            $map = [
                0 => 'u.username',
                1 => 'u.fullname',
                2 => 'u.email',
                3 => 'u.isAdmin',
                4 => 'u.description',
            ];

            $query->addOrderBy($map[$order['column']], $order['dir']);
        }

        /** @var \eTraxis\Model\User[] $entities */
        $entities = $query->getQuery()->getResult();

        $command->total = count($entities);

        $users = [];

        for ($i = 0; $i < $command->length || $command->length == -1; $i++) {

            $index = $i + $command->start;

            if ($index >= $command->total) {
                break;
            }

            $entity = $entities[$index];

            $users[] = [
                $entity->getUsername(),
                $entity->getFullname(),
                $entity->getEmail(),
                $this->translator->trans($entity->isAdmin() ? 'role.administrator' : 'role.user'),
                $entity->getDescription(),
            ];
        }

        $command->users = $users;
    }
}
