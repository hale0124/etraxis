<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users;

use SimpleBus\MessageTrait;
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
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\EntityId()
     */
    public $id;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Dictionary\Locale", "keys"})
     */
    public $locale;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Dictionary\Theme", "keys"})
     */
    public $theme;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Dictionary\Timezone", "keys"})
     */
    public $timezone;
}
