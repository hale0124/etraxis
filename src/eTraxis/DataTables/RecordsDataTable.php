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
use eTraxis\Constant\Seconds;
use eTraxis\Dictionary\BBCodeMode;
use eTraxis\Repository\TemplatesRepository;
use eTraxis\Service\BBCodeInterface;
use eTraxis\Service\RecordsCacheInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Enumerates all records available to current user.
 */
class RecordsDataTable implements DataTableHandlerInterface
{
    const COLUMN_ID          = 'id';
    const COLUMN_RECORD_ID   = 'record';
    const COLUMN_PROJECT     = 'project';
    const COLUMN_STATE       = 'state';
    const COLUMN_SUBJECT     = 'subject';
    const COLUMN_AUTHOR      = 'author';
    const COLUMN_RESPONSIBLE = 'responsible';
    const COLUMN_AGE         = 'age';

    protected $manager;
    protected $token_storage;
    protected $cache;
    protected $bbcode;
    protected $templates_repository;

    /** @var int The today's midnight (Unix Epoch timestamp). */
    protected $today;

    /** @var array Query parameters. */
    protected $parameters;

    /** @var array The "SELECT" section of the query. */
    protected $clause_select;

    /** @var array List of tables joined to the primary one. */
    protected $clause_join;

    /** @var array The "WHERE" section of the query. */
    protected $clause_where;

    /** @var array The "ORDER BY" section of the query. */
    protected $clause_order;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     * @param   TokenStorageInterface  $token_storage
     * @param   RecordsCacheInterface  $cache
     * @param   BBCodeInterface        $bbcode
     * @param   TemplatesRepository    $templates_repository
     */
    public function __construct(
        EntityManagerInterface $manager,
        TokenStorageInterface  $token_storage,
        RecordsCacheInterface  $cache,
        BBCodeInterface        $bbcode,
        TemplatesRepository    $templates_repository
    )
    {
        $this->manager              = $manager;
        $this->token_storage        = $token_storage;
        $this->cache                = $cache;
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

        $cached = $this->cache->getRecords($user->getId(), $request);

        if ($cached === false) {
            $cached = $this->doQuery($request);
            $this->cache->saveRecords($user->getId(), $cached);
        }

        $results = new DataTableResults();

        $results->recordsTotal    = $cached->total;
        $results->recordsFiltered = count($cached->data);

        $results->data = array_slice($cached->data, $request->start, $request->length === -1 ? null : $request->length);

        return $results;
    }

    /**
     * Processes specified DataTables query.
     *
     * @param   DataTableQuery $request
     *
     * @return  DataTableCachedResults
     */
    protected function doQuery(DataTableQuery $request): DataTableCachedResults
    {
        /** @var \eTraxis\Security\CurrentUser $user */
        $user = $this->token_storage->getToken()->getUser();

        // Get the midnight of today.
        $date        = getdate();
        $this->today = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);

        // Query parameters.
        $this->parameters = [
            'user'      => $user->getId(),
            'templates' => $this->templates_repository->getTemplates($user->getId()),
        ];

        // The "SELECT" section of the query.
        $this->clause_select = [
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
        $this->clause_join = [
            'INNER JOIN record.state state',
            'INNER JOIN state.template template',
            'INNER JOIN template.project project',
            'INNER JOIN record.author author',
            'LEFT JOIN record.responsible responsible',
            'LEFT JOIN eTraxis:LastRead lastRead WITH lastRead.record = record AND lastRead.user = :user',
        ];

        // The "WHERE" section of the query.
        $this->clause_where = [
            'record.author = :user OR record.responsible = :user OR template.id IN (:templates)',
        ];

        // The "ORDER BY" section of the query.
        $this->clause_order = [];

        // Total number of entries.
        $dql = sprintf('SELECT COUNT(record.id) FROM eTraxis:Record record %s WHERE (%s)',
            implode(' ', $this->clause_join),
            implode(') AND (', $this->clause_where)
        );

        $total = $this->manager->createQuery($dql)->setParameters($this->parameters)->getSingleScalarResult();

        // Search.
        if ($request->search->value) {
            $this->querySearch($request->search);
        }

        // Filter by columns.
        foreach ($request->columns as $column) {
            if ($column->search->value) {
                $this->queryFilter($column);
            }
        }

        // Order.
        foreach ($request->order as $order) {
            $this->queryOrder($order, $request->columns[$order->column]);
        }

        // Default order.
        $this->clause_order[] = 'record.id ASC';

        // Base query.
        $dql = sprintf('SELECT %s FROM eTraxis:Record record %s WHERE (%s) ORDER BY %s',
            implode(', ', $this->clause_select),
            implode(' ', $this->clause_join),
            implode(') AND (', $this->clause_where),
            implode(', ', $this->clause_order)
        );

        $query = $this->manager->createQuery($dql);

        $this->parameters['today'] = $this->today;

        $records = array_map([$this, 'doctrine2datatable'], $query->execute($this->parameters));

        return new DataTableCachedResults($request, $total, $records);
    }

