<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\Field;
use eTraxis\Entity\ListItem;
use eTraxis\SimpleBus\Fields\Command;
use SimpleBus\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Base command handler to create/update fields.
 */
abstract class FieldCommandHandler
{
    const HANDLERS = [
        Command\NumberFieldCommand::class   => 'handleNumber',
        Command\DecimalFieldCommand::class  => 'handleDecimal',
        Command\StringFieldCommand::class   => 'handleString',
        Command\TextFieldCommand::class     => 'handleText',
        Command\CheckboxFieldCommand::class => 'handleCheckbox',
        Command\ListFieldCommand::class     => 'handleList',
        Command\RecordFieldCommand::class   => 'handleRecord',
        Command\DateFieldCommand::class     => 'handleDate',
        Command\DurationFieldCommand::class => 'handleDuration',
    ];

    protected $validator;
    protected $manager;
    protected $translator;

    /**
     * Dependency Injection constructor.
     *
     * @param   ValidatorInterface     $validator
     * @param   EntityManagerInterface $manager
     * @param   TranslatorInterface    $translator
     */
    public function __construct(
        ValidatorInterface     $validator,
        EntityManagerInterface $manager,
        TranslatorInterface    $translator)
    {
        $this->validator  = $validator;
        $this->manager    = $manager;
        $this->translator = $translator;
    }

    /**
     * Handles specified command.
     *
     * @param   Command\FieldCommand $command
     */
    abstract public function handle(Command\FieldCommand $command);

    /**
     * Handles specified command for "number" field.
     *
     * @param   Field                      $entity
     * @param   Command\NumberFieldCommand $command
     *
     * @return  Field Updated field entity.
     *
     * @throws  ValidationException
     */
    protected function handleNumber(Field $entity, Command\NumberFieldCommand $command)
    {
        if ($command->minValue > $command->maxValue) {
            throw new ValidationException([$this->translator->trans('field.error.min_max_values')]);
        }

        if ($command->defaultValue !== null) {

            if ($command->defaultValue < $command->minValue || $command->defaultValue > $command->maxValue) {

                $error = $this->translator->trans('field.error.default_value', [
                    '%min%' => $command->minValue,
                    '%max%' => $command->maxValue,
                ]);

                throw new ValidationException([$error]);
            }
        }

        $entity->setType(Field::TYPE_NUMBER);

        $entity->asNumber()
            ->setMinValue($command->minValue)
            ->setMaxValue($command->maxValue)
            ->setDefaultValue($command->defaultValue)
        ;

        return $entity;
    }

    /**
     * Handles specified command for "decimal" field.
     *
     * @param   Field                       $entity
     * @param   Command\DecimalFieldCommand $command
     *
     * @return  Field Updated field entity.
     *
     * @throws  ValidationException
     */
    protected function handleDecimal(Field $entity, Command\DecimalFieldCommand $command)
    {
        if (bccomp($command->minValue, $command->maxValue) > 0) {
            throw new ValidationException([$this->translator->trans('field.error.min_max_values')]);
        }

        if ($command->defaultValue !== null) {

            if (bccomp($command->defaultValue, $command->minValue) < 0 || bccomp($command->defaultValue, $command->maxValue) > 0) {

                $error = $this->translator->trans('field.error.default_value', [
                    '%min%' => $command->minValue,
                    '%max%' => $command->maxValue,
                ]);

                throw new ValidationException([$error]);
            }
        }

        $entity->setType(Field::TYPE_DECIMAL);

        $entity->asDecimal()
            ->setMinValue($command->minValue)
            ->setMaxValue($command->maxValue)
            ->setDefaultValue($command->defaultValue)
        ;

        return $entity;
    }

    /**
     * Handles specified command for "string" field.
     *
     * @param   Field                      $entity
     * @param   Command\StringFieldCommand $command
     *
     * @return  Field Updated field entity.
     */
    protected function handleString(Field $entity, Command\StringFieldCommand $command)
    {
        $entity->setType(Field::TYPE_STRING);

        $entity->getRegex()
            ->setCheck($command->regexCheck)
            ->setSearch($command->regexSearch)
            ->setReplace($command->regexReplace)
        ;

        $entity->asString()
            ->setMaxLength($command->maxLength)
            ->setDefaultValue($command->defaultValue)
        ;

        return $entity;
    }

