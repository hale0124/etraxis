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
 * Updates specified template.
 *
 * @property    int    $id          Template ID.
 * @property    string $name        New name.
 * @property    string $prefix      New prefix.
 * @property    string $description New description.
 * @property    int    $criticalAge New critical age.
 * @property    int    $frozenTime  New frozen time.
 * @property    bool   $guestAccess Whether to grant view access to anonymous.
 */
class UpdateTemplateCommand
{
    use ObjectInitiationTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\EntityId()
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "50")
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "3")
     */
    public $prefix;

    /**
     * @Assert\Length(max = "100")
     */
    public $description;

    /**
     * @Assert\Any({
     *     @Assert\Blank(),
     *     @Assert\Range(min = "1", max = "100")
     * })
     */
    public $criticalAge;

    /**
     * @Assert\Any({
     *     @Assert\Blank(),
     *     @Assert\Range(min = "1", max = "100")
     * })
     */
    public $frozenTime;

    /**
     * @Assert\NotNull()
     */
    public $guestAccess;
}
