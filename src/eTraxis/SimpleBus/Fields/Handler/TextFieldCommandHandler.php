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
use eTraxis\SimpleBus\Fields\CreateTextFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateTextFieldCommand;

/**
 * Command handler.
 */
class TextFieldCommandHandler extends BaseFieldCommandHandler
{
    /**
     * Creates or updates "text" field.
     *
     * @param   CreateTextFieldCommand|UpdateTextFieldCommand $command
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

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

        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
