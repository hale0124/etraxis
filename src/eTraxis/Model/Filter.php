<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Filter.
 *
 * @ORM\Table(name="tbl_filters",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_filters", columns={"account_id", "filter_name"})
 *            })
 * @ORM\Entity
 */
class Filter
{
    // Filter type.
    const TYPE_ALL_PROJECTS  = 1;
    const TYPE_ALL_TEMPLATES = 2;
    const TYPE_ALL_STATES    = 3;
    const TYPE_SEL_STATES    = 4;

    // Filter flag.
    const FLAG_CREATED_BY  = 0x0001;
    const FLAG_ASSIGNED_TO = 0x0002;
    const FLAG_UNCLOSED    = 0x0004;
    const FLAG_POSTPONED   = 0x0008;
    const FLAG_ACTIVE      = 0x0010;
    const FLAG_UNASSIGNED  = 0x0020;

    /**
     * @var int Unique ID.
     *
     * @ORM\Column(name="filter_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int Owner of the filter.
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $userId;

    /**
     * @var string Name of the filter.
     *
     * @ORM\Column(name="filter_name", type="string", length=50)
     */
    private $name;

    /**
     * @var int Filter scope (type).
     *
     *          FILTER_TYPE_ALL_PROJECTS  - all projects
     *          FILTER_TYPE_ALL_TEMPLATES - all templates of specified project
     *          FILTER_TYPE_ALL_STATES    - all states of specified template
     *          FILTER_TYPE_SEL_STATES    - selected states of specified template
     *
     * @ORM\Column(name="filter_type", type="integer")
     */
    private $type;

    /**
     * @var int Filter flags.
     *
     *          FILTER_FLAG_CREATED_BY  - filter shows only issues created by specified users
     *          FILTER_FLAG_ASSIGNED_TO - filter shows only issues assigned on specified user
     *          FILTER_FLAG_UNCLOSED    - filter shows only opened issues
     *          FILTER_FLAG_POSTPONED   - filter shows only postponed issues
     *          FILTER_FLAG_ACTIVE      - filter shows only active (not postponed) issues
     *          FILTER_FLAG_UNASSIGNED  - filter shows only unassigned issues
     *
     * @ORM\Column(name="filter_flags", type="integer")
     */
    private $flags;

    /**
     * @var int Parameter of the filter. Depends on filter type as following:
     *
     *          FILTER_TYPE_ALL_PROJECTS  - NULL (not used)
     *          FILTER_TYPE_ALL_TEMPLATES - ID of the project
     *          FILTER_TYPE_ALL_STATES    - ID of the template
     *          FILTER_TYPE_SEL_STATES    - ID of the template
     *
     * @ORM\Column(name="filter_param", type="integer", nullable=true)
     */
    private $parameter;

    /**
     * @var User Owner of the filter.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="account_id")
     */
    private $user;

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Standard setter.
     *
     * @param   int $userId
     *
     * @return  self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Standard setter.
     *
     * @param   string $name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Standard setter.
     *
     * @param   int $type
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Standard setter.
     *
     * @param   int $flags
     *
     * @return  self
     */
    public function setFlags($flags)
    {
        $this->flags = $flags;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Standard setter.
     *
     * @param   int $parameter
     *
     * @return  self
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Standard setter.
     *
     * @param   User $user
     *
     * @return  self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  User
     */
    public function getUser()
    {
        return $this->user;
    }
}