    /**
     * Alters query in accordance with the specified search.
     *
     * @param   Search $search
     */
    protected function querySearch(Search $search)
    {
        $search = mb_strtolower("%{$search->value}%");

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

        $this->parameters['records'] = array_unique($records);

        $this->clause_where[] = 'record.id IN (:records)';
    }

    /**
     * Alters query to filter by the specified column.
     *
     * @param   Column $column
     */
    protected function queryFilter(Column $column)
    {
        $value = mb_strtolower($column->search->value);

        switch ($column->data) {

            case self::COLUMN_RECORD_ID:

                $this->parameters['filter_id'] = "%{$value}%";

                $this->clause_where[] = 'CONCAT(LOWER(template.prefix), \'-\', record.id) LIKE :filter_id';

                break;

            case self::COLUMN_PROJECT:

                $this->parameters['filter_project'] = "%{$value}%";

                $this->clause_where[] = 'LOWER(project.name) LIKE :filter_project';

                break;

            case self::COLUMN_STATE:

                $this->parameters['filter_state'] = "%{$value}%";

                $this->clause_where[] = 'LOWER(state.abbreviation) LIKE :filter_state';

                break;

            case self::COLUMN_SUBJECT:

                $this->parameters['filter_subject'] = "%{$value}%";

                $this->clause_where[] = 'LOWER(record.subject) LIKE :filter_subject';

                break;

            case self::COLUMN_AUTHOR:

                $this->parameters['filter_author'] = "%{$value}%";

                $this->clause_where[] = 'LOWER(author.fullname) LIKE :filter_author';

                break;

            case self::COLUMN_RESPONSIBLE:

                $this->parameters['filter_responsible'] = "%{$value}%";

                $this->clause_where[] = 'LOWER(responsible.fullname) LIKE :filter_responsible';

                break;

            case self::COLUMN_AGE:

                $this->parameters['today']      = $this->today;
                $this->parameters['filter_age'] = ((int) $value) - 1;

                $this->clause_where[] = 'INTDIV(COALESCE(record.closedAt - record.createdAt, :today - record.createdAt), 86400) = :filter_age';

                break;
        }
    }

    /**
     * Alters query in accordance with the specified sorting.
     *
     * @param   Order  $order
     * @param   Column $column
     */
    protected function queryOrder(Order $order, Column $column)
    {
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

        $this->clause_order[] = sprintf('%s %s', $map[$column->data] ?? 'record.id', $dir[$order->dir] ?? 'ASC');
    }

    /**
     * Converts an entry returned from the database to DataTables representation.
     *
     * @param   array $data
     *
     * @return  array
     */
    protected function doctrine2datatable(array $data)
    {
        $row_class = [];

        $age = intdiv($data['age'], Seconds::ONE_DAY) + 1;

        if ($data['closedAt'] !== null) {
            $row_class[] = 'gray';
        }
        elseif ($data['resumedAt'] > $this->today) {
            $row_class[] = 'blue';
        }
        elseif ($age > $data['criticalAge']) {
            $row_class[] = 'red';
        }

        if ($data['readAt'] < $data['changedAt']) {
            $row_class[] = 'unread';
        }

        return [
            self::COLUMN_ID                => $data['id'],
            self::COLUMN_RECORD_ID         => sprintf('%s-%d', $data['templatePrefix'], $data['id']),
            self::COLUMN_PROJECT           => $data['projectName'],
            self::COLUMN_STATE             => $data['stateAbbreviation'],
            self::COLUMN_SUBJECT           => $this->bbcode->bbcode($data['subject'], BBCodeMode::STRIP),
            self::COLUMN_AUTHOR            => $data['authorFullname'],
            self::COLUMN_RESPONSIBLE       => $data['responsibleFullname'] ?: '&mdash;',
            self::COLUMN_AGE               => $age,
            DataTableResults::DT_ROW_ATTR  => ['data-id' => $data['id']],
            DataTableResults::DT_ROW_CLASS => implode(' ', $row_class),
        ];
    }
}
