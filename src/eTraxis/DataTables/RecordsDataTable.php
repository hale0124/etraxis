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
use eTraxis\Dictionary\BBCodeMode;
use eTraxis\Repository\TemplatesRepository;
use eTraxis\Service\BBCodeInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Enumerates all records available to current user.
 */
class RecordsDataTable implements DataTableHandlerInterface
{
    const COLUMN_ID          = 0;
    const COLUMN_RECORD_ID   = 1;
    const COLUMN_PROJECT     = 2;
    const COLUMN_STATE       = 3;
    const COLUMN_SUBJECT     = 4;
    const COLUMN_AUTHOR      = 5;
    const COLUMN_RESPONSIBLE = 6;
    const COLUMN_AGE         = 7;

    protected $manager;
    protected $token_storage;
    protected $bbcode;
    protected $templates_repository;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     * @param   TokenStorageInterface  $token_storage
     * @param   BBCodeInterface        $bbcode
     * @param   TemplatesRepository    $templates_repository
     */
    public function __construct(
        EntityManagerInterface $manager,
        TokenStorageInterface  $token_storage,
        BBCodeInterface        $bbcode,
        TemplatesRepository    $templates_repository
    )
    {
        $this->manager              = $manager;
        $this->token_storage        = $token_storage;
        $this->bbcode               = $bbcode;
        $this->templates_repository = $templates_repository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DataTableQuery $request): DataTableResults
    {
        /** @var \eTraxis\Security\CurrentUser $user */
        $user = $this->token_storage->getToken()->getUser();

        $results = new DataTableResults();

        // Get the midnight of today.
        $date  = getdate();
        $today = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);

        // Query parameters.
        $parameters = [
            'user'      => $user->getId(),
            'templates' => $this->templates_repository->getTemplates($user->getId()),
        ];

        // The "SELECT" section of the query.
        $clause_select = [
            'record.id',
            'project.name AS projectName',
            'template.prefix AS templatePrefix',
            'template.criticalAge',
            'state.abbreviation AS stateAbbreviation',
            'record.subject',
            'author.fullname AS authorFullname',
            'responsible.fullname AS responsibleFullname',
            'record.createdAt',
            'record.changedAt',
            'record.closedAt',
            'record.resumedAt',
            'COALESCE(record.closedAt - record.createdAt, :today - record.createdAt) AS age',
            'lastRead.readAt',
        ];

        // List of tables joined to the primary one.
        $clause_join = [
            'INNER JOIN record.state state',
            'INNER JOIN state.template template',
            'INNER JOIN template.project project',
            'INNER JOIN record.author author',
            'LEFT JOIN record.responsible responsible',
            'LEFT JOIN eTraxis:LastRead lastRead WITH lastRead.record = record AND lastRead.user = :user',
        ];

        // The "WHERE" section of the query.
        $clause_where = [
            'record.author = :user OR record.responsible = :user OR template.id IN (:templates)',
        ];

        // The "ORDER BY" section of the query.
        $clause_order = [];

        // Total number of entries.
        $dql = sprintf('SELECT COUNT(record.id) FROM eTraxis:Record record %s WHERE (%s)',
            implode(' ', $clause_join),
            implode(') AND (', $clause_where)
        );

        $results->recordsTotal = $this->manager->createQuery($dql)->setParameters($parameters)->getSingleScalarResult();

