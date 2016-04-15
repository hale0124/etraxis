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
use eTraxis\SimpleBus\Fields\CreateRecordFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateRecordFieldCommand;

/**
 * Command handler.
 */
class RecordFieldCommandHandler extends BaseFieldCommandHandler
{
    /**
     * Creates or updates "record" field.
     *
     * @param   CreateRecordFieldCommand|UpdateRecordFieldCommand $command
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        $entity->setType(Field::TYPE_RECORD);

        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
