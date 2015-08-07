<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups;

use eTraxis\Traits\CommandBusTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creates new group.
 *
 * Returns ID of the created group.
 *
 * @property    int    $project     ID of the group's project (empty for global group).
 * @property    string $name        Group name.
 * @property    string $description Description.
 */
class CreateGroupCommand
{
    use CommandBusTrait;

    /**
     * @eTraxis\Validator\EntityIdConstraint()
     */
    public $project = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "25")
     */
    public $name = null;

    /**
     * @Assert\Length(max = "100")
     */
    public $description = null;
}
