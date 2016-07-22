<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eTraxis\Dictionary;
use eTraxis\Security\CurrentUser;

/**
 * Record.
 *
 * @ORM\Table(name="records",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(columns={"author_id", "created_at"})
 *            })
 * @ORM\Entity
 * @ORM\EntityListeners({"eTraxis\Entity\EntityListener"})
 */
class Record extends Entity
{
    // Constraints.
    const MAX_SUBJECT = 250;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string Subject of the record.
     *
     * @ORM\Column(name="subject", type="string", length=250)
     */
    private $subject;

    /**
     * @var State Current state of the record.
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state_id", nullable=false, referencedColumnName="id")
     */
    private $state;

    /**
     * @var User Author of the the record.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", nullable=false, referencedColumnName="id")
     */
    private $author;

    /**
     * @var User Current responsible of the record.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="responsible_id", referencedColumnName="id")
     */
    private $responsible;

    /**
     * @var int Unix Epoch timestamp when the record was created.
     *
     * @ORM\Column(name="created_at", type="integer")
     */
    private $createdAt;

    /**
     * @var int Unix Epoch timestamp when the record was changed last time.
     *
     * @ORM\Column(name="changed_at", type="integer")
     */
    private $changedAt;

    /**
     * @var int Unix Epoch timestamp when the record was closed.
     *
     * @ORM\Column(name="closed_at", type="integer", nullable=true)
     */
    private $closedAt;

    /**
     * @var int Unix Epoch timestamp when the postponed record will be resumed back.
     *
     * @ORM\Column(name="resumed_at", type="integer", nullable=true)
     */
    private $resumedAt;

    /**
     * @var ArrayCollection List of record events.
     *
     * @ORM\OneToMany(targetEntity="Event", mappedBy="record", cascade={"persist"})
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $history;

    /**
     * @var ArrayCollection List of state fields.
     *
     * @ORM\OneToMany(targetEntity="Watcher", mappedBy="record")
     */
    private $watchers;

    /**
     * @var FieldValue[] Current values of all fields.
     *                   Contains only fields which the current user is allowed to read.
     */
    private $fieldValues = [];

    /**
     * @var array Scalar values of all fields.
     *            Contains only fields which the current user is allowed to read.
     */
    private $cacheFieldValues = [];

    /**
     * @var array Human readable values of "decimal" fields only.
     *            WARNING: contains all fields no matter whether the current user is allowed to see them.
     */
    private $cacheDecimalValues = [];

    /**
     * @var array Human readable values of "string" fields only.
     *            WARNING: contains all fields no matter whether the current user is allowed to see them.
     */
    private $cacheStringValues = [];

    /**
     * @var array Human readable values of "text" fields only.
     *            WARNING: contains all fields no matter whether the current user is allowed to see them.
     */
    private $cacheTextValues = [];

    /**
     * @var array Human readable values of "list" fields only.
     *            WARNING: contains all fields no matter whether the current user is allowed to see them.
     */
    private $cacheListValues = [];

    /**
     * @var int ID of the current user.
     */
    private $currentUser;

    /**
     * Creates new record.
     *
     * @param   User     $author   Author of the record.
     * @param   Template $template Template to use for creation.
     */
    public function __construct(User $author, Template $template)
    {
        $this->state  = $template->getInitialState();
        $this->author = $author;

        $this->history  = new ArrayCollection();
        $this->watchers = new ArrayCollection();

        $event = new Event($this, $this->author, Dictionary\EventType::RECORD_CREATED, $this->state->getId());
        $this->history->add($event);

        $this->createdAt = $event->getCreatedAt();
        $this->changedAt = $event->getCreatedAt();
    }

    /**
     * If field values cache is not set, initializes the cache.
     *
     * @param   int $user Current user ID.
     */
    protected function initFieldValuesCache(int $user)
    {
        if ($this->currentUser !== $user) {

            $this->currentUser = $user;

            $this->cacheFieldValues   = [];
            $this->cacheDecimalValues = [];
            $this->cacheStringValues  = [];
            $this->cacheTextValues    = [];
            $this->cacheListValues    = [];

            $builder = $this->manager->createQueryBuilder();

            $query = $builder
                ->distinct()
                ->select('fieldValue')
                ->addSelect('event')
                ->addSelect('field')
                ->addSelect('state')
                ->from(FieldValue::class, 'fieldValue')
                ->innerJoin('fieldValue.event', 'event')
                ->innerJoin('fieldValue.field', 'field')
                ->innerJoin('field.state', 'state')
                ->leftJoin('field.rolePermissions', 'rolePermissions')
                ->leftJoin('field.groupPermissions', 'groupPermissions')
                ->leftJoin('groupPermissions.group', 'group')
                ->where('event.record = :record')
                ->andWhere('fieldValue.isCurrent = :current')
                ->andWhere($builder->expr()->orX(
                    $builder->expr()->isMemberOf(':user', 'group.members'),
                    $builder->expr()->andX('rolePermissions.permission = :permission', 'rolePermissions.role IN (:roles)')
                ))
                ->orderBy('event.createdAt')
                ->addOrderBy('field.removedAt')
                ->addOrderBy('field.order')
            ;

            $roles = [Dictionary\SystemRole::ANYONE];

            if ($this->author->getId() === $user) {
                $roles[] = Dictionary\SystemRole::AUTHOR;
            }

            if ($this->responsible !== null && $this->responsible->getId() === $user) {
                $roles[] = Dictionary\SystemRole::RESPONSIBLE;
            }

            $query->setParameters([
                'record'     => $this->id,
                'current'    => true,
                'user'       => $user,
                'permission' => Dictionary\FieldPermission::READ,
                'roles'      => $roles,
            ]);

            $this->fieldValues = $query->getQuery()->getResult();

            foreach ($this->fieldValues as $value) {
                $this->cacheFieldValues[$value->getField()->getId()] = $value->getValue();
            }
        }
    }

