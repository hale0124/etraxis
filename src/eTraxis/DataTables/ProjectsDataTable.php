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
use eTraxis\Repository\ProjectsRepository;
use eTraxis\Service\LocalizerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Enumerates all projects existing in eTraxis database.
 */
class ProjectsDataTable implements DataTableHandlerInterface
{
    const COLUMN_NAME        = 0;
    const COLUMN_START_TIME  = 1;
    const COLUMN_DESCRIPTION = 2;

    protected $translator;
    protected $localizer;
    protected $repository;

    /**
     * Dependency Injection constructor.
     *
     * @param   TranslatorInterface $translator
     * @param   LocalizerInterface  $localizer
     * @param   ProjectsRepository  $repository
     */
    public function __construct(
        TranslatorInterface $translator,
        LocalizerInterface  $localizer,
        ProjectsRepository  $repository)
    {
        $this->translator = $translator;
        $this->localizer  = $localizer;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DataTableQuery $request)
    {
        $results = new DataTableResults();

        $query = $this->repository->createQueryBuilder('p')->select('COUNT(p.id)');

        $results->recordsTotal = $query->getQuery()->getSingleScalarResult();

        $query = $this->repository->createQueryBuilder('p');

        $query->select('p');

        // Search.
        if ($request->search['value']) {

            $conditions = [
                'LOWER(p.name) LIKE :search',
                'LOWER(p.description) LIKE :search',
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
                        ->andWhere('LOWER(p.name) LIKE :name')
                        ->setParameter('name', "%{$value}%")
                    ;

                    break;

                case self::COLUMN_START_TIME:

                    $query
                        ->andWhere('EPOCH_DATE(p.createdAt) LIKE :created')
                        ->setParameter('created', "%{$value}%")
                    ;

                    break;

                case self::COLUMN_DESCRIPTION:

                    $query
                        ->andWhere('LOWER(p.description) LIKE :description')
                        ->setParameter('description', "%{$value}%")
                    ;

                    break;
            }
        }

        // Order.
        foreach ($request->order as $order) {

            $map = [
                self::COLUMN_NAME        => 'p.name',
                self::COLUMN_START_TIME  => 'p.createdAt',
                self::COLUMN_DESCRIPTION => 'p.description',
            ];

            $query->addOrderBy($map[$order['column']], $order['dir']);
        }

        /** @var \eTraxis\Entity\Project[] $entities */
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
                $this->localizer->formatDate($this->localizer->getLocalTimestamp($entity->getCreatedAt())),
                $entity->getDescription(),
                'DT_RowAttr'  => ['data-id' => $entity->getId()],
                'DT_RowClass' => $entity->isSuspended() ? 'gray' : null,
            ];
        }

        return $results;
    }
}
