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

namespace eTraxis\CommandBus\Users;

use eTraxis\Traits\CommandBusTrait;
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
    use CommandBusTrait;

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
