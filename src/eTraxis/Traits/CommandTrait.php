<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Traits;

/**
 * A trait for CommandBus command object.
 */
trait CommandTrait
{
    /**
     * Initializes object properties with values from provided arrays.
     *
     * @param   array $values Initial values.
     * @param   array $extra  Optional extra values.
     *                        In case of keys conflicts this array overrides data from the first one.
     */
    public function __construct(array $values = [], array $extra = [])
    {
        /**
         * Replaces empty strings with nulls.
         *
         * @param   mixed $value A value to be updated. Can be an array.
         * @return  mixed Updated value.
         */
        $empty2null = function ($value) use (&$empty2null) {

            if (is_array($value)) {
                foreach ($value as &$v) {
                    $v = $empty2null($v);
                }

                return $value;
            }

            return is_string($value) && strlen($value) === 0 ? null : $value;
        };

        $data = $empty2null($extra + $values);

        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }
}
