<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Projects\Handler;

use eTraxis\CommandBus\Projects\ListProjectsCommand;
use eTraxis\Service\LocalizerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Command handler.
 */
class ListProjectsCommandHandler
{
    const COLUMN_NAME        = 0;
    const COLUMN_START_TIME  = 1;
    const COLUMN_DESCRIPTION = 2;

    protected $translator;
    protected $localizer;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   TranslatorInterface $translator Translation service.
     * @param   LocalizerInterface  $localizer  Localization service.
     * @param   RegistryInterface   $doctrine   Doctrine entity managers registry.
     */
    public function __construct(
        TranslatorInterface $translator,
        LocalizerInterface  $localizer,
        RegistryInterface   $doctrine)
    {
        $this->translator = $translator;
        $this->localizer  = $localizer;
        $this->doctrine   = $doctrine;
    }

    /**
     * Enumerates all projects existing in eTraxis database.
     *
     * @param   ListProjectsCommand $command
     *
     * @return  array
     */
    public function handle(ListProjectsCommand $command)
    {
        $result = [
            'projects' => [],
            'filtered' => 0,
            'total'    => 0,
        ];

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:Project');

        $query = $repository->createQueryBuilder('p')->select('COUNT(p.id)');

        $result['total'] = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('p');

        $query->select('p');

        // Search.
        if ($command->search) {

            $conditions = [
                'LOWER(p.name) LIKE :search',
                'LOWER(p.description) LIKE :search',
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
        foreach ($command->order as $order) {

            $map = [
                self::COLUMN_NAME        => 'p.name',
                self::COLUMN_START_TIME  => 'p.createdAt',
                self::COLUMN_DESCRIPTION => 'p.description',
            ];

            $query->addOrderBy($map[$order['column']], $order['dir']);
        }

        /** @var \eTraxis\Entity\Project[] $entities */
        $entities = $query->getQuery()->getResult();

        $result['filtered'] = count($entities);

        for ($i = 0; $i < $command->length || $command->length == -1; $i++) {

            $index = $i + $command->start;

            if ($index >= $result['filtered']) {
                break;
            }

            $entity = $entities[$index];

            $result['projects'][] = [
                $entity->getName(),
                $this->localizer->formatDate($this->localizer->getLocalTimestamp($entity->getCreatedAt())),
                $entity->getDescription(),
                'DT_RowAttr'  => ['data-id' => $entity->getId()],
                'DT_RowClass' => $entity->isSuspended() ? 'gray' : null,
            ];
        }

        return $result;
    }
}
