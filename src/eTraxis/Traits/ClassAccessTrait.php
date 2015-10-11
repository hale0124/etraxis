<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Traits;

/**
 * A trait to access protected methods and properties.
 */
trait ClassAccessTrait
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
     * @throws  \Exception If the property doesn't exist.
     *
     * @return  mixed Current value of the property.
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new \Exception(sprintf('Unknown property "%s" in class "%s".', $name, get_class($this)));
        }

        return $this->$name;
    }

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

    /**
     * Calls specified method.
     *
     * @param   string $name      Method name.
     * @param   array  $arguments List of arguments to be passed in the call.
     *
     * @throws  \Exception Unknown method.
     *
     * @return  mixed Value returned from the method, if any.
     */
    public function __call($name, array $arguments)
    {
        $reflection = new \ReflectionObject($this);

        if (!$reflection->hasMethod($name)) {
            throw new \Exception(sprintf('Unknown method "%s" in class "%s".', $name, get_class($this)));
        }

        $method = $reflection->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($this, $arguments);
    }
}
