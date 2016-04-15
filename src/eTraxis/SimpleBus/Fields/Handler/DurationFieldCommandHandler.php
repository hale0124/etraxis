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
use eTraxis\SimpleBus\Fields\CreateDurationFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateDurationFieldCommand;
use SimpleBus\ValidationException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class DurationFieldCommandHandler extends BaseFieldCommandHandler
{
    protected $translator;

    /**
     * Dependency Injection constructor.
     *
     * @param   ValidatorInterface     $validator
     * @param   EntityManagerInterface $manager
     * @param   TranslatorInterface    $translator
     */
    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $manager,
        TranslatorInterface $translator)
    {
        parent::__construct($validator, $manager);

        $this->translator = $translator;
    }

    /**
     * Creates or updates "duration" field.
     *
     * @param   CreateDurationFieldCommand|UpdateDurationFieldCommand $command
     *
     * @throws  ValidationException
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        $minValue = $this->durationToInt($command->minValue);
        $maxValue = $this->durationToInt($command->maxValue);
        $default  = ($command->defaultValue === null) ? null : $this->durationToInt($command->defaultValue);

        if ($minValue > $maxValue) {
            throw new ValidationException([$this->translator->trans('field.error.min_max_values')]);
        }

        if ($default !== null) {
            if ($default < $minValue || $default > $maxValue) {
                $error = $this->translator->trans('field.error.default_value', [
                    '%min%' => $command->minValue, '%max%' => $command->maxValue,
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

        $this->manager->persist($entity);
        $this->manager->flush();
    }

    /**
     * Converts string with duration to integer value.
     *
     * @param   string $duration
     *
     * @return  int
     */
    protected function durationToInt($duration)
    {
        list($hh, $mm) = explode(':', $duration);

        return $hh * 60 + $mm;
    }
}