        // Search.
        if ($request->search->value) {

            $search = mb_strtolower("%{$request->search->value}%");

            // Get list of records which subjects contain the searched value.
            $query = $this->manager->createQuery('
                SELECT record.id
                FROM eTraxis:Record record
                WHERE LOWER(record.subject) LIKE :search
            ');

            $inSubjects = $query->execute(['search' => $search]);

            // Get list of records which comments contain the searched value.
            $query = $this->manager->createQuery('
                SELECT record.id
                FROM eTraxis:Comment comment
                  INNER JOIN comment.event event
                  INNER JOIN event.record record
                WHERE LOWER(comment.text) LIKE :search
                  AND comment.isPrivate = FALSE
            ');

            $inComments = $query->execute(['search' => $search]);

            // Merge resulted IDs and append them to the base query.
            $records = array_map(function ($item) {
                return $item['id'];
            }, array_merge($inSubjects, $inComments));

            $parameters['records'] = array_unique($records);

            $clause_where[] = 'record.id IN (:records)';
        }

        // Filter by columns.
        foreach ($request->columns as $column) {

            if (!$column->search->value) {
                continue;
            }

            $value = mb_strtolower($column->search->value);

            switch ($column->data) {

                case self::COLUMN_RECORD_ID:

                    $parameters['filter_id'] = "%{$value}%";

                    $clause_where[] = 'CONCAT(LOWER(template.prefix), \'-\', record.id) LIKE :filter_id';

                    break;

                case self::COLUMN_PROJECT:

                    $parameters['filter_project'] = "%{$value}%";

                    $clause_where[] = 'LOWER(project.name) LIKE :filter_project';

                    break;

                case self::COLUMN_STATE:

                    $parameters['filter_state'] = "%{$value}%";

                    $clause_where[] = 'LOWER(state.abbreviation) LIKE :filter_state';

                    break;

                case self::COLUMN_SUBJECT:

                    $parameters['filter_subject'] = "%{$value}%";

                    $clause_where[] = 'LOWER(record.subject) LIKE :filter_subject';

                    break;

                case self::COLUMN_AUTHOR:

                    $parameters['filter_author'] = "%{$value}%";

                    $clause_where[] = 'LOWER(author.fullname) LIKE :filter_author';

                    break;

                case self::COLUMN_RESPONSIBLE:

                    $parameters['filter_responsible'] = "%{$value}%";

                    $clause_where[] = 'LOWER(responsible.fullname) LIKE :filter_responsible';

                    break;

                case self::COLUMN_AGE:

                    $parameters['today']      = $today;
                    $parameters['filter_age'] = ((int) $value) - 1;

                    $clause_where[] = 'INTDIV(COALESCE(record.closedAt - record.createdAt, :today - record.createdAt), 86400) = :filter_age';

                    break;
            }
        }

        // Filtered number of entries.
        $dql = sprintf('SELECT COUNT(record.id) FROM eTraxis:Record record %s WHERE (%s)',
            implode(' ', $clause_join),
            implode(') AND (', $clause_where)
        );

        $results->recordsFiltered = $this->manager->createQuery($dql)->setParameters($parameters)->getSingleScalarResult();

        // Order.
        foreach ($request->order as $order) {

            $map = [
                self::COLUMN_ID          => 'record.id',
                self::COLUMN_RECORD_ID   => 'record.id',
                self::COLUMN_PROJECT     => 'projectName',
                self::COLUMN_STATE       => 'stateAbbreviation',
                self::COLUMN_SUBJECT     => 'record.subject',
                self::COLUMN_AUTHOR      => 'authorFullname',
                self::COLUMN_RESPONSIBLE => 'responsibleFullname',
                self::COLUMN_AGE         => 'age',
            ];

            $dir = [
                'asc'  => 'ASC',
                'desc' => 'DESC',
            ];

            $clause_order[] = sprintf('%s %s', $map[$order->column] ?? 'record.id', $dir[$order->dir] ?? 'ASC');
        }

        // Default order.
        $clause_order[] = 'record.id ASC';

        // Base query.
        $dql = sprintf('SELECT %s FROM eTraxis:Record record %s WHERE (%s) ORDER BY %s',
            implode(', ', $clause_select),
            implode(' ', $clause_join),
            implode(') AND (', $clause_where),
            implode(', ', $clause_order)
        );

        $query = $this->manager->createQuery($dql);

        // Pagination.
        $query->setFirstResult($request->start);

        if ($request->length >= 0) {
            $query->setMaxResults($request->length);
        }

        $parameters['today'] = $today;

        $rows = $query->execute($parameters);

        foreach ($rows as $row) {

            $row_class = [];

            $age = intdiv($row['age'], 86400) + 1;

            if ($row['closedAt'] !== null) {
                $row_class[] = 'gray';
            }
            elseif ($row['resumedAt'] > $today) {
                $row_class[] = 'blue';
            }
            elseif ($age > $row['criticalAge']) {
                $row_class[] = 'red';
            }

            if ($row['readAt'] < $row['changedAt']) {
                $row_class[] = 'unread';
            }

            $results->data[] = [
                $row['id'],
                sprintf('%s-%d', $row['templatePrefix'], $row['id']),
                $row['projectName'],
                $row['stateAbbreviation'],
                $this->bbcode->bbcode($row['subject'], BBCodeMode::STRIP),
                $row['authorFullname'],
                $row['responsibleFullname'] ?: '&mdash;',
                $age,
                'DT_RowAttr'  => ['data-id' => $row['id']],
                'DT_RowClass' => implode(' ', $row_class),
            ];
        }

        return $results;
    }
}
