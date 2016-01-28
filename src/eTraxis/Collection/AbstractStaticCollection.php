<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Collection;

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
     * {@inheritdoc}
     */
    public static function getAllKeys()
    {
        $collection = static::initCache();

        return array_keys($collection);
    }

    /**
     * {@inheritdoc}
     */
    public static function getAllValues()
    {
        $collection = static::initCache();

        return array_values($collection);
    }

    /**
     * {@inheritdoc}
     */
    public static function getValue($key)
    {
        $collection = static::initCache();

        return array_key_exists($key, $collection) ? $collection[$key] : null;
    }
}
