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
use eTraxis\SimpleBus\Fields\CreateDecimalFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateDecimalFieldCommand;
use SimpleBus\ValidationException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class DecimalFieldCommandHandler extends BaseFieldCommandHandler
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
        ValidatorInterface     $validator,
        EntityManagerInterface $manager,
        TranslatorInterface    $translator)
    {
        parent::__construct($validator, $manager);

        $this->translator = $translator;
    }

    /**
     * Creates or updates "decimal" field.
     *
     * @param   CreateDecimalFieldCommand|UpdateDecimalFieldCommand $command
     *
     * @throws  ValidationException
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        if (bccomp($command->minValue, $command->maxValue) > 0) {
            throw new ValidationException([$this->translator->trans('field.error.min_max_values')]);
        }

        if ($command->defaultValue !== null) {
            if (bccomp($command->defaultValue, $command->minValue) < 0 || bccomp($command->defaultValue, $command->maxValue) > 0) {
                $error = $this->translator->trans('field.error.default_value', [
                    '%min%' => $command->minValue, '%max%' => $command->maxValue,
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

        $this->manager->persist($entity);
    }
}
