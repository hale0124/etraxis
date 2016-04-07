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
 * Subrecord.
 *
 * @ORM\Table(name="tbl_children")
 * @ORM\Entity
 */
class Child
{
    /**
     * @var Record Parent record.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="record_id")
     */
    private $parent;

    /**
     * @var Record Child record.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Record")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="record_id")
     */
    private $child;

    /**
     * @var int Whether the child is a dependency for the parent.
     *
     * @ORM\Column(name="is_dependency", type="integer")
     */
    private $isDependency;
}
