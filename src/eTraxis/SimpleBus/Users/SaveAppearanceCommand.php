<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users;

use eTraxis\Traits\ObjectInitiationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Saves appearance settings of specified account.
 *
 * @property    int    $id       User ID.
 * @property    string $locale   New locale.
 * @property    string $theme    New theme.
 * @property    int    $timezone New timezone.
 */
class SaveAppearanceCommand
{
    use ObjectInitiationTrait;

    /**
     * @Assert\NotBlank()
     * @eTraxis\Validator\EntityIdConstraint()
     */
    public $id;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Collection\Locale", "getAllKeys"})
     */
    public $locale = null;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Collection\Theme", "getAllKeys"})
     */
    public $theme = null;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Collection\Timezone", "getAllKeys"})
     */
    public $timezone = null;
}