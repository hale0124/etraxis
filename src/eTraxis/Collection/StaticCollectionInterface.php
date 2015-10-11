<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Collection;

/**
 * Interface of static collection of key/value pairs.
 */
interface StaticCollectionInterface
{
    /**
     * Returns whole collection as array with keys.
     *
     * @return  array
     */
    public static function getCollection();

    /**
     * Returns all keys of the collection.
     *
     * @return  array
     */
    public static function getAllKeys();

    /**
     * Returns all keys of the collection.
     *
     * @return  array
     */
    public static function getAllValues();

    /**
     * Returns collection value by specified key.
     *
     * @param   mixed $key
     *
     * @return  mixed
     */
    public static function getValue($key);
}
