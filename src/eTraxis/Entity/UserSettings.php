<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Doctrine\ORM\Mapping as ORM;
use eTraxis\Collection;

/**
 * User settings.
 *
 * @ORM\Embeddable
 */
class UserSettings implements \ArrayAccess
{
    /**
     * @var int Locale ID of user interface.
     *
     * @ORM\Column(name="locale", type="integer")
     */
    private $locale;

    /**
     * @var string Name of UI theme (e.g. "Emerald").
     *
     * @ORM\Column(name="theme_name", type="string", length=50)
     */
    private $theme;

    /**
     * @var int Timezone ID.
     *
     * @ORM\Column(name="timezone", type="integer")
     */
    private $timezone;

    /**
     * @var View Current view.
     *
     * @ORM\OneToOne(targetEntity="View")
     * @ORM\JoinColumn(name="view_id", referencedColumnName="view_id")
     */
    public $view;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->timezone = 0;
    }

    /**
     * Property setter.
     *
     * @param   string $locale
     *
     * @return  self
     */
    public function setLocale($locale)
    {
        $locales = array_flip(Collection\LegacyLocale::getCollection());

        if (array_key_exists($locale, $locales)) {
            $this->locale = $locales[$locale];
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getLocale()
    {
        $locales = Collection\LegacyLocale::getCollection();

        if (!array_key_exists($this->locale, $locales)) {
            $this->locale = 1000;
        }

        return $locales[$this->locale];
    }

    /**
     * Property setter.
     *
     * @param   string $theme
     *
     * @return  self
     */
    public function setTheme($theme)
    {
        if (in_array($theme, Collection\Theme::getAllKeys())) {
            $this->theme = $theme;
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getTheme()
    {
        $theme = strtolower($this->theme);

        if (!in_array($theme, Collection\Theme::getAllKeys())) {
            $theme = 'azure';
        }

        return $theme;
    }

    /**
     * Property setter.
     *
     * @param   int $timezone
     *
     * @return  self
     */
    public function setTimezone($timezone)
    {
        if (in_array($timezone, Collection\Timezone::getAllKeys())) {
            $this->timezone = $timezone;
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getTimezone()
    {
        if (!in_array($this->timezone, Collection\Timezone::getAllKeys())) {
            $this->timezone = 0;
        }

        return $this->timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $method = 'get' . ucfirst($offset);

        return $this->$method();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $method = 'set' . ucfirst($offset);

        $this->$method($value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->$offset = null;
    }
}
