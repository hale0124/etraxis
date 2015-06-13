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


namespace eTraxis\SimpleBus;

/**
 * Command trait to access command properties.
 */
trait CommandTrait
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
     * Sets value of specified property.
     *
     * @param   string $name  Name of the property.
     * @param   mixed  $value New value of the property.
     *
     * @throws  \Exception If the property doesn't exist.
     */
    public function __set($name, $value)
    {
        if (!property_exists($this, $name)) {
            throw new \Exception(sprintf('Class "%s" doesn\'t contain property "%s".', get_class($this), $name));
        }

        $this->$name = $value;
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
            throw new \Exception(sprintf('Class "%s" doesn\'t contain property "%s".', get_class($this), $name));
        }

        return $this->$name;
    }
}
