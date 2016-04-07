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
 * Decimal value.
 *
 * @ORM\Table(name="tbl_float_values",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_float_values", columns={"float_value"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\DecimalValuesRepository")
 */
class DecimalValue
{
    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="value_id", type="integer")
     */
    private $id;

    /**
     * @var string Decimal value.
     *
     * @ORM\Column(name="float_value", type="decimal", precision=20, scale=10)
     */
    private $value;

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
     * @param   string $value
     *
     * @return  self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->value;
    }
}
