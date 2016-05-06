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

/**
 * Field embedded "regex" options.
 *
 * @ORM\Embeddable
 */
class FieldRegex
{
    // Constraints.
    const MAX_REGEX = 500;

    /**
     * @var string Perl-compatible regular expression which values of the field must conform to.
     *
     * @ORM\Column(name="check", type="string", length=500, nullable=true)
     */
    private $check;

    /**
     * @var string Perl-compatible regular expression to modify values of the field before display them (search for).
     *
     * @ORM\Column(name="search", type="string", length=500, nullable=true)
     */
    private $search;

    /**
     * @var string Perl-compatible regular expression to modify values of the field before display them (replace with).
     *
     * @ORM\Column(name="replace", type="string", length=500, nullable=true)
     */
    private $replace;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->check   = null;
        $this->search  = null;
        $this->replace = null;
    }

    /**
     * Property setter.
     *
     * @param   string|null $check
     *
     * @return  self
     */
    public function setCheck(string $check = null)
    {
        $this->check = $check;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string|null
     */
    public function getCheck()
    {
        return $this->check;
    }

    /**
     * Property setter.
     *
     * @param   string|null $search
     *
     * @return  self
     */
    public function setSearch(string $search = null)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string|null
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Property setter.
     *
     * @param   string|null $replace
     *
     * @return  self
     */
    public function setReplace(string $replace = null)
    {
        $this->replace = $replace;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string|null
     */
    public function getReplace()
    {
        return $this->replace;
    }
}
