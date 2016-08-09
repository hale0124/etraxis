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

use DataTables\Column;
use DataTables\DataTableHandlerInterface;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use DataTables\Order;
use DataTables\Search;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use eTraxis\Dictionary\AuthenticationProvider;
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

        $query->select('u');
        $query->from(User::class, 'u');

        // Search.
        if ($request->search->value) {
            $this->querySearch($query, $request->search);
        }

        // Filter by columns.
        foreach ($request->columns as $column) {
            if ($column->search->value) {
                $this->queryFilter($query, $column);
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
            $query = $this->queryOrder($query, $order);
        }

        // Pagination.
        $query->setFirstResult($request->start);

        if ($request->length >= 0) {
            $query->setMaxResults($request->length);
        }

        // Execute query.
        $results->data = array_map([$this, 'doctrine2datatable'], $query->getQuery()->getResult());

        return $results;
    }

    /**
     * Alters query in accordance with the specified search.
     *
     * @param   QueryBuilder $query
     * @param   Search       $search
     *
     * @return  QueryBuilder
     */
    protected function querySearch(QueryBuilder $query, Search $search)
    {
        $conditions = [
            'LOWER(u.username) LIKE :search',
            'LOWER(u.fullname) LIKE :search',
            'LOWER(u.email) LIKE :search',
            'LOWER(u.description) LIKE :search',
        ];

        return $query
            ->where('(' . implode(' OR ', $conditions) . ')')
            ->setParameter('search', mb_strtolower("%{$search->value}%"))
        ;
    }

    /**
     * Alters query to filter by the specified column.
     *
     * @param   QueryBuilder $query
     * @param   Column       $column
     *
     * @return  QueryBuilder
     */
    protected function queryFilter(QueryBuilder $query, Column $column)
    {
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

                $query
                    ->andWhere('u.isAdmin = :isAdmin')
                    ->setParameter('isAdmin', $value === 'admin')
                ;

                break;

            case self::COLUMN_AUTHENTICATION:

                if (AuthenticationProvider::has($value)) {
                    $query
                        ->andWhere('u.provider = :provider')
                        ->setParameter('provider', $value)
                    ;
                }

                break;

            case self::COLUMN_DESCRIPTION:

                $query
                    ->andWhere('LOWER(u.description) LIKE :description')
                    ->setParameter('description', "%{$value}%")
                ;

                break;
        }

        return $query;
    }

    /**
     * Alters query in accordance with the specified sorting.
     *
     * @param   QueryBuilder $query
     * @param   Order        $order
     *
     * @return  QueryBuilder
     */
    protected function queryOrder(QueryBuilder $query, Order $order)
    {
        $map = [
            self::COLUMN_ID             => 'u.id',
            self::COLUMN_USERNAME       => 'u.username',
            self::COLUMN_FULLNAME       => 'u.fullname',
            self::COLUMN_EMAIL          => 'u.email',
            self::COLUMN_PERMISSIONS    => 'u.isAdmin',
            self::COLUMN_AUTHENTICATION => 'u.provider',
            self::COLUMN_DESCRIPTION    => 'u.description',
        ];

        return $query->addOrderBy($map[$order->column], $order->dir);
    }

    /**
     * Converts an entry returned from the database to DataTables representation.
     *
     * @param   User $user
     *
     * @return  array
     */
    protected function doctrine2datatable(User $user)
    {
        if ($user->isLocked()) {
            $color = 'red';
        }
        elseif ($user->isDisabled()) {
            $color = 'gray';
        }
        else {
            $color = null;
        }

        return [
            $user->getId(),
            $user->getUsername(),
            $user->getFullname(),
            $user->getEmail(),
            $this->translator->trans($user->isAdmin() ? 'role.administrator' : 'role.user'),
            AuthenticationProvider::get($user->getProvider()),
            $user->getDescription(),
            DataTableResults::DT_ROW_ATTR  => ['data-id' => $user->getId()],
            DataTableResults::DT_ROW_CLASS => $color,
        ];
    }
}
