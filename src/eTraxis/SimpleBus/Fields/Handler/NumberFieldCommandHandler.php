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

/**
 * Command handler.
 */
class NumberFieldCommandHandler extends BaseFieldCommandHandler
{
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
            throw new ValidationException([$this->translator->trans('field.min_max_values')]);
        }

        if ($command->default !== null) {
            if ($command->default < $command->minValue || $command->default > $command->maxValue) {
                $error = $this->translator->trans('field.default_value', ['%min%' => $command->minValue, '%max%' => $command->maxValue]);
                $this->logger->error($error, [$command->default]);
                throw new ValidationException([$error]);
            }
        }

        $entity
            ->setType(Field::TYPE_NUMBER)
            ->setParameter1($command->minValue)
            ->setParameter2($command->maxValue)
            ->setDefaultValue($command->default)
        ;

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
