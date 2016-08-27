<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Record;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Dictionary\EventType;
use eTraxis\Dictionary\FieldPermission;
use eTraxis\Dictionary\FieldType;
use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\DecimalValue;
use eTraxis\Entity\Event;
use eTraxis\Entity\FieldValue;
use eTraxis\Entity\ListItem;
use eTraxis\Entity\Record;
use eTraxis\Entity\State;
use eTraxis\Entity\StringValue;
use eTraxis\Entity\TextValue;
use eTraxis\Security\CurrentUser;

/**
 * Collection of all states which are present in the record's history.
 *
 * New states could be created after the record is closed, so current set of states in the template
 * may differ from set of states which was used initially. As result, we need list of states from
 * record's history rather than from record's template.
 */
class RecordStates extends \ArrayIterator
{
    /**
     * @var array[] List of all users who were assigned to the record.
     *              Key is a state ID. Value is an array of "User" objects.
     */
    private $responsibles = [];

    /**
     * @var FieldValue[] Current values of all fields.
     *                   Contains only fields which the current user is allowed to read.
     */
    private $fieldValues = [];

    /**
     * @var int[] Scalar values of all fields.
     *            Contains only fields which the current user is allowed to read.
     */
    private $scalarValues = [];

    /**
     * @var string[] Human readable values of "decimal" fields only.
     *               WARNING: contains all fields no matter whether the current user is allowed to see them.
     */
    private $decimalValues;

    /**
     * @var string[] Human readable values of "string" fields only.
     *               WARNING: contains all fields no matter whether the current user is allowed to see them.
     */
    private $stringValues;

    /**
     * @var string[] Human readable values of "text" fields only.
     *               WARNING: contains all fields no matter whether the current user is allowed to see them.
     */
    private $textValues;

    /**
     * @var string[] Human readable values of "list" fields only.
     *               WARNING: contains all fields no matter whether the current user is allowed to see them.
     */
    private $listValues;

    /**
     * Constructor.
     *
     * @param   Record                 $record  Target record.
     * @param   CurrentUser            $user    Current user.
     * @param   EntityManagerInterface $manager Entity manager.
     */
    public function __construct(Record $record, CurrentUser $user, EntityManagerInterface $manager)
    {
        parent::__construct();

        $this->initResponsibles($record);
        $this->initFieldValues($record, $user, $manager);

        $query = $manager->createQueryBuilder()
            ->select('state')
            ->from(State::class, 'state')
            ->from(Event::class, 'event')
            ->where('event.record = :record')
            ->andWhere('event.type IN (:types)')
            ->andWhere('event.parameter = state.id')
            ->orderBy('event.createdAt')
        ;

        $query->setParameters([
            'record' => $record->getId(),
            'types'  => [
                EventType::RECORD_CREATED,
                EventType::RECORD_REOPENED,
                EventType::STATE_CHANGED,
            ],
        ]);

        /** @var State[] $states */
        $states = $query->getQuery()->getResult();

        foreach ($states as $state) {

            $recordFields = [];

            $fieldValues = array_filter($this->fieldValues, function (FieldValue $value) use ($state) {
                return $value->getField()->getState() === $state;
            });

            foreach ($fieldValues as $fieldValue) {

                $field = $fieldValue->getField();
                $value = null;

                if (array_key_exists($field->getId(), $this->scalarValues)) {

                    switch ($field->getType()) {

                        case FieldType::DECIMAL:
                            $this->initDecimalValues($record, $manager);
                            $value = $this->decimalValues[$field->getId()] ?? null;
                            break;

                        case FieldType::STRING:
                            $this->initStringValues($record, $manager);
                            $value = $this->stringValues[$field->getId()] ?? null;
                            $value = $field->getPCRE()->transform($value);
                            break;

                        case FieldType::TEXT:
                            $this->initTextValues($record, $manager);
                            $value = $this->textValues[$field->getId()] ?? null;
                            $value = $field->getPCRE()->transform($value);
                            break;

                        case FieldType::LIST:
                            $this->initListValues($record, $manager);
                            $value = $this->listValues[$field->getId()] ?? null;
                            break;

                        default:
                            $value = $this->scalarValues[$field->getId()] ?? null;
                    }
                }

                $recordFields[] = new RecordField($field->getName(), $field->getType(), $value);
            }

            $recordState = new RecordState($state->getName(), $recordFields, $this->responsibles[$state->getName()] ?? []);

            $this->append($recordState);
        }
    }

