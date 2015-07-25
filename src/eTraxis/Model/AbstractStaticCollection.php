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
    protected static $values = null;

    protected static function initCache()
    {
        if (static::$values === null) {
            static::$values = static::getCollection();
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getAllKeys()
    {
        static::initCache();

        return array_keys(static::$values);
    }

    /**
     * {@inheritDoc}
     */
    public static function getAllValues()
    {
        static::initCache();

        return array_values(static::$values);
    }

    /**
     * {@inheritDoc}
     */
    public static function getValue($key)
    {
        static::initCache();

        return array_key_exists($key, static::$values) ? static::$values[$key] : null;
    }
}
