<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creates new "list" field.
 *
 * @property    array $items   List items.
 * @property    int   $default Default value of the field.
 */
class CreateListFieldCommand extends CreateFieldBaseCommand
{
    /**
     * @Assert\NotNull()
     * @Assert\Type(type = "array")
     * @Assert\Count(min = "1", max = "1000")
     * @Assert\All({
     *     @Assert\Collection(
     *         fields = {
     *             "index" = {
     *                 @Assert\NotBlank()
     *                 @Assert\Type(type = "int")
     *                 @Assert\GreaterThan(value = "0")
     *             },
     *             "value" = {
     *                 @Assert\NotBlank()
     *                 @Assert\Length(max = "50")
     *             }
     *         },
     *         allowExtraFields   = false,
     *         allowMissingFields = false
     *     )
     * })
     */
    public $items = [];

    /**
     * @Assert\Type(type = "int")
     * @Assert\GreaterThan(value = "0")
     */
    public $default;
}
