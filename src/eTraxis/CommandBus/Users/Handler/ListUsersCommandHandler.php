<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users\Handler;

use eTraxis\CommandBus\Users\ListUsersCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Command handler.
 */
class ListUsersCommandHandler
{
    const COLUMN_ID             = 0;
    const COLUMN_USERNAME       = 1;
    const COLUMN_FULLNAME       = 2;
    const COLUMN_EMAIL          = 3;
    const COLUMN_PERMISSIONS    = 4;
    const COLUMN_AUTHENTICATION = 5;
    const COLUMN_DESCRIPTION    = 6;

    protected $translator;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   TranslatorInterface $translator
     * @param   RegistryInterface   $doctrine
     */
    public function __construct(TranslatorInterface $translator, RegistryInterface $doctrine)
    {
        $this->translator = $translator;
        $this->doctrine   = $doctrine;
    }

    /**
     * Enumerates all accounts existing in eTraxis database.
     *
     * @param   ListUsersCommand $command
     *
     * @return  array
     */
    public function handle(ListUsersCommand $command)
    {
        $result = [
            'users'    => [],
            'filtered' => 0,
            'total'    => 0,
        ];

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:User');

        $query = $repository->createQueryBuilder('u')->select('COUNT(u.id)');

        $result['total'] = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('u');

        // Search.
        if ($command->search) {

            $conditions = [
                'LOWER(u.username) LIKE :search',
                'LOWER(u.fullname) LIKE :search',
                'LOWER(u.email) LIKE :search',
                'LOWER(u.description) LIKE :search',
            ];

            $query
                ->where('(' . implode(' OR ', $conditions) . ')')
                ->setParameter('search', mb_strtolower("%{$command->search}%"))
            ;
        }

        // Filter by columns.
        foreach ($command->columns as $column) {

            if (!$column['search']['value']) {
                continue;
            }

            $value = mb_strtolower($column['search']['value']);

            switch ($column['data']) {

                case self::COLUMN_USERNAME:

                    $query
                        ->andWhere('LOWER(u.username) LIKE :username')
                        ->setParameter('username', "%{$value}%")
                    ;

                    break;

                case self::COLUMN_FULLNAME:

                    $query
                        ->andWhere('LOWER(u.fullname) LIKE :fullname')
                        ->setParameter('fullname', "%{$value}%")
                    ;

                    break;

                case self::COLUMN_EMAIL:

                    $query
                        ->andWhere('LOWER(u.email) LIKE :email')
                        ->setParameter('email', "%{$value}%")
                    ;

                    break;

                case self::COLUMN_PERMISSIONS:

                    if ($value == 'admin') {
                        $query->andWhere('u.isAdmin <> 0');
                    }
                    elseif ($value == 'user') {
                        $query->andWhere('u.isAdmin = 0');
                    }

                    break;

                case self::COLUMN_AUTHENTICATION:

                    if ($value == 'etraxis') {
                        $query->andWhere('u.isLdap = 0');
                    }
                    elseif ($value == 'ldap') {
                        $query->andWhere('u.isLdap <> 0');
                    }

                    break;

                case self::COLUMN_DESCRIPTION:

                    $query
                        ->andWhere('LOWER(u.description) LIKE :description')
                        ->setParameter('description', "%{$value}%")
                    ;

                    break;
            }
        }

        // Order.
        foreach ($command->order as $order) {

            $map = [
                self::COLUMN_ID             => 'u.id',
                self::COLUMN_USERNAME       => 'u.username',
                self::COLUMN_FULLNAME       => 'u.fullname',
                self::COLUMN_EMAIL          => 'u.email',
                self::COLUMN_PERMISSIONS    => 'u.isAdmin',
                self::COLUMN_AUTHENTICATION => 'u.isLdap',
                self::COLUMN_DESCRIPTION    => 'u.description',
            ];

            $query->addOrderBy($map[$order['column']], $order['dir']);
        }

        /** @var \eTraxis\Entity\User[] $entities */
        $entities = $query->getQuery()->getResult();

        $result['filtered'] = count($entities);

        for ($i = 0; $i < $command->length || $command->length == -1; $i++) {

            $index = $i + $command->start;

            if ($index >= $result['filtered']) {
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

            $result['users'][] = [
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

        return $result;
    }
}
