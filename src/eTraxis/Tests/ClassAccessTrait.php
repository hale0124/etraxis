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


namespace eTraxis\Tests;

/**
 * A simple trait to access protected methods and properties in unit tests.
 */
trait ClassAccessTrait
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

    /**
     * Returns current value of specified property.
     *
     * @param   string $name Property name.
     *
     * @return  mixed Property value.
     * @throws  \Exception Unknown property.
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new \Exception(sprintf('Unknown property "%s" in class "%s".', $name, get_class($this)));
        }

        return $this->$name;
    }

    /**
     * Calls specified method.
     *
     * @param   string $name      Method name.
     * @param   array  $arguments List of arguments to be passed in the call.
     *
     * @return  mixed Value returned from the method, if any.
     * @throws  \Exception Unknown method.
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
