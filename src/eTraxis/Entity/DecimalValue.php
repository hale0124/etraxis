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
 * @ORM\Table(name="decimal_values",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_decimal_values", columns={"value"})
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
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string Decimal value.
     *
     * @ORM\Column(name="value", type="decimal", precision=20, scale=10)
     */
    private $value;

    /**
     * Creates new decimal value.
     *
     * @param   string $value String representation of the value.
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->value;
    }
}
