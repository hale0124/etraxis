<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups\Handler;

use eTraxis\CommandBus\Groups\ListGroupsCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Command handler.
 */
class ListGroupsCommandHandler
{
    const COLUMN_NAME        = 0;
    const COLUMN_TYPE        = 1;
    const COLUMN_PROJECT     = 2;
    const COLUMN_DESCRIPTION = 3;

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
     * Enumerates all groups existing in eTraxis database.
     *
     * @param   ListGroupsCommand $command
     *
     * @return  array
     */
    public function handle(ListGroupsCommand $command)
    {
        $result = [
            'groups'   => [],
            'filtered' => 0,
            'total'    => 0,
        ];

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:Group');

        $query = $repository->createQueryBuilder('g')->select('COUNT(g.id)');

        $result['total'] = $query->getQuery()->getSingleScalarResult();

        $query = $repository->createQueryBuilder('g');

        $query
            ->select('g')
            ->addSelect('p')
            ->leftJoin('g.project', 'p')
        ;

        // Search.
        if ($command->search) {

            $conditions = [
                'LOWER(g.name) LIKE :search',
                'LOWER(p.name) LIKE :search',
                'LOWER(g.description) LIKE :search',
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
        foreach ($command->order as $order) {

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

        $result['filtered'] = count($entities);

        for ($i = 0; $i < $command->length || $command->length == -1; $i++) {

            $index = $i + $command->start;

            if ($index >= $result['filtered']) {
                break;
            }

            $entity = $entities[$index];

            $result['groups'][] = [
                $entity->getName(),
                $this->translator->trans($entity->isGlobal() ? 'group.global' : 'group.local'),
                $entity->isGlobal() ? null : $entity->getProject()->getName(),
                $entity->getDescription(),
                'DT_RowAttr' => ['data-id' => $entity->getId()],
            ];
        }

        return $result;
    }
}
