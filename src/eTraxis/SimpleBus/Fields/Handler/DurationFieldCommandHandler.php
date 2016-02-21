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

use eTraxis\Entity\Field;
use eTraxis\SimpleBus\Fields\CreateDurationFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateDurationFieldCommand;
use eTraxis\SimpleBus\Middleware\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
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
     * @param   LoggerInterface     $logger
     * @param   ValidatorInterface  $validator
     * @param   RegistryInterface   $doctrine
     * @param   TranslatorInterface $translator
     */
    public function __construct(
        LoggerInterface     $logger,
        ValidatorInterface  $validator,
        RegistryInterface   $doctrine,
        TranslatorInterface $translator)
    {
        parent::__construct($logger, $validator, $doctrine);

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
        $default  = ($command->default === null) ? null : $this->durationToInt($command->default);

        if ($minValue > $maxValue) {
            $this->logger->error('Minimum valus is greater than maximum one.', [$command->minValue, $command->maxValue]);
            throw new ValidationException([$this->translator->trans('field.min_max_values')]);
        }

        if ($default !== null) {
            if ($default < $minValue || $default > $maxValue) {
                $error = $this->translator->trans('field.default_value', ['%min%' => $command->minValue, '%max%' => $command->maxValue]);
                $this->logger->error($error, [$command->default]);
                throw new ValidationException([$error]);
            }
        }

        $entity
            ->setType(Field::TYPE_DURATION)
            ->setParameter1($minValue)
            ->setParameter2($maxValue)
            ->setDefaultValue($default)
        ;

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
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
        list($min, $sec) = explode(':', $duration);

        return $min * 60 + $sec;
    }
}
