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

namespace eTraxis\Traits;

/**
 * A trait to extend DTO for Command Bus.
 */
trait CommandBusTrait
{
    /**
     * Initializes object properties with values from provided array.
     *
     * @param   array $values Initial values.
     */
    public function __construct($values = [])
    {
        foreach ($values as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }
}
