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
 * A trait to call protected methods.
 */
trait CallTrait
{
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
