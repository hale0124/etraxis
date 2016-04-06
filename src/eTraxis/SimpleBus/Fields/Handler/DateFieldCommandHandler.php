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
use eTraxis\SimpleBus\Fields\CreateDateFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateDateFieldCommand;
use SimpleBus\ValidationException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class DateFieldCommandHandler extends BaseFieldCommandHandler
{
    protected $translator;

    /**
     * Dependency Injection constructor.
     *
     * @param   ValidatorInterface  $validator
     * @param   RegistryInterface   $doctrine
     * @param   TranslatorInterface $translator
     */
    public function __construct(
        ValidatorInterface  $validator,
        RegistryInterface   $doctrine,
        TranslatorInterface $translator)
    {
        parent::__construct($validator, $doctrine);

        $this->translator = $translator;
    }

    /**
     * Creates or updates "date" field.
     *
     * @param   CreateDateFieldCommand|UpdateDateFieldCommand $command
     *
     * @throws  ValidationException
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        if ($command->minValue > $command->maxValue) {
            throw new ValidationException([$this->translator->trans('field.error.min_max_values')]);
        }

        if ($command->defaultValue !== null) {
            if ($command->defaultValue < $command->minValue || $command->defaultValue > $command->maxValue) {
                $error = $this->translator->trans('field.error.default_value', ['%min%' => $command->minValue, '%max%' => $command->maxValue]);
                throw new ValidationException([$error]);
            }
        }

        $entity->setType(Field::TYPE_DATE);

        $entity->asDate()
            ->setMinValue($command->minValue)
            ->setMaxValue($command->maxValue)
            ->setDefaultValue($command->defaultValue)
        ;

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
