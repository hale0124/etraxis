<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users;

use eTraxis\Traits\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Saves appearance settings of specified account.
 *
 * @property    int    $id       User ID.
 * @property    string $locale   New locale.
 * @property    string $theme    New theme.
 * @property    string $timezone New timezone.
 */
class SaveAppearanceCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $id;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback={"eTraxis\Dictionary\Locale", "keys"})
     */
    public $locale;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback={"eTraxis\Dictionary\Theme", "keys"})
     */
    public $theme;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback={"eTraxis\Dictionary\Timezone", "values"})
     */
    public $timezone;
}
