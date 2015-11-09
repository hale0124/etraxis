<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Templates;

use eTraxis\Traits\ObjectInitiationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creates new template.
 *
 * @property    int    $project     ID of the template's project.
 * @property    string $name        Template name.
 * @property    string $prefix      Template prefix.
 * @property    string $description Description.
 * @property    int    $criticalAge Critical age.
 * @property    int    $frozenTime  Frozen time.
 * @property    bool   $guestAccess Whether to grant view access to anonymous.
 */
class CreateTemplateCommand
{
    use ObjectInitiationTrait;

    /**
     * @Assert\EntityId()
     */
    public $project = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "50")
     */
    public $name = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "3")
     */
    public $prefix = null;

    /**
     * @Assert\Length(max = "100")
     */
    public $description = null;

    /**
     * @Assert\Range(min = "1", max = "100")
     */
    public $criticalAge = null;

    /**
     * @Assert\Range(min = "1", max = "100")
     */
    public $frozenTime = null;

    /**
     * @Assert\NotNull()
     */
    public $guestAccess = null;
}
