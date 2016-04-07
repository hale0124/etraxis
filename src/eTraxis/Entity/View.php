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
 * View.
 *
 * @ORM\Table(name="tbl_views",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_views", columns={"account_id", "view_name"})
 *            })
 * @ORM\Entity
 */
class View
{
    // Constraints.
    const MAX_NAME = 50;

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="view_id", type="integer")
     */
    private $id;

    /**
     * @var User Owner of the view.
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="account_id", nullable=false, referencedColumnName="account_id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string Name of the view.
     *
     * @ORM\Column(name="view_name", type="string", length=50)
     */
    private $name;
}
