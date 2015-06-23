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
 * A trait to write protected properties.
 */
trait SetTrait
{
    /**
     * Sets value of specified property.
     *
     * @param   string $name  Property name.
     * @param   mixed  $value Property value.
     *
     * @throws  \Exception Unknown property.
     */
    public function __set($name, $value)
    {
        if (!property_exists($this, $name)) {
            throw new \Exception(sprintf('Unknown property "%s" in class "%s".', $name, get_class($this)));
        }

        $this->$name = $value;
    }
}
