<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\DataTables;

use DataTables\DataTableHandlerInterface;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\User;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Enumerates all accounts existing in eTraxis database.
 */
class UsersDataTable implements DataTableHandlerInterface
{
    const COLUMN_ID             = 0;
    const COLUMN_USERNAME       = 1;
    const COLUMN_FULLNAME       = 2;
    const COLUMN_EMAIL          = 3;
    const COLUMN_PERMISSIONS    = 4;
    const COLUMN_AUTHENTICATION = 5;
    const COLUMN_DESCRIPTION    = 6;

    protected $manager;
    protected $translator;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     * @param   TranslatorInterface    $translator
     */
    public function __construct(EntityManagerInterface $manager, TranslatorInterface $translator)
    {
        $this->manager    = $manager;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        $results = new DataTableResults();

        $query = $this->manager->createQueryBuilder();

        $query
            ->select('u')
            ->from(User::class, 'u')
        ;

        // Search.
        if ($request->search->value) {

            $conditions = [
                'LOWER(u.username) LIKE :search',
                'LOWER(u.fullname) LIKE :search',
                'LOWER(u.email) LIKE :search',
                'LOWER(u.description) LIKE :search',
            ];

            $query
                ->where('(' . implode(' OR ', $conditions) . ')')
                ->setParameter('search', mb_strtolower("%{$request->search->value}%"))
            ;
        }

        // Filter by columns.
        foreach ($request->columns as $column) {

            if (!$column->search->value) {
                continue;
            }

            $value = mb_strtolower($column->search->value);

            switch ($column->data) {

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

                    if ($value === 'admin') {
                        $query->andWhere('u.isAdmin <> 0');
                    }
                    elseif ($value === 'user') {
                        $query->andWhere('u.isAdmin = 0');
                    }

                    break;

                case self::COLUMN_AUTHENTICATION:

                    if ($value === 'etraxis') {
                        $query->andWhere('u.isLdap = 0');
                    }
                    elseif ($value === 'ldap') {
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

        // Total number of entries.
        $queryTotal = $this->manager->createQueryBuilder();
        $queryTotal->select('COUNT(u.id)');
        $queryTotal->from(User::class, 'u');
        $results->recordsTotal = (int) $queryTotal->getQuery()->getSingleScalarResult();

        // Filtered number of entries.
        $queryFiltered = clone $query;
        $queryFiltered->select('COUNT(u.id)');
        $results->recordsFiltered = (int) $queryFiltered->getQuery()->getSingleScalarResult();

        // Order.
        foreach ($request->order as $order) {

            $map = [
                self::COLUMN_ID             => 'u.id',
                self::COLUMN_USERNAME       => 'u.username',
                self::COLUMN_FULLNAME       => 'u.fullname',
                self::COLUMN_EMAIL          => 'u.email',
                self::COLUMN_PERMISSIONS    => 'u.isAdmin',
                self::COLUMN_AUTHENTICATION => 'u.isLdap',
                self::COLUMN_DESCRIPTION    => 'u.description',
            ];

            $query->addOrderBy($map[$order->column], $order->dir);
        }

        // Pagination.
        $query->setFirstResult($request->start);

        if ($request->length >= 0) {
            $query->setMaxResults($request->length);
        }

        /** @var \eTraxis\Entity\User[] $entities */
        $entities = $query->getQuery()->getResult();

        foreach ($entities as $entity) {

            if ($entity->isLocked()) {
                $color = 'red';
            }
            elseif ($entity->isDisabled()) {
                $color = 'gray';
            }
            else {
                $color = null;
            }

            $results->data[] = [
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

        return $results;
    }
}
