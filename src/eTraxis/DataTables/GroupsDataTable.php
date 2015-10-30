<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\DataTables;

use DataTables\DataTableHandlerInterface;
use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use eTraxis\Repository\GroupsRepository;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Enumerates all groups existing in eTraxis database.
 */
class GroupsDataTable implements DataTableHandlerInterface
{
    const COLUMN_NAME        = 0;
    const COLUMN_TYPE        = 1;
    const COLUMN_PROJECT     = 2;
    const COLUMN_DESCRIPTION = 3;

    protected $translator;
    protected $repository;

    /**
     * Dependency Injection constructor.
     *
     * @param   TranslatorInterface $translator
     * @param   GroupsRepository    $repository
     */
    public function __construct(TranslatorInterface $translator, GroupsRepository $repository)
    {
        $this->translator = $translator;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DataTableQuery $request)
    {
        $results = new DataTableResults();

        $query = $this->repository->createQueryBuilder('g')->select('COUNT(g.id)');

        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $this->repository->createQueryBuilder('g');

        $query
            ->select('g')
            ->addSelect('p')
            ->leftJoin('g.project', 'p')
        ;

        // Search.
        if ($request->search['value']) {

            $conditions = [
                'LOWER(g.name) LIKE :search',
                'LOWER(p.name) LIKE :search',
                'LOWER(g.description) LIKE :search',
            ];

            $query
                ->where('(' . implode(' OR ', $conditions) . ')')
                ->setParameter('search', mb_strtolower("%{$request->search['value']}%"))
            ;
        }

        // Filter by columns.
        foreach ($request->columns as $column) {

            if (!$column['search']['value']) {
                continue;
            }

            $value = mb_strtolower($column['search']['value']);

            switch ($column['data']) {

                case self::COLUMN_NAME:

                    $query
                        ->andWhere('LOWER(g.name) LIKE :name')
                        ->setParameter('name', "%{$value}%")
                    ;

                    break;

                case self::COLUMN_TYPE:

                    if ($value == 'global') {
                        $query->andWhere('g.projectId IS NULL');
                    }
                    elseif ($value == 'local') {
                        $query->andWhere('g.projectId IS NOT NULL');
                    }

                    break;

                case self::COLUMN_PROJECT:

                    $query
                        ->andWhere('g.projectId = :project')
                        ->setParameter('project', $value)
                    ;

                    break;

                case self::COLUMN_DESCRIPTION:

                    $query
                        ->andWhere('LOWER(g.description) LIKE :description')
                        ->setParameter('description', "%{$value}%")
                    ;

                    break;
            }
        }

        // Order.
        foreach ($request->order as $order) {

            $map = [
                self::COLUMN_NAME        => 'g.name',
                self::COLUMN_TYPE        => 'g.projectId',
                self::COLUMN_PROJECT     => 'p.name',
                self::COLUMN_DESCRIPTION => 'g.description',
            ];

            $query->addOrderBy($map[$order['column']], $order['dir']);
        }

        /** @var \eTraxis\Entity\Group[] $entities */
        $entities = $query->getQuery()->getResult();

        $results->recordsFiltered = count($entities);

        for ($i = 0; $i < $request->length || $request->length == -1; $i++) {

            $index = $i + $request->start;

            if ($index >= $results->recordsFiltered) {
                break;
            }

            $entity = $entities[$index];

            $results->data[] = [
                $entity->getName(),
                $this->translator->trans($entity->isGlobal() ? 'group.global' : 'group.local'),
                $entity->isGlobal() ? null : $entity->getProject()->getName(),
                $entity->getDescription(),
                'DT_RowAttr' => ['data-id' => $entity->getId()],
            ];
        }

        return $results;
    }
}
