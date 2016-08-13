<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Templates;

use eTraxis\Traits\CommandTrait;
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
 */
class CreateTemplateCommand
{
    use CommandTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $project;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="50")
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="3")
     */
    public $prefix;

    /**
     * @Assert\Length(max="100")
     */
    public $description;

    /**
     * @Assert\Range(min="1", max="100")
     */
    public $criticalAge;

    /**
     * @Assert\Range(min="1", max="100")
     */
    public $frozenTime;
}
