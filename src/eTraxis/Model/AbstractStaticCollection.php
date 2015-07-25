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

namespace eTraxis\Model;

/**
 * Abstract static collection of key/value pairs.
 */
abstract class AbstractStaticCollection implements StaticCollectionInterface
{
    /** @var array Cached collection. */
    protected static $values = [];

    protected static function initCache()
    {
        $key = get_called_class();

        if (!array_key_exists($key, static::$values)) {
            static::$values[$key] = static::getCollection();
        }

        return static::$values[$key];
    }

    /**
     * {@inheritDoc}
     */
    public static function getAllKeys()
    {
        $collection = static::initCache();

        return array_keys($collection);
    }

    /**
     * {@inheritDoc}
     */
    public static function getAllValues()
    {
        $collection = static::initCache();

        return array_values($collection);
    }

    /**
     * {@inheritDoc}
     */
    public static function getValue($key)
    {
        $collection = static::initCache();

        return array_key_exists($key, $collection) ? $collection[$key] : null;
    }
}
