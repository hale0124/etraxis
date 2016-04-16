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
use eTraxis\SimpleBus\Fields\CreateStringFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateStringFieldCommand;

/**
 * Command handler.
 */
class StringFieldCommandHandler extends BaseFieldCommandHandler
{
    /**
     * Creates or updates "string" field.
     *
     * @param   CreateStringFieldCommand|UpdateStringFieldCommand $command
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

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

        $this->manager->persist($entity);
    }
}
