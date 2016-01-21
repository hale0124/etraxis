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
use eTraxis\SimpleBus\Fields\CreateIssueFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateIssueFieldCommand;

/**
 * Command handler.
 */
class IssueFieldCommandHandler extends BaseFieldCommandHandler
{
    /**
     * Creates or updates "issue" field.
     *
     * @param   CreateIssueFieldCommand|UpdateIssueFieldCommand $command
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        $entity
            ->setType(Field::TYPE_ISSUE)
            ->setParameter1(null)
            ->setParameter2(null)
            ->setDefaultValue(null)
        ;

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
