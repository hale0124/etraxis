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
use eTraxis\SimpleBus\Fields\CreateNumberFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateNumberFieldCommand;
use eTraxis\SimpleBus\Middleware\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class NumberFieldCommandHandler extends BaseFieldCommandHandler
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
     * Creates or updates "number" field.
     *
     * @param   CreateNumberFieldCommand|UpdateNumberFieldCommand $command
     *
     * @throws  ValidationException
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        if ($command->minValue > $command->maxValue) {
            $this->logger->error('Minimum valus is greater than maximum one.', [$command->minValue, $command->maxValue]);
            throw new ValidationException([$this->translator->trans('field.error.min_max_values')]);
        }

        if ($command->defaultValue !== null) {
            if ($command->defaultValue < $command->minValue || $command->defaultValue > $command->maxValue) {
                $error = $this->translator->trans('field.error.default_value', ['%min%' => $command->minValue, '%max%' => $command->maxValue]);
                $this->logger->error($error, [$command->defaultValue]);
                throw new ValidationException([$error]);
            }
        }

        $entity->setType(Field::TYPE_NUMBER);

        $entity->asNumber()
            ->setMinValue($command->minValue)
            ->setMaxValue($command->maxValue)
            ->setDefaultValue($command->defaultValue)
        ;

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