    /**
     * If decimal values cache is not set, initializes the cache.
     */
    protected function initDecimalValuesCache()
    {
        if (count($this->cacheDecimalValues) === 0) {

            $query = $this->manager->createQueryBuilder()
                ->select('field.id')
                ->addSelect('decimalValue.value')
                ->from(FieldValue::class, 'fieldValue')
                ->from(DecimalValue::class, 'decimalValue')
                ->innerJoin('fieldValue.event', 'event')
                ->innerJoin('fieldValue.field', 'field')
                ->where('event.record = :record')
                ->andWhere('field.type = :type')
                ->andWhere('fieldValue.isCurrent = :current')
                ->andWhere('decimalValue.id = fieldValue.value')
            ;

            $query->setParameters([
                'record'  => $this->id,
                'type'    => Dictionary\FieldType::DECIMAL,
                'current' => true,
            ]);

            foreach ($query->getQuery()->getArrayResult() as $value) {
                $this->cacheDecimalValues[$value['id']] = $value['value'];
            }
        }
    }

    /**
     * If string values cache is not set, initializes the cache.
     */
    protected function initStringValuesCache()
    {
        if (count($this->cacheStringValues) === 0) {

            $query = $this->manager->createQueryBuilder()
                ->select('field.id')
                ->addSelect('stringValue.value')
                ->from(FieldValue::class, 'fieldValue')
                ->from(StringValue::class, 'stringValue')
                ->innerJoin('fieldValue.event', 'event')
                ->innerJoin('fieldValue.field', 'field')
                ->where('event.record = :record')
                ->andWhere('field.type = :type')
                ->andWhere('fieldValue.isCurrent = :current')
                ->andWhere('stringValue.id = fieldValue.value')
            ;

            $query->setParameters([
                'record'  => $this->id,
                'type'    => Dictionary\FieldType::STRING,
                'current' => true,
            ]);

            foreach ($query->getQuery()->getArrayResult() as $value) {
                $this->cacheStringValues[$value['id']] = $value['value'];
            }
        }
    }

    /**
     * If text values cache is not set, initializes the cache.
     */
    protected function initTextValuesCache()
    {
        if (count($this->cacheTextValues) === 0) {

            $query = $this->manager->createQueryBuilder()
                ->select('field.id')
                ->addSelect('textValue.value')
                ->from(FieldValue::class, 'fieldValue')
                ->from(TextValue::class, 'textValue')
                ->innerJoin('fieldValue.event', 'event')
                ->innerJoin('fieldValue.field', 'field')
                ->where('event.record = :record')
                ->andWhere('field.type = :type')
                ->andWhere('fieldValue.isCurrent = :current')
                ->andWhere('textValue.id = fieldValue.value')
            ;

            $query->setParameters([
                'record'  => $this->id,
                'type'    => Dictionary\FieldType::TEXT,
                'current' => true,
            ]);

            foreach ($query->getQuery()->getArrayResult() as $value) {
                $this->cacheTextValues[$value['id']] = $value['value'];
            }
        }
    }

