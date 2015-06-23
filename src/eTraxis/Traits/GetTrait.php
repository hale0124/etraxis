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
 * A trait to read protected properties.
 */
trait GetTrait
{
    /**
     * Checks whether specified property exists.
     *
     * @param   string $name Name of the property.
     *
     * @return  bool TRUE if the property exists, FALSE otherwise.
     */
    public function __isset($name)
    {
        return property_exists($this, $name);
    }

    /**
     * Returns current value of specified property.
     *
     * @param   string $name Name of the property.
     *
     * @return  mixed Current value of the property.
     *
     * @throws  \Exception If the property doesn't exist.
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new \Exception(sprintf('Unknown property "%s" in class "%s".', $name, get_class($this)));
        }

        return $this->$name;
    }
}