    /**
     * Handles specified command for "text" field.
     *
     * @param   Field                    $entity
     * @param   Command\TextFieldCommand $command
     *
     * @return  Field Updated field entity.
     */
    protected function handleText(Field $entity, Command\TextFieldCommand $command)
    {
        $entity->setType(Field::TYPE_TEXT);

        $entity->getRegex()
            ->setCheck($command->regexCheck)
            ->setSearch($command->regexSearch)
            ->setReplace($command->regexReplace)
        ;

        $entity->asText()
            ->setMaxLength($command->maxLength)
            ->setDefaultValue($command->defaultValue)
        ;

        return $entity;
    }

    /**
     * Handles specified command for "checkbox" field.
     *
     * @param   Field                        $entity
     * @param   Command\CheckboxFieldCommand $command
     *
     * @return  Field Updated field entity.
     */
    protected function handleCheckbox(Field $entity, Command\CheckboxFieldCommand $command)
    {
        $entity->setType(Field::TYPE_CHECKBOX);

        $entity->asCheckbox()->setDefaultValue($command->defaultValue);

        return $entity;
    }

    /**
     * Handles specified command for "list" field.
     *
     * @param   Field                    $entity
     * @param   Command\ListFieldCommand $command
     *
     * @return  Field Updated field entity.
     *
     * @throws  NotFoundHttpException
     */
    protected function handleList(Field $entity, Command\ListFieldCommand $command)
    {
        $entity->setType(Field::TYPE_LIST);

        if (in_array(Command\UpdateFieldCommandTrait::class, class_uses($command))) {

            /** @var \eTraxis\SimpleBus\Fields\UpdateListFieldCommand $command */
            if ($command->defaultValue !== null) {

                /** @var ListItem $item */
                $item = $this->manager->getRepository(ListItem::class)->findOneBy([
                    'field' => $entity,
                    'key'   => $command->defaultValue,
                ]);

                if (!$item) {
                    throw new NotFoundHttpException('Unknown list item.');
                }
            }

            $entity->getParameters()->setDefaultValue($command->defaultValue);
        }

        return $entity;
    }

    /**
     * Handles specified command for "record" field.
     *
     * @param   Field $entity
     *
     * @return  Field Updated field entity.
     */
    protected function handleRecord(Field $entity)
    {
        return $entity->setType(Field::TYPE_RECORD);
    }

    /**
     * Handles specified command for "date" field.
     *
     * @param   Field                    $entity
     * @param   Command\DateFieldCommand $command
     *
     * @return  Field Updated field entity.
     *
     * @throws  ValidationException
     */
    protected function handleDate(Field $entity, Command\DateFieldCommand $command)
    {
        if ($command->minValue > $command->maxValue) {
            throw new ValidationException([$this->translator->trans('field.error.min_max_values')]);
        }

        if ($command->defaultValue !== null) {

            if ($command->defaultValue < $command->minValue || $command->defaultValue > $command->maxValue) {

                $error = $this->translator->trans('field.error.default_value', [
                    '%min%' => $command->minValue,
                    '%max%' => $command->maxValue,
                ]);

                throw new ValidationException([$error]);
            }
        }

        $entity->setType(Field::TYPE_DATE);

        $entity->asDate()
            ->setMinValue($command->minValue)
            ->setMaxValue($command->maxValue)
            ->setDefaultValue($command->defaultValue)
        ;

        return $entity;
    }

    /**
     * Handles specified command for "duration" field.
     *
     * @param   Field                        $entity
     * @param   Command\DurationFieldCommand $command
     *
     * @return  Field Updated field entity.
     *
     * @throws  ValidationException
     */
    protected function handleDuration(Field $entity, Command\DurationFieldCommand $command)
    {
        /**
         * Converts string with duration to integer value.
         *
         * @param   string $duration
         * @return  int
         */
        $durationToInt = function ($duration)
        {
            list($hh, $mm) = explode(':', $duration);

            return $hh * 60 + $mm;
        };

        $minValue = $durationToInt($command->minValue);
        $maxValue = $durationToInt($command->maxValue);

        if ($minValue > $maxValue) {
            throw new ValidationException([$this->translator->trans('field.error.min_max_values')]);
        }

        if ($command->defaultValue !== null) {

            $default = $durationToInt($command->defaultValue);

            if ($default < $minValue || $default > $maxValue) {

                $error = $this->translator->trans('field.error.default_value', [
                    '%min%' => $command->minValue,
                    '%max%' => $command->maxValue,
                ]);

                throw new ValidationException([$error]);
            }
        }

        $entity->setType(Field::TYPE_DURATION);

        $entity->asDuration()
            ->setMinValue($command->minValue)
            ->setMaxValue($command->maxValue)
            ->setDefaultValue($command->defaultValue)
        ;

        return $entity;
    }
}
