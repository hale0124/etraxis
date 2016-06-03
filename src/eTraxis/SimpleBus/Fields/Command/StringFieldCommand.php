<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields\Command;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Create/update command for "string" field.
 *
 * @property    int    $maxLength    Maximum allowed length of field values.
 * @property    string $defaultValue Default value of the field.
 * @property    string $pcreCheck    Perl-compatible regular expression which values of the field must conform to.
 * @property    string $pcreSearch   Perl-compatible regular expression to modify values of the field before display them (search for).
 * @property    string $pcreReplace  Perl-compatible regular expression to modify values of the field before display them (replace with).
 */
class StringFieldCommand extends FieldCommand
{
    /**
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="250")
     * @Assert\Regex("/^(\-|\+)?\d+$/")
     */
    public $maxLength;

    /**
     * @Assert\Length(max="250")
     */
    public $defaultValue;

    /**
     * @Assert\Length(max="500")
     */
    public $pcreCheck;

    /**
     * @Assert\Length(max="500")
     */
    public $pcreSearch;

    /**
     * @Assert\Length(max="500")
     */
    public $pcreReplace;
}