    /**
     * If list values cache is not set, initializes the cache.
     */
    protected function initListValuesCache()
    {
        if (count($this->cacheListValues) === 0) {

            $query = $this->manager->createQueryBuilder()
                ->select('field.id')
                ->addSelect('item.text')
                ->from(FieldValue::class, 'fieldValue')
                ->from(ListItem::class, 'item')
                ->innerJoin('fieldValue.event', 'event')
                ->innerJoin('fieldValue.field', 'field')
                ->where('event.record = :record')
                ->andWhere('field.type = :type')
                ->andWhere('fieldValue.isCurrent = :current')
                ->andWhere('item.field = field')
                ->andWhere('item.value = fieldValue.value')
            ;

            $query->setParameters([
                'record'  => $this->id,
                'type'    => Dictionary\FieldType::LIST,
                'current' => true,
            ]);

            foreach ($query->getQuery()->getArrayResult() as $value) {
                $this->cacheListValues[$value['id']] = $value['text'];
            }
        }
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns formatted record ID.
     *
     * @return  string
     */
    public function getRecordId()
    {
        return $this->state->getTemplate()->getPrefix() . '-' . $this->id;
    }

    /**
     * Property setter.
     *
     * @param   string $subject
     *
     * @return  self
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Property getter.
     *
     * @return  Project
     */
    public function getProject()
    {
        return $this->state->getTemplate()->getProject();
    }

    /**
     * Property getter.
     *
     * @return  Template
     */
    public function getTemplate()
    {
        return $this->state->getTemplate();
    }

    /**
     * Property getter.
     *
     * @return  State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Property getter.
     *
     * @return  User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Property getter.
     *
     * @return  User
     */
    public function getResponsible()
    {
        return $this->responsible;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getChangedAt()
    {
        return $this->changedAt;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * Returns record's age in number of days.
     *
     * @return  int
     */
    public function getAge()
    {
        $age = ($this->closedAt ?: time()) - $this->createdAt;

        return intdiv($age, 86400) + 1;
    }

    /**
     * Returns whether the record is overdue (older than critical age of its template).
     *
     * @return  bool
     */
    public function isOverdue()
    {
        $criticalAge = $this->state->getTemplate()->getCriticalAge() ?: PHP_INT_MAX;

        return $this->getAge() > $criticalAge;
    }

    /**
     * Checks whether the record is closed.
     *
     * @return  bool
     */
    public function isClosed()
    {
        return $this->closedAt !== null;
    }

    /**
     * Checks whether the record is postponed.
     *
     * @return  bool
     */
    public function isPostponed()
    {
        return $this->resumedAt > time();
    }

    /**
     * Get record history.
     *
     * @return  Event[]
     */
    public function getHistory()
    {
        return $this->history->toArray();
    }

    /**
     * Add watcher.
     *
     * @param   Watcher $watcher
     *
     * @return  self
     */
    public function addWatcher(Watcher $watcher)
    {
        $this->watchers[] = $watcher;

        return $this;
    }

    /**
     * Remove watcher.
     *
     * @param   Watcher $watcher
     *
     * @return  self
     */
    public function removeWatcher(Watcher $watcher)
    {
        $this->watchers->removeElement($watcher);

        return $this;
    }

    /**
     * Get list of record watchers.
     *
     * @return  Watcher[]
     */
    public function getWatchers()
    {
        return $this->watchers->toArray();
    }

    /**
     * Returns list of all states which are present in the record's history.
     *
     * New states could be created after the record is closed, so current set of states in the template
     * may differ from set of states which was used initially. As result, we need list of states from
     * record's history rather than from record's template.
     *
     * @return  State[]
     */
    public function getAllStates()
    {
        $query = $this->manager->createQueryBuilder()
            ->select('state')
            ->from(State::class, 'state')
            ->from(Event::class, 'event')
            ->where('event.record = :record')
            ->andWhere('event.type IN (:types)')
            ->andWhere('event.parameter = state.id')
            ->orderBy('event.createdAt')
        ;

        $query->setParameters([
            'record' => $this->id,
            'types'  => [
                Dictionary\EventType::RECORD_CREATED,
                Dictionary\EventType::RECORD_REOPENED,
                Dictionary\EventType::STATE_CHANGED,
            ],
        ]);

        return $query->getQuery()->getResult();
    }

    /**
     * Returns all fields which are present in the specified state for this record.
     *
     * New fields could be created (and existing could be removed) after the record was in the state last time,
     * so current set of fields in the state may differ from set of fields which was used initially. As result,
     * we need list of fields from record's current values rather than from appropriate state.
     *
     * @param   State       $state
     * @param   CurrentUser $user
     *
     * @return  Field[]
     */
    public function getFieldsByState(State $state, CurrentUser $user)
    {
        $this->initFieldValuesCache($user->getId());

        $filtered = array_filter($this->fieldValues, function (FieldValue $value) use ($state) {
            return $value->getField()->getState() === $state;
        });

        return array_map(function (FieldValue $value) {
            return $value->getField();
        }, $filtered);
    }

    /**
     * Returns current value of specified field in a human readable form.
     *
     * @param   Field       $field
     * @param   CurrentUser $user
     *
     * @return  mixed
     */
    public function getFieldValue(Field $field, CurrentUser $user)
    {
        $value = null;

        $this->initFieldValuesCache($user->getId());

        if (array_key_exists($field->getId(), $this->cacheFieldValues)) {

            switch ($field->getType()) {

                case Dictionary\FieldType::DECIMAL:
                    $this->initDecimalValuesCache();
                    $value = $this->cacheDecimalValues[$field->getId()] ?? null;
                    break;

                case Dictionary\FieldType::STRING:
                    $this->initStringValuesCache();
                    $value = $this->cacheStringValues[$field->getId()] ?? null;
                    break;

                case Dictionary\FieldType::TEXT:
                    $this->initTextValuesCache();
                    $value = $this->cacheTextValues[$field->getId()] ?? null;
                    break;

                case Dictionary\FieldType::LIST:
                    $this->initListValuesCache();
                    $value = $this->cacheListValues[$field->getId()] ?? null;
                    break;

                default:
                    $value = $this->cacheFieldValues[$field->getId()] ?? null;
            }
        }

        return $value;
    }
}
