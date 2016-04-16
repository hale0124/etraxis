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
use eTraxis\SimpleBus\Fields\CreateCheckboxFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateCheckboxFieldCommand;

/**
 * Command handler.
 */
class CheckboxFieldCommandHandler extends BaseFieldCommandHandler
{
    /**
     * Creates or updates "checkbox" field.
     *
     * @param   CreateCheckboxFieldCommand|UpdateCheckboxFieldCommand $command
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        $entity->setType(Field::TYPE_CHECKBOX);

        $entity->asCheckbox()->setDefaultValue($command->defaultValue);

        $this->manager->persist($entity);
    }
}