    /**
     * Initializes the responsibles cache.
     *
     * @param   Record $record
     */
    private function initResponsibles(Record $record)
    {
        $events = $record->getHistory();

        $state = null;

        foreach ($events as $event) {

            switch ($event->getType()) {

                case EventType::RECORD_CREATED:
                case EventType::RECORD_REOPENED:
                case EventType::STATE_CHANGED:

                    $state = $event->getParameter();

                    break;

                case EventType::RECORD_ASSIGNED:

                    if (!array_key_exists($state, $this->responsibles)) {
                        $this->responsibles[$state] = [];
                    }

                    if (!in_array($event->getUser(), $this->responsibles[$state])) {
                        $this->responsibles[$state][] = $event->getParameter();
                    }

                    break;
            }
        }
    }

    /**
     * Initializes the field values cache.
     *
     * @param   Record                 $record
     * @param   CurrentUser            $user
     * @param   EntityManagerInterface $manager
     */
    private function initFieldValues(Record $record, CurrentUser $user, EntityManagerInterface $manager)
    {
        $builder = $manager->createQueryBuilder();

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

        $roles = [SystemRole::ANYONE];

        if ($record->getAuthor()->getId() === $user->getId()) {
            $roles[] = SystemRole::AUTHOR;
        }

        if ($record->isAssigned() && $record->getResponsible()->getId() === $user->getId()) {
            $roles[] = SystemRole::RESPONSIBLE;
        }

        $query->setParameters([
            'record'     => $record->getId(),
            'current'    => true,
            'user'       => $user->getId(),
            'permission' => FieldPermission::READ,
            'roles'      => $roles,
        ]);

        $this->fieldValues = $query->getQuery()->getResult();

        foreach ($this->fieldValues as $value) {
            $this->scalarValues[$value->getField()->getId()] = $value->getValue();
        }
    }

    /**
     * If decimal values cache is not set, initializes the cache.
     *
     * @param   Record                 $record
     * @param   EntityManagerInterface $manager
     */
    private function initDecimalValues(Record $record, EntityManagerInterface $manager)
    {
        if ($this->decimalValues === null) {

            $query = $manager->createQueryBuilder()
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
                'record'  => $record->getId(),
                'type'    => FieldType::DECIMAL,
                'current' => true,
            ]);

            foreach ($query->getQuery()->getArrayResult() as $value) {
                $this->decimalValues[$value['id']] = $value['value'];
            }
        }
    }

    /**
     * If string values cache is not set, initializes the cache.
     *
     * @param   Record                 $record
     * @param   EntityManagerInterface $manager
     */
    private function initStringValues(Record $record, EntityManagerInterface $manager)
    {
        if ($this->stringValues === null) {

            $query = $manager->createQueryBuilder()
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
                'record'  => $record->getId(),
                'type'    => FieldType::STRING,
                'current' => true,
            ]);

            foreach ($query->getQuery()->getArrayResult() as $value) {
                $this->stringValues[$value['id']] = $value['value'];
            }
        }
    }

    /**
     * If text values cache is not set, initializes the cache.
     *
     * @param   Record                 $record
     * @param   EntityManagerInterface $manager
     */
    private function initTextValues(Record $record, EntityManagerInterface $manager)
    {
        if ($this->textValues === null) {

            $query = $manager->createQueryBuilder()
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
                'record'  => $record->getId(),
                'type'    => FieldType::TEXT,
                'current' => true,
            ]);

            foreach ($query->getQuery()->getArrayResult() as $value) {
                $this->textValues[$value['id']] = $value['value'];
            }
        }
    }

    /**
     * If list values cache is not set, initializes the cache.
     *
     * @param   Record                 $record
     * @param   EntityManagerInterface $manager
     */
    private function initListValues(Record $record, EntityManagerInterface $manager)
    {
        if ($this->listValues === null) {

            $query = $manager->createQueryBuilder()
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
                'record'  => $record->getId(),
                'type'    => FieldType::LIST,
                'current' => true,
            ]);

            foreach ($query->getQuery()->getArrayResult() as $value) {
                $this->listValues[$value['id']] = $value['text'];
            }
        }
    }
}
